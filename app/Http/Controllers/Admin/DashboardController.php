<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get date ranges
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Total Revenue
        $totalRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Total Orders
        $totalOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total Customers
        $totalCustomers = User::where('role', 'customer')->count();

        // Pending Orders
        $pendingOrders = Order::where('status', 'pending')->count();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->shipping_name ?? $order->user->name,
                    'created_at' => $order->created_at->format('M d, Y'),
                    'total' => $order->total,
                    'status' => $order->status->value ?? 'pending',
                ];
            });

        // Top Products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as sales'), DB::raw('SUM(subtotal) as revenue'))
            ->with('product.images')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'image' => $item->product->thumbnail,
                    'sales' => $item->sales,
                    'revenue' => $item->revenue,
                ];
            });

        // Low Stock Products Count
        $lowStockCount = Product::where('stock_in', '<', 10)->count();

        // Additional metrics
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $outOfStock = Product::where('stock_in', '<=', 0)->count();
        $totalReviews = Review::count();
        $activeCoupons = Coupon::where('expires_at', '>', now())->count();

        // Today's metrics
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total');

        // Average Order Value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $widgets['totalRevenue'] = $totalRevenue;
        $widgets['totalOrders'] = $totalOrders;
        $widgets['totalCustomers'] = $totalCustomers;
        $widgets['pendingOrders'] = $pendingOrders;
        $widgets['totalProducts'] = $totalProducts;
        $widgets['totalCategories'] = $totalCategories;
        $widgets['outOfStock'] = $outOfStock;
        $widgets['totalReviews'] = $totalReviews;
        $widgets['activeCoupons'] = $activeCoupons;
        $widgets['todayOrders'] = $todayOrders;
        $widgets['todayRevenue'] = $todayRevenue;
        $widgets['avgOrderValue'] = $avgOrderValue;

        return view('admin.dashboard', compact(
            'widgets',
            'recentOrders',
            'topProducts',
            'lowStockCount'
        ));
    }
}
