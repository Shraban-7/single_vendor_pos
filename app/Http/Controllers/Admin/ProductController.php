<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
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
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'stock_alert_quantity')
                        ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
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
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
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
        return Category::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    private function getUnits()
    {
        return Unit::orderBy('name')->get();
    }

    public function create()
    {
        $categories = $this->getCategories();
        $units = $this->getUnits();

        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock_in' => 'required|numeric|min:0',
            'stock_alert_quantity' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'is_returnable' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        DB::beginTransaction();
        $imgPath = null;
        $imageService = new ImageOptimizerService;

        try {
            $validated['name'] = strtoupper($request->name);
            $validated['user_id'] = Auth::id();

            $validated['is_active'] = $request->has('is_active');
            $validated['is_returnable'] = $request->has('is_returnable');

            if (empty($validated['sku'])) {
                $validated['sku'] = method_exists(Product::class, 'generate_sku')
                    ? Product::generate_sku()
                    : 'SKU-' . strtoupper(Str::random(8));
            }

            if ($request->hasFile('image')) {
                $validated['image'] = $imageService->uploadAndOptimize($request->file('image'), 'products/thumbnails');
                $imgPath = $validated['image'];
            }

            $validated['stock_quantity'] = (float) $validated['stock_in'];
            $validated['stock_out'] = 0.00;

            $product = Product::create($validated);

            if ((float) $product->stock_in > 0) {
                StockMovement::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'type' => 'in', // Replace with StockMovementType::IN->value if your enum requires strict typing
                    'reference_type' => 'product_creation',
                    'reference_id' => $product->id,
                    'quantity' => (float) $product->stock_in,
                    'unit_cost' => (float) $product->cost_price,
                    'before_quantity' => 0.00,
                    'after_quantity' => (float) $product->stock_in,
                    'notes' => $request->note ?? 'Initial stock',
                ]);

                activity_log(
                    action: 'created',
                    model: $product,
                    description: 'Product created with initial stock',
                );
            } else {
                activity_log(
                    action: 'created',
                    model: $product,
                    description: 'Product created',
                );
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'redirect' => route('admin.products.index'),
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($imgPath) {
                delete_file($imgPath);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $categories = $this->getCategories();
        $units = $this->getUnits();

        $product->load(['category', 'unit']);

        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock_alert_quantity' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'is_returnable' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $imageService = new ImageOptimizerService;
        DB::beginTransaction();

        try {
            $validated['name'] = strtoupper($request->name);

            $validated['is_active'] = $request->has('is_active');
            $validated['is_returnable'] = $request->has('is_returnable');

            if ($request->hasFile('image')) {
                if ($product->image) {
                    delete_file($product->image);
                }
                $validated['image'] = $imageService->uploadAndOptimize($request->file('image'), 'products/thumbnails');
            }

            $product->update($validated);

            activity_log(
                action: 'updated',
                model: $product,
                description: 'Product updated',
            );

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully!',
                    'redirect' => route('admin.products.index'),
                ]);
            }

            return redirect()->back()->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            if (method_exists($product, 'orderItems') && $product->orderItems()->exists()) {
                return redirect()->back()
                    ->with('error', 'This product cannot be deleted because it is associated with orders.');
            }

            if ($product->image) {
                delete_file($product->image);
            }

            if (method_exists($product, 'images')) {
                foreach ($product->images as $image) {
                    delete_file($image->image_path);
                }
            }

            activity_log(
                action: 'deleted',
                model: $product,
                description: 'Product deleted',
            );

            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function manageStock(Product $product)
    {
        $product->load(['category', 'unit']);
        return view('admin.products.manage-stock', compact('product'));
    }

    public function stockHistory(Product $product)
    {
        $product->load(['category', 'unit']);

        $stockMovements = StockMovement::where('product_id', $product->id)
            ->with(['user'])
            ->latest()
            ->paginate(20);

        return view('admin.products.stock-history', compact('product', 'stockMovements'));
    }

    public function addStock(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:0.01',
                'note' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $stockBefore = (float) $product->stock_quantity;
            $quantity = (float) $validated['quantity'];
            $stockAfter = $stockBefore + $quantity;

            $product->stock_in = (float) $product->stock_in + $quantity;
            $product->stock_quantity = $stockAfter;
            $product->save();

            StockMovement::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'type' => StockMovementType::ADJUSTMENT,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'quantity' => $quantity,
                'unit_cost' => (float) $product->cost_price,
                'before_quantity' => $stockBefore,
                'after_quantity' => $stockAfter,
                'notes' => $validated['note'] ?? null,
            ]);

            activity_log(
                action: 'updated',
                model: $product,
                description: 'Product stock added',
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock added successfully!',
                    'stock_after' => $stockAfter,
                ]);
            }

            return redirect()->back()->with('success', 'Stock added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add stock: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }

    public function removeStock(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:0.01',
                'note' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $stockBefore = (float) $product->stock_quantity;
            $quantity = (float) $validated['quantity'];

            if ($stockBefore < $quantity) {
                throw new \Exception('Insufficient stock for this product');
            }

            $product->stock_out = (float) $product->stock_out + $quantity;
            $stockAfter = $stockBefore - $quantity;
            $product->stock_quantity = $stockAfter;
            $product->save();

            StockMovement::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'type' => StockMovementType::ADJUSTMENT, // Replace with StockMovementType::OUT->value if your enum requires strict typing
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'quantity' => $quantity,
                'unit_cost' => (float) $product->cost_price,
                'before_quantity' => $stockBefore,
                'after_quantity' => $stockAfter,
                'notes' => $validated['note'] ?? null,
            ]);

            activity_log(
                action: 'updated',
                model: $product,
                description: 'Product stock removed',
            );

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

        return redirect()->route('admin.products.printBarcode')
            ->with('error', 'Product not found!');
    }


    public function setCategory($product_id, Request $request)
    {
        $categoryId = $request->input('category_id');

        $product = Product::findOrFail($product_id);
        $product->category_id = $categoryId;
        $product->save();

        return redirect()->back()->with('success', 'Product category updated successfully.');
    }
}
