<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
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
        $query = Product::with(['category', 'images']);

        // Search by name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
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

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
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
        $categories = Category::category()->active()->orderBy('name')->with('children')->get();

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                    ];
                }),
            ];
        }

        return $data;
    }

    public function create()
    {
        $categories = $this->getCategories();
        $sizes = Size::orderBy('sort_order')->get();
        $colors = Color::orderBy('name')->get();
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        return view('admin.products.create', compact(
            'categories',
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
            'brand' => 'nullable|string|max:255',
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
        $sizes = Size::orderBy('sort_order')->get();
        $colors = Color::orderBy('name')->get();
        $fitTypes = FitType::cases();
        $patterns = Pattern::cases();
        $occasions = Occasion::cases();

        $product->load(['images', 'variants.size', 'variants.color']);

        return view('admin.products.edit', compact(
            'product',
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
            'brand' => 'nullable|string|max:255',
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
                $deletedCount = $request->has('delete_images') ? count($request->delete_images) : 0;
                $newCount = count($request->file('images'));

                if (($currentImageCount - $deletedCount + $newCount) <= 5) {
                    foreach ($request->file('images') as $image) {
                        $path = $imageService->uploadAndOptimize($image, 'products');
                        $product->images()->create([
                            'image_path' => $path,
                        ]);
                    }
                } else {
                    // Optionally handle error, throw exception, or skip excess images
                }
            }

            if ($request->has('delete_variants')) {
                $product->variants()->whereIn('id', $request->delete_variants)->delete();
            }

            if ($request->has('variants')) {

                $variantIds = [];

                foreach ($request->variants as $variantData) {
                    if (empty($variantData['size_id']) && empty($variantData['color_id'])) {
                        continue;
                    }

                    if (!empty($variantData['id'])) {
                        $variantIds[] = $variantData['id'];
                    }

                    $variantData['product_id'] = $product->id;
                    $variantData['stock_in'] = $variantData['stock_in'] ?? 0;
                    $variantData['price'] = $variantData['price'] ?? $product->price;
                    $variantData['sku'] = $variantData['sku'] ?? ProductVariant::generate_sku();

                    if (!empty($variantData['id'])) {
                        $variant = $product->variants()->find($variantData['id']);
                        if ($variant) {
                            $variantData['stock_in'] = $variantData['stock_in'] == 0 ? $variant->stock_in : $variantData['stock_in'];
                            $variant->update($variantData);
                        }
                    } else {
                        $product->variants()->create($variantData);
                    }

                    if (count($variantIds) > 0) {
                        $product->variants()->whereNotIn('id', $variantIds)->delete();
                    }
                }
            }

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

            foreach ($product->images as $image) {
                delete_file($image->image_path);
            }

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
                throw new \Exception('Either product or variant must be specified');
            }

            DB::beginTransaction();

            $stockBefore = 0;
            $stockAfter = 0;

            if (isset($validated['variant_id'])) {
                $variant = ProductVariant::findOrFail($validated['variant_id']);
                $stockBefore = $variant->currentStock;

                if ($stockBefore < $validated['quantity']) {
                    throw new \Exception('Cannot remove more stock than available');
                }

                $stockAfter = $stockBefore - $validated['quantity'];
                $variant->decrement('stock_in', $validated['quantity']);

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
                $product = Product::findOrFail($validated['product_id']);
                $stockBefore = $product->currentStock;

                if ($stockBefore < $validated['quantity']) {
                    throw new \Exception('Cannot remove more stock than available');
                }

                $stockAfter = $stockBefore - $validated['quantity'];
                $product->decrement('stock_in', $validated['quantity']);

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

            return redirect()
                ->back()
                ->with('success', 'Stock removed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove stock: ' . $e->getTraceAsString()
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to remove stock: ' . $e->getMessage());
        }
    }
}
