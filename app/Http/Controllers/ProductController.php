<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'brand')
            ->where('is_active', true);

        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER (3 LEVEL SUPPORT)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('categories')) {

            $categorySlugs = is_array($request->categories)
                ? $request->categories
                : explode(',', $request->categories);

            $selectedCategories = Category::whereIn('slug', $categorySlugs)
                ->get(['id']);

            $categoryIds = [];

            foreach ($selectedCategories as $category) {

                // include selected category
                $categoryIds[] = $category->id;

                // level 2
                $children = Category::where('parent_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                $categoryIds = array_merge($categoryIds, $children);

                // level 3
                if (!empty($children)) {
                    $grandChildren = Category::whereIn('parent_id', $children)
                        ->pluck('id')
                        ->toArray();

                    $categoryIds = array_merge($categoryIds, $grandChildren);
                }
            }

            $categoryIds = array_unique($categoryIds);

            $query->where(function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                    ->orWhereIn('subcategory_id', $categoryIds)
                    ->orWhereIn('sub_subcategory_id', $categoryIds);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | BRAND FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('brands')) {

            $brandSlugs = is_array($request->brands)
                ? $request->brands
                : explode(',', $request->brands);

            $brandIds = Brand::whereIn('slug', $brandSlugs)
                ->pluck('id')
                ->toArray();

            if (!empty($brandIds)) {
                $query->whereIn('brand_id', $brandIds);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRICE FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        /*
        |--------------------------------------------------------------------------
        | SIZE FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('sizes')) {

            $sizeIds = is_array($request->sizes)
                ? $request->sizes
                : explode(',', $request->sizes);

            $query->whereHas('variants', function ($q) use ($sizeIds) {
                $q->whereIn('size_id', $sizeIds);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | COLOR FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('colors')) {

            $colorIds = is_array($request->colors)
                ? $request->colors
                : explode(',', $request->colors);

            $query->whereHas('variants', function ($q) use ($colorIds) {
                $q->whereIn('color_id', $colorIds);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | RATING FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        /*
        |--------------------------------------------------------------------------
        | SPECIAL FILTERS
        |--------------------------------------------------------------------------
        */
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'new-arrivals':
                    $query->where('is_new_arrival', true);
                    break;

                case 'best-sellers':
                    $query->where('is_best_seller', true);
                    break;

                case 'featured':
                    $query->where('is_featured', true);
                    break;

                case 'on-sale':
                    $query->where('is_on_sale', true);
                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('tags', 'like', "%{$searchTerm}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */
        switch ($request->get('sort', 'featured')) {

            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'best_selling':
                $query->where('is_best_seller', true)
                    ->orderBy('review_count', 'desc');
                break;

            case 'top_rated':
                $query->orderBy('average_rating', 'desc')
                    ->orderBy('review_count', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            default:
                $query->orderBy('is_featured', 'desc')
                    ->orderBy('is_new_arrival', 'desc')
                    ->orderBy('review_count', 'desc');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $totalProducts = (clone $query)->count();
        $perPage = $request->get('per_page', 24);

        $products = $query->simplePaginate($perPage)
            ->appends($request->query());

        $categories = Category::category()
            ->with([
                'children' => function ($query) {
                    $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                },
                'children.children' => function ($query) {
                    $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                }
            ])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $sizes = Size::orderBy('sort_order')->get();
        $colors = Color::orderBy('name')->get();

        $brands = Brand::active()
            ->orderBy('name')
            ->withCount('products')
            ->get();

        $counts = Product::where('is_active', true)
            ->selectRaw('category_id as id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'id')
            ->toArray();

        $subCounts = Product::where('is_active', true)
            ->selectRaw('subcategory_id as id, COUNT(*) as total')
            ->whereNotNull('subcategory_id')
            ->groupBy('subcategory_id')
            ->pluck('total', 'id')
            ->toArray();

        $subSubCounts = Product::where('is_active', true)
            ->selectRaw('sub_subcategory_id as id, COUNT(*) as total')
            ->whereNotNull('sub_subcategory_id')
            ->groupBy('sub_subcategory_id')
            ->pluck('total', 'id')
            ->toArray();

        $categoryCounts = [];

        foreach ($counts as $id => $count) {
            $categoryCounts[$id] = $count;
        }

        foreach ($subCounts as $id => $count) {
            $categoryCounts[$id] = $count;
        }

        foreach ($subSubCounts as $id => $count) {
            $categoryCounts[$id] = $count;
        }

        $brandCounts = [];

        foreach ($brands as $brand) {
            $brandCounts[$brand->id] = $brand->products_count;
        }

        $allCategories = Category::select('id', 'name', 'slug')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'sizes',
            'colors',
            'brands',
            'categoryCounts',
            'brandCounts',
            'allCategories',
            'totalProducts'
        ));
    }

    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $product = Product::with([
            'category',
            'images',
            'variants.size',
            'variants.color',
            'approvedReviews.user',
            'approvedReviews.images'
        ])
            ->where('slug', $slug)
            ->where('is_active', true)->first();

        if (!$product) {
            abort(404);
        }

        $variantTotalStock = 0;

        foreach ($product->variants as $variant) {
            $variantTotalStock += $variant->currentStock;
        }

        // Increment view count
        $product->incrementViewCount();

        // Get unique sizes and colors from variants
        $availableSizes = $product->variants
            ->pluck('size')
            ->unique('id')
            ->sortBy('sort_order')
            ->values()
            ->filter();

        $availableColors = $product->variants
            ->pluck('color')
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->filter();

        $relatedProducts = Product::with(['images', 'category'])
            //->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        // Calculate rating distribution
        $ratingDistribution = [
            5 => $product->approvedReviews()->where('rating', 5)->count(),
            4 => $product->approvedReviews()->where('rating', 4)->count(),
            3 => $product->approvedReviews()->where('rating', 3)->count(),
            2 => $product->approvedReviews()->where('rating', 2)->count(),
            1 => $product->approvedReviews()->where('rating', 1)->count(),
        ];

        $variantsMap = $product->variants->map(function ($variant) {
            return [
                'size_id' => $variant->size_id,
                'color_id' => $variant->color_id,
                'size_name' => $variant->size?->name,
                'color_name' => $variant->color?->name,
                'stock' => $variant->currentStock,
            ];
        })->values();

        return view('products.show', compact('product', 'relatedProducts', 'ratingDistribution', 'availableSizes', 'availableColors', 'variantTotalStock', 'variantsMap'));
    }

    /**
     * Get product details for (Quick View)
     */
    public function getQuickviewData($id)
    {
        $product = Product::with([
            'category',
            'images',
            'variants.size',
            'variants.color'
        ])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $haveVariant = $product->variants->count();

        // Transform product data for API
        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand,
            'category' => $product->category,
            'price' => (float) $product->price,
            'compare_price' => $product->compare_price ? (float) $product->compare_price : null,
            'short_description' => $product->short_description,
            'stock_in' => $product->stock_in,
            'stock' => $haveVariant > 0 ? 0 : $product->currentStock,
            'is_new_arrival' => $product->is_new_arrival,
            'is_best_seller' => $product->is_best_seller,
            'is_on_sale' => $product->is_on_sale,
            'is_featured' => $product->is_featured,
            'average_rating' => (float) $product->average_rating,
            'review_count' => $product->review_count,
            'image' => $product->thumbnail,
            'images' => $product->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => storage_url($image->image_path),
                    'is_primary' => $image->is_primary,
                ];
            }),
            'variants' => $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'size_id' => $variant->size_id,
                    'color_id' => $variant->color_id,
                    'stock_in' => $variant->stock_in,
                    'stock' => $variant->currentStock,
                    'price' => (float) ($variant->price ?? 0),
                    'size' => $variant->size ? [
                        'id' => $variant->size->id,
                        'name' => $variant->size->name,
                    ] : null,
                    'color' => $variant->color ? [
                        'id' => $variant->color->id,
                        'name' => $variant->color->name,
                        'hex_code' => $variant->color->hex_code,
                    ] : null,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'product' => $productData
        ]);
    }

    // public function search(Request $request)
    // {
    //     $name = $request->name;

    //     $products = Product::where('name', $name)
    //         ->orwhere('name', 'LIKE', $name . '%')
    //         ->orwhere('name', 'LIKE', '%' . $name . '%')
    //         ->limit(8)
    //         ->with('category')
    //         ->get();

    //     $data = [];

    //     foreach ($products as $product) {
    //         $data[] = [
    //             'id' => $product->id,
    //             'name' => $product->name,
    //             'slug' => $product->slug,
    //             'price' => $product->price,
    //             'originalPrice' => $product->compare_price,
    //             'image' => $product->thumbnail,
    //             'category' => $product->category->name,
    //             'tags' => $product->tags
    //         ];
    //     }

    //     return apiResponse($data);
    // }

    public function suggestions(Request $request)
    {
        $query = trim($request->query('query'));

        if (!$query) {
            return response()->json([]);
        }

        $suggestions = collect();

        $products = Product::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'product',
                'name' => $item->name,
                'slug' => $item->slug,
            ]);

        $categories = Category::where('name', 'like', "%{$query}%")
            ->limit(6)
            ->get()
            ->map(fn($item) => [
                'type' => $item->parent_id ? 'subcategory' : 'category',
                'name' => $item->name,
                'slug' => $item->slug,
            ]);

        $brands = Brand::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(fn($item) => [
                'type' => 'brand',
                'name' => $item->name,
                'slug' => $item->slug,
            ]);

        $suggestions = $products
            ->concat($categories)
            ->concat($brands)
            ->unique('name')
            ->take(10)
            ->values();

        return response()->json($suggestions);
    }

    public function search(Request $request)
    {
        $query = trim($request->query('query'));

        if (empty($query)) {
            return response()->json([
                'products' => []
            ]);
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%")
                    ->orWhere('short_description', 'LIKE', "%{$query}%")
                    ->orWhereHas('category', function ($cat) use ($query) {
                        $cat->where('name', 'LIKE', "%{$query}%");
                    });
            })
            ->latest()
            ->take(12)
            ->get();

        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->sale_price ?? $product->price,
                    'original_price' => $product->price,
                    'image' => $product->thumbnail,
                    'url' => route('products.show', $product->slug),
                    'category' => [
                        'name' => $product->category?->name
                    ]
                ];
            })
        ]);
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
