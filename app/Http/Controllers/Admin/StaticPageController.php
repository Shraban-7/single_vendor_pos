<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;

class StaticPageController extends Controller
{
    public function index()
    {
        $pages = StaticPage::orderBy('created_at', 'desc')->get();

        return view('admin.static_pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.static_pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:static_pages,slug',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'footer_position' => 'nullable|integer|min:1|max:255',
        ]);

        StaticPage::create([
            ...$request->only('title', 'slug', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'is_active', 'sort_order', 'footer_position'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.static_pages.index')->with('success', 'Static page created successfully.');
    }

    public function edit($id)
    {
        $page = StaticPage::findOrFail($id);

        return view('admin.static_pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = StaticPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:static_pages,slug,{$id}",
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'footer_position' => 'nullable|integer|min:1|max:255',
        ]);

        $page->update([
            ...$request->only('title', 'slug', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'is_active', 'sort_order', 'footer_position'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.static_pages.index')->with('success', 'Static page updated successfully.');
    }

    public function destroy($id)
    {
        $page = StaticPage::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.static_pages.index')->with('success', 'Static page deleted successfully.');
    }
}
