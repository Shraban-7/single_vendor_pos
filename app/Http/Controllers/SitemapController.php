<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];
        
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->format('Y-m-d'),
            'changefreq' => 'always',
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => route('products.index'),
            'lastmod' => now()->format('Y-m-d'),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];

        $products = Product::get();
        foreach($products as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];
        }

        return response()->view('xml.sitemap', compact('urls'))->header('Content-Type', 'text/xml');
    }
}
