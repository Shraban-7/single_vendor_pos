<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function show($slug)
    {
        $page = \App\Models\StaticPage::where('slug', $slug)->active()->first();

        if(!$page) {
            abort(404);
        }
        
        return view('static_pages.show', compact('page'));
    }
}
