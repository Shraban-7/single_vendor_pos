<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
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
        $query = Product::with(['category', 'images']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
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

        return view('admin.products.index', compact('products', 'categories'));
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
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        return view('admin.products.create', compact(
            'categories',
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

            if (!empty($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            }

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
                foreach ($request->file('images') as $image) {
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
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        $product->load(['images']);

        return view('admin.products.edit', compact(
            'product',
            'categories',
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
                }
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
        $product->load(['category']);
        return view('admin.products.manage-stock', compact('product'));
    }

    public function stockHistory(Product $product)
    {
        $product->load(['category']);

        $stockLogs = StockLog::where('product_id', $product->id)
            ->with(['user', 'product'])
            ->latest()
            ->paginate(20);

        return view('admin.products.stock-history', compact('product', 'stockLogs'));
    }

    public function addStock(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

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
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
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
        $products = Product::get();
        $siteName = Setting::where('key', 'site_name')->value('value');

        return view('admin.barcodes.index', compact('products', 'siteName'));
    }

    public function printBarcodeLabels(Request $request)
    {
        $request->validate([
            'sku' => 'required',
            'quantity' => 'required|numeric'
        ]);

        $siteName = Setting::where('key', 'site_name')->value('value');
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
