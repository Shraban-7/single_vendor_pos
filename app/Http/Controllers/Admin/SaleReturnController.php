<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleReturn::query()
            ->withCount('items')
            ->with('order.customer');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($oq) use ($search) {
                    $oq->where('order_number', 'like', "%{$search}%");
                })->orWhereHas('order.customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });

            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $returns = $query->latest()->paginate(20)->appends($request->all());

        return view('admin.sale-returns.index', compact('returns'));
    }

    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',

            'refund_amount' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::with(['items.product', 'items.variant'])
                ->find($id);

            $saleReturn = SaleReturn::create([
                'sale_id' => $order->id,
                'returned_id' => Order::generateOrderNumber('SR'),
                'customer_id' => $order->customer_id,
                'order_number' => $order->order_number,
                'refund_amount' => $request->refund_amount ?? 0,
                'refund_method' => $request->refund_method,
                'remarks' => $request->remarks,
                'employee_id' => auth()->id(),
            ]);

            foreach ($request->items as $itemData) {

                $orderItem = OrderItem::where('id', $itemData['id'])->first();
                $size = $orderItem->variant?->size?->name;
                $color = $orderItem->variant?->color?->name;

                $variantName = trim(($size ?? '') . ' - ' . ($color ?? ''), ' -') ?: 'Standard';

                if (!$orderItem) {
                    throw new \Exception("Invalid item selected");
                }

                if ($itemData['quantity'] > $orderItem->quantity) {
                    throw new \Exception("Return qty exceeds sold qty");
                }

                $returnItem = SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $orderItem->product_id,
                    'product_variant_id' => $orderItem->product_variant_id,
                    'sku' => $orderItem->sku ?? '',
                    'product_name' => $orderItem->product->name ?? '',
                    'variant_name' => $variantName,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'is_exchanged' => false,
                ]);

                $orderItem->return_item_id = $returnItem->id;

                $orderItem->save();

                if ($orderItem->product_variant_id) {

                    $variant = ProductVariant::find($orderItem->product_variant_id);

                    if ($variant) {
                        $variant->increment('stock_in', $itemData['quantity']);
                    }

                } else {

                    $product = Product::find($orderItem->product_id);

                    if ($product) {
                        $product->increment('stock_in', $itemData['quantity']);
                    }
                }

            }

            activity_log(
                action: 'updated',
                model: $saleReturn,
                description: 'Sale Return updated',
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale return processed successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(SaleReturn $return)
    {
        return view('admin.sale-returns.show', compact('return'));
    }
}
