<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        // Flat structure, ordered by display_order
        $categories = Category::orderBy('display_order', 'asc')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = upload_file($request->file('image'), 'categories');
        }

        Category::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'name_bn' => $validated['name_bn'],
            'slug' => $slug,
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'image' => $imagePath,
            'display_order' => $validated['display_order'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($category->name !== $request->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $category->slug = $slug;
        }

        if ($request->hasFile('image')) {
            if (!is_null($category->image)) {
                delete_file($category->image);
            }
            $category->image = upload_file($request->file('image'), 'categories');
        }

        $category->update([
            'name' => $validated['name'],
            'name_bn' => $validated['name_bn'],
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'display_order' => $validated['display_order'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function delete(Category $category)
    {
        if ($category->image) {
            delete_file($category->image);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
