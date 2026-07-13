<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $productLimit = 10;

        $newArrivals = Product::where('is_active', true)
            ->where('is_new_arrival', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->take($productLimit)
            ->get();

        if ($newArrivals->count() != $productLimit) {
            $newArrivals = Product::where('is_active', true)
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->take($productLimit)
                ->get();
        }

        // Fetch best selling products
        $bestSelling = Product::where('is_active', true)
            ->where('is_best_seller', true)
            ->with('category')
            ->orderBy('review_count', 'desc')
            ->take($productLimit)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('review_count', 'desc')
            ->take($productLimit)
            ->get();

        $onSaleProducts = Product::where('is_active', true)
            ->where('is_on_sale', true)
            ->with('category')
            ->orderBy('review_count', 'desc')
            ->take($productLimit)
            ->get();

        // Fetch men's products
        $menCategoryIds = Category::where('slug', 'like', '%men%')->pluck('id')->toArray();

        $mensProducts = collect();
        if (count($menCategoryIds)) {
            $mensProducts = Product::where('is_active', true)
                ->where(function ($query) use ($menCategoryIds) {
                    $query->whereIn('category_id', $menCategoryIds)
                        ->orWhereIn('subcategory_id', $menCategoryIds);
                })
                ->with('category')
                ->latest('id')
                ->take($productLimit)
                ->get();
        }

        // Fetch women's products
        $womenCategoryIds = Category::where('slug', 'like', '%women%')->pluck('id')->toArray();

        $womensProducts = collect();
        if (count($womenCategoryIds)) {
            $womensProducts = Product::where('is_active', true)
                ->where(function ($query) use ($womenCategoryIds) {
                    $query->whereIn('category_id', $womenCategoryIds)
                        ->orWhereIn('subcategory_id', $womenCategoryIds);
                })
                ->with('category')
                ->latest('id')
                ->take($productLimit)
                ->get();
        }

        // Fetch featured categories
        $featuredCategories = Category::where('is_featured', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $banners = Banner::get();

        $ourBrands = Brand::where('is_active', true)
            ->where('own_brand', true)
            ->limit(5)
            ->get();

        // Fetch active home sections with dynamic items
        $homeSections = HomeSection::active()
            ->with(['items' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }, 'items.item'])
            ->orderBy('sort_order', 'asc')
            ->get();

        // Resolve products based on source for dynamic sections
        foreach ($homeSections as $section) {
            $source = $section->source?->value;

            if ($source === 'new_arrivals') {
                $section->resolvedProducts = Product::where('is_active', true)
                    ->latest()
                    ->take(15)
                    ->get();
            } elseif ($source === 'best_sellers') {
                $section->resolvedProducts = Product::where('is_active', true)
                    ->where('is_best_seller', true)
                    ->orderBy('review_count', 'desc')
                    ->take(15)
                    ->get();
            } elseif ($source === 'featured') {
                $section->resolvedProducts = Product::where('is_active', true)
                    ->where('is_featured', true)
                    ->orderBy('review_count', 'desc')
                    ->take(15)
                    ->get();
            }
        }

        return view('home', compact(
            'newArrivals',
            'bestSelling',
            'mensProducts',
            'womensProducts',
            'featuredCategories',
            'banners',
            'ourBrands',
            'featuredProducts',
            'onSaleProducts',
            'homeSections'
        ));
    }
}
