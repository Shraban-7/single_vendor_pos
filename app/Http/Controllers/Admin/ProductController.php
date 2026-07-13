<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Size;
use App\Models\StockLog;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'brand']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'low_stock':
                    $query->whereColumn('stock_in', '<=', 'low_stock_threshold')
                        ->where('stock_in', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_in', '<=', 0);
                    break;
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest('id');
                break;
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(15)->appends($request->query());

        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    private function getCategories()
    {
        $categories = Category::category()
            ->active()
            ->orderBy('name')
            ->with([
                'children' => function ($query) {
                    $query->orderBy('name');
                },
                'children.children' => function ($query) {
                    $query->orderBy('name');
                }
            ])
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'children' => $child->children->map(function ($subChild) {
                            return [
                                'id' => $subChild->id,
                                'name' => $subChild->name,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();
    }


    public function create()
    {
        $categories = $this->getCategories();
        $brands = Brand::active()->orderBy('name')->get();
        $sizes = Size::orderBy('sort_order')->get();
        $colors = Color::orderBy('name')->get();
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        return view('admin.products.create', compact(
            'categories',
            'brands',
            'sizes',
            'colors',
            'fitTypes',
            'patterns',
            'occasions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'sub_subcategory_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'material' => 'nullable|string|max:255',
            'fit_type' => 'nullable|string|in:' . implode(',', FitType::values()),
            'pattern' => 'nullable|string|in:' . implode(',', Pattern::values()),
            'occasion' => 'nullable|string|in:' . implode(',', Occasion::values()),
            'stock_in' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_on_sale' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        DB::beginTransaction();
        $imgPath = null;
        $galleryPaths = [];

        $imageService = new ImageOptimizerService;

        try {
            $validated['name'] = strtoupper($request->name);
            $validated['slug'] = Str::slug($validated['name']);

            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            if (empty($validated['sku'])) {
                $validated['sku'] = Product::generate_sku();
            }

            // Convert tags string to array
            if (!empty($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            }

            // Handle boolean fields (checkboxes not sent when unchecked)
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');
            $validated['is_new_arrival'] = $request->has('is_new_arrival');
            $validated['is_best_seller'] = $request->has('is_best_seller');
            $validated['is_on_sale'] = $request->has('is_on_sale');

            if ($request->hasFile('image')) {
                $validated['image'] = $imageService->uploadAndOptimize($request->file('image'), 'products/thumbnails');
            }

            $product = Product::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $imageService->uploadAndOptimize($image, 'products');
                    $galleryPaths[] = $path;

                    $product->images()->create([
                        'image_path' => $path,
                    ]);
                }
            }

            if ($product->stock_in > 0) {
                StockLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $product->stock_in,
                    'stock_before' => 0,
                    'stock_after' => $product->stock_in,
                    'note' => $validated['note'] ?? null,
                ]);

                activity_log(
                    action: 'updated',
                    model: $product,
                    description: 'Product stock updated ',
                );
            }


            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'redirect' => route('admin.products.index'),
                    'product' => $product->load('images')
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($imgPath) {
                delete_file($imgPath);
            }

            foreach ($galleryPaths as $path) {
                delete_file($path);
            }
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $categories = $this->getCategories();
        $brands = Brand::active()->orderBy('name')->get();
        $sizes = Size::orderBy('sort_order')->get();
        $colors = Color::orderBy('name')->get();
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        $product->load(['images', 'variants.size', 'variants.color']);

        return view('admin.products.edit', compact(
            'product',
            'brands',
            'categories',
            'sizes',
            'colors',
            'fitTypes',
            'patterns',
            'occasions'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'sub_subcategory_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'material' => 'nullable|string|max:255',
            'fit_type' => 'nullable|string|in:' . implode(',', FitType::values()),
            'pattern' => 'nullable|string|in:' . implode(',', Pattern::values()),
            'occasion' => 'nullable|string|in:' . implode(',', Occasion::values()),
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_on_sale' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'delete_images' => 'nullable|string',
            'delete_images.*' => 'exists:product_images,id',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.sku' => 'nullable|string|max:255',
            //'variants.*.stock_in' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric',
            'delete_variants' => 'nullable|array',
            'delete_variants.*' => 'nullable|integer|exists:product_variants,id',
        ]);

        $imageService = new ImageOptimizerService;

        DB::beginTransaction();

        try {
            $validated['name'] = strtoupper($request->name);
            if ($validated['name'] !== $product->name) {
                $validated['slug'] = Str::slug($validated['name']);

                $originalSlug = $validated['slug'];
                $counter = 1;
                while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            if (!empty($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            }

            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');
            $validated['is_new_arrival'] = $request->has('is_new_arrival');
            $validated['is_best_seller'] = $request->has('is_best_seller');
            $validated['is_on_sale'] = $request->has('is_on_sale');

            if ($request->hasFile('image')) {
                if ($product->image) {
                    delete_file($product->image);
                }

                $validated['image'] = $imageService->uploadAndOptimize($request->file('image'), 'products/thumbnails');
            }

            $product->update($validated);

            if ($request->filled('delete_images')) {
                $deleteImageIds = json_decode($request->delete_images, true);
                if (is_array($deleteImageIds)) {
                    foreach ($deleteImageIds as $imageId) {
                        $image = $product->images()->find($imageId);
                        if ($image) {
                            try {
                                delete_file($image->image_path);
                                $image->delete();
                            } catch (\Exception $e) {
                                // Log error or ignore if delete fails
                            }
                        }
                    }
                }
            }

            if ($request->hasFile('images')) {
                // Check if total images (existing - deleted + new) <= 5
                $currentImageCount = $product->images()->count();

                $deleteImageIds = $request->filled('delete_images')
                    ? json_decode($request->delete_images, true)
                    : [];

                $deletedCount = is_array($deleteImageIds) ? count($deleteImageIds) : 0;

                $newImages = $request->file('images') ?? [];
                $newCount = is_array($newImages) ? count($newImages) : 0;

                if (($currentImageCount - $deletedCount + $newCount) <= 5) {
                    foreach ($request->file('images') as $image) {
                        $path = $imageService->uploadAndOptimize($image, 'products');
                        $product->images()->create([
                            'image_path' => $path,
                        ]);
                    }
                } else {
                }
            }

            $variants = $request->variants ?? [];

            $variantIds = [];
            $combinations = [];
            $skus = [];

            foreach ($variants as $variantData) {

                if (empty($variantData['size_id']) && empty($variantData['color_id'])) {
                    continue;
                }

                $sizeId = $variantData['size_id'] ?? null;
                $colorId = $variantData['color_id'] ?? null;

                $combinationKey = $sizeId . '-' . $colorId;

                if (in_array($combinationKey, $combinations)) {
                    throw new \Exception('Duplicate variant found.');
                }

                $combinations[] = $combinationKey;

                $variantSku = $variantData['sku'] ?? ProductVariant::generate_sku();

                if (in_array($variantSku, $skus)) {
                    throw new \Exception('Duplicate variant SKU found: ' . $variantSku);
                }

                $skus[] = $variantSku;

                $skuExists = ProductVariant::query()
                    ->where('sku', $variantSku)
                    ->when(
                        !empty($variantData['id']),
                        fn($q) => $q->where('id', '!=', $variantData['id'])
                    )
                    ->exists();

                if ($skuExists) {
                    throw new \Exception('Variant SKU already exists: ' . $variantSku);
                }

                $variantData['product_id'] = $product->id;
                $variantData['stock_in'] = $variantData['stock_in'] ?? 0;
                $variantData['price'] = $variantData['price'] ?? $product->price;
                $variantData['sku'] = $variantSku;

                if (!empty($variantData['id'])) {

                    $variant = $product->variants()->find($variantData['id']);

                    if ($variant) {

                        $variantData['stock_in'] = $variantData['stock_in'] == 0
                            ? $variant->stock_in
                            : $variantData['stock_in'];

                        $variant->update($variantData);

                        $variantIds[] = $variant->id;
                    }

                } else {

                    $newVariant = $product->variants()->create($variantData);

                    $variantIds[] = $newVariant->id;
                }
            }

            if (!empty($variantIds)) {

                $product->variants()
                    ->whereNotIn('id', $variantIds)
                    ->delete();

            } else {

                $product->variants()->delete();
            }

            if (!empty($variantIds)) {
                $product->variants()
                    ->whereNotIn('id', $variantIds)
                    ->delete();
            } else {
                $product->variants()->delete();
            }

            activity_log(
                action: 'updated',
                model: $product,
                description: 'Product updated ',
            );

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully!',
                    'redirect' => route('admin.products.index'),
                    'product' => $product->load('images')
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->orderItems()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'This product cannot be deleted because it is associated with orders.');
            }

            foreach ($product->images as $image) {
                delete_file($image->image_path);
            }

            $product->variants()->delete();

            activity_log(
                action: 'deleted',
                model: $product,
                description: 'Product deleted ',
            );

            $product->sku = $product->sku . '_deleted';

            $product->save();

            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function manageStock(Product $product)
    {
        $product->load(['variants.size', 'variants.color', 'category']);

        return view('admin.products.manage-stock', compact('product'));
    }

    public function stockHistory(Product $product)
    {
        $product->load(['variants.size', 'variants.color', 'category']);

        $stockLogs = StockLog::where(function ($query) use ($product) {
            $query->where('product_id', $product->id)
                ->orWhereIn('product_variant_id', $product->variants->pluck('id'));
        })
            ->with(['user', 'product', 'productVariant.size', 'productVariant.color'])
            ->latest()
            ->paginate(20);

        return view('admin.products.stock-history', compact('product', 'stockLogs'));
    }

    public function addStock(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'nullable|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            if (empty($validated['product_id']) && empty($validated['variant_id'])) {
                throw new \Exception('Either product or variant must be specified');
            }

            DB::beginTransaction();

            $stockBefore = 0;
            $stockAfter = 0;

            if (isset($validated['variant_id'])) {
                // Add stock to variant
                $variant = ProductVariant::findOrFail($validated['variant_id']);
                $stockBefore = $variant->currentStock;
                $stockAfter = $stockBefore + $validated['quantity'];
                $variant->increment('stock_in', $validated['quantity']);
                $variant->product->increment('stock_in', $validated['quantity']);

                StockLog::create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $validated['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => $validated['note'] ?? null,
                ]);

                activity_log(
                    action: 'updated',
                    model: $variant,
                    description: 'Product stock updated ',
                );
            } else {
                $product = Product::findOrFail($validated['product_id']);
                $stockBefore = $product->currentStock;
                $stockAfter = $stockBefore + $validated['quantity'];
                $product->increment('stock_in', $validated['quantity']);

                StockLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $validated['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => $validated['note'] ?? null,
                ]);

                activity_log(
                    action: 'updated',
                    model: $product,
                    description: 'Product stock updated ',
                );
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock added successfully!',
                    'stock_after' => $stockAfter,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Stock added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add stock: ' . $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }

    public function removeStock(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'nullable|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            if (empty($validated['product_id']) && empty($validated['variant_id'])) {
                throw new \Exception('Product or variant is required');
            }

            DB::beginTransaction();

            if (!empty($validated['variant_id'])) {
                $variant = ProductVariant::lockForUpdate()
                    ->findOrFail($validated['variant_id']);

                $stockBefore = $variant->currentStock;

                if ($stockBefore < $validated['quantity']) {
                    throw new \Exception('Insufficient stock for this variant');
                }

                $variant->increment('stock_out', $validated['quantity']);
                $variant->product->increment('stock_out', $validated['quantity']);

                $stockAfter = $stockBefore - $validated['quantity'];

                StockLog::create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $validated['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => $validated['note'] ?? null,
                ]);

            } else {

                $product = Product::lockForUpdate()
                    ->findOrFail($validated['product_id']);

                $stockBefore = $product->currentStock;

                if ($stockBefore < $validated['quantity']) {
                    throw new \Exception('Insufficient stock for this product');
                }

                $product->increment('stock_out', $validated['quantity']);

                $stockAfter = $stockBefore - $validated['quantity'];

                StockLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $validated['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => $validated['note'] ?? null,
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock removed successfully!',
                    'stock_after' => $stockAfter,
                ]);
            }

            return back()->with('success', 'Stock removed successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function printBarcode(Request $request)
    {
        $products = Product::with(['variants.size', 'variants.color'])
            ->get();

        $siteName = Setting::where('key', 'site_name')->value('value');

        return view('admin.barcodes.index', compact('products', 'siteName'));
    }

    public function printBarcodeLabels(Request $request)
    {
        $request->validate([
            'sku' => 'required',
            'quantity' => 'required|numeric'
        ]);

        $variant = ProductVariant::where('sku', $request->sku)->first();

        $siteName = Setting::where('key', 'site_name')->value('value');

        if ($variant) {
            $price = $variant->finalPrice;

            $data = [
                'sellerName' => $siteName,
                'productName' => $variant->product->name,
                'variantName' => $variant->variantName,
                'sku' => $variant->sku,
                'price' => money($price),
                'quantity' => $request->quantity,
            ];

            return view('admin.barcodes.print_new', compact('data'));
        }

        $product = Product::where('sku', $request->sku)->first();
        if ($product) {
            $data = [
                'sellerName' => $siteName,
                'productName' => $product->name,
                'variantName' => '',
                'sku' => $product->sku,
                'price' => money($product->selling_price),
                'quantity' => $request->quantity,
            ];

            return view('admin.barcodes.print_new', compact('data'));
        }

        return redirect()->route('admin.products.printBarcode')->with('error', 'Product not found!');
    }

    public function updateCategory()
    {
        $products = Product::whereNull('category_id')
            ->select(
                'id',
                'name',
                'image',
                'category_id',
                'subcategory_id',
                'sub_subcategory_id'
            )
            ->latest()
            ->get();

        $categories = Category::whereNull('parent_id')
            ->with('children.children')
            ->get();

        return view('products.update_category', compact('products', 'categories'));
    }

    public function setCategory($product_id, Request $request)
    {
        $categoryId = $request->input('category_id');
        $subcategory_id = $request->input('subcategory_id');
        $sub_subcategory_id = $request->input('sub_subcategory_id');

        $product = Product::findOrFail($product_id);
        $product->category_id = $categoryId;
        $product->subcategory_id = $subcategory_id;
        $product->sub_subcategory_id = $sub_subcategory_id;
        $product->save();

        return redirect()->back()->with('success', 'Product category updated successfully.');
    }
}
