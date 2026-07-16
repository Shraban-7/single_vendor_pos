<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '' || strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $sales = Sale::query()
            ->with('customer:id,name')
            ->where(function ($query) use ($q) {
                $query->where('invoice_number', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%");
            })
            ->orderByDesc('sale_date')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'type' => 'sale',
                    'label' => $sale->invoice_number ?? ('#'.$sale->id),
                    'subtitle' => ($sale->customer_id ? optional($sale->customer)->name.' · ' : '').money($sale->total_amount),
                    'url' => route('admin.ecommerce-sales.show', $sale->order_number ?? $sale->id),
                ];
            });

        $products = Product::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'label' => $product->name,
                    'subtitle' => $product->sku,
                    'url' => route('admin.products.index', ['search' => $product->id]),
                ];
            });

        $customers = Customer::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'type' => 'customer',
                    'label' => $customer->name,
                    'subtitle' => $customer->phone,
                    'url' => route('admin.customers.index', ['search' => $customer->id]),
                ];
            });

        $results = $sales->concat($products)->concat($customers);

        return response()->json(['results' => $results]);
    }
}
