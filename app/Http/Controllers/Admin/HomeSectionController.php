<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HomeSectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomeSectionRequest;
use App\Http\Requests\UpdateHomeSectionRequest;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sections = HomeSection::withCount('items')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.home_sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = HomeSectionType::cases();
        return view('admin.home_sections.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHomeSectionRequest $request)
    {
        $validated = $request->validated();
        
        $validated['settings'] = []; // Empty settings array on creation

        $section = HomeSection::create($validated);

        return redirect()
            ->route('admin.home-sections.edit', $section->id)
            ->with('success', 'Section created successfully! Now you can manage items and settings.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomeSection $homeSection)
    {
        $types = HomeSectionType::cases();
        
        // Eager load related items with polymorphic relation
        $homeSection->load(['items' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        }, 'items.item']);

        return view('admin.home_sections.edit', [
            'section' => $homeSection,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHomeSectionRequest $request, HomeSection $homeSection)
    {
        $validated = $request->validated();

        // Extract settings based on component type and save inside settings JSON
        $type = $validated['type'];
        $settings = [];

        // Check if settings are submitted in request
        if ($request->has('settings')) {
            $rawSettings = $request->input('settings');
            
            // Clean settings depending on component type to keep DB clean
            if (str_contains($type, 'product')) {
                $settings = [
                    'show_view_all' => filter_var($rawSettings['show_view_all'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'show_price' => filter_var($rawSettings['show_price'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'show_discount_badge' => filter_var($rawSettings['show_discount_badge'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'product_autoplay' => filter_var($rawSettings['product_autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'items_per_row' => (int) ($rawSettings['items_per_row'] ?? 4),
                ];

            } elseif (str_contains($type, 'banner')) {
                $settings = [
                    'banner_autoplay' => filter_var($rawSettings['banner_autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'show_navigation' => filter_var($rawSettings['show_navigation'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'show_dots' => filter_var($rawSettings['show_dots'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            } elseif (str_contains($type, 'category')) {
                $settings = [
                    'items_per_row' => (int) ($rawSettings['items_per_row'] ?? 4),
                ];
            }
        }

        $validated['settings'] = $settings;

        $homeSection->update($validated);

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomeSection $homeSection)
    {
        DB::transaction(function () use ($homeSection) {
            // Cascade delete attached items
            $homeSection->items()->delete();
            $homeSection->delete();
        });

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section and its attached items deleted successfully!');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(HomeSection $homeSection)
    {
        $homeSection->update([
            'is_active' => !$homeSection->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $homeSection->is_active,
            'message' => 'Status updated successfully!'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Autocomplete Search Endpoints for Select2
    |--------------------------------------------------------------------------
    */

    public function searchProducts(Request $request)
    {
        $query = $request->input('q');

        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->paginate(20);

        return response()->json([
            'results' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name . ' (SKU: ' . $product->sku . ')'
                ];
            }),
            'pagination' => [
                'more' => $products->hasMorePages()
            ]
        ]);
    }

    public function searchCategories(Request $request)
    {
        $query = $request->input('q');

        $categories = Category::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->paginate(20);

        return response()->json([
            'results' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'text' => $category->name
                ];
            }),
            'pagination' => [
                'more' => $categories->hasMorePages()
            ]
        ]);
    }

    public function searchBanners(Request $request)
    {
        $query = $request->input('q');

        $banners = Banner::active()
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->paginate(20);

        return response()->json([
            'results' => $banners->map(function ($banner) {
                $positionLabel = $banner->position->value ?? $banner->position;
                return [
                    'id' => $banner->id,
                    'text' => $banner->title . ' (Position: ' . $positionLabel . ')'
                ];
            }),
            'pagination' => [
                'more' => $banners->hasMorePages()
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Dynamic Item Management
    |--------------------------------------------------------------------------
    */

    /**
     * Add item to home section.
     */
    public function addItem(Request $request, HomeSection $homeSection)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer'],
            'item_type' => ['required', 'string', 'in:product,category,banner'],
        ]);

        // Check if item is already added to this section
        $exists = $homeSection->items()
            ->where('item_id', $validated['item_id'])
            ->where('item_type', $validated['item_type'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This item is already assigned to this section.'
            ], 422);
        }

        // Get max sort_order
        $maxOrder = $homeSection->items()->max('sort_order') ?? 0;

        $sectionItem = $homeSection->items()->create([
            'item_id' => $validated['item_id'],
            'item_type' => $validated['item_type'],
            'sort_order' => $maxOrder + 1,
        ]);

        $sectionItem->load('item');

        // Extract title/name dynamically
        $name = $sectionItem->item->name ?? $sectionItem->item->title ?? 'N/A';
        $details = '';
        if ($validated['item_type'] === 'product') {
            $details = 'SKU: ' . ($sectionItem->item->sku ?? 'N/A');
        } elseif ($validated['item_type'] === 'banner') {
            $details = 'Position: ' . ($sectionItem->item->position->value ?? $sectionItem->item->position ?? 'N/A');
        }

        return response()->json([
            'success' => true,
            'message' => 'Item assigned successfully.',
            'item' => [
                'id' => $sectionItem->id,
                'name' => $name,
                'details' => $details,
                'type' => ucfirst($validated['item_type']),
                'sort_order' => $sectionItem->sort_order,
            ]
        ]);
    }

    /**
     * Remove item from home section.
     */
    public function removeItem(HomeSectionItem $homeSectionItem)
    {
        $homeSectionItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully.'
        ]);
    }

    /**
     * Reorder items in home section.
     */
    public function reorderItems(Request $request, HomeSection $homeSection)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:home_section_items,id'],
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('order') as $index => $itemId) {
                HomeSectionItem::where('id', $itemId)->update([
                    'sort_order' => $index + 1
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sort order saved successfully.'
        ]);
    }
}
