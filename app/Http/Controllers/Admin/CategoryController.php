<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        //$categories = Category::with('parent')->orderBy('sort_order', 'asc')->paginate(10);
        $categories = Category::category()->with('children.children')->orderBy('sort_order', 'asc')->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|string',
            'is_featured' => 'nullable|string',
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
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'],
            'icon' => $validated['icon'],
            'image' => $imagePath,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        Category::clearCache();

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
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

        // Handle image upload
        if ($request->hasFile('image')) {
            if (!is_null($category->image)) {
                delete_file($category->image);
            }

            $imagePath = upload_file($request->file('image'), 'categories');
            $category->image = $imagePath;
        }

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'],
            'icon' => $validated['icon'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        Category::clearCache();

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function delete(Category $category)
    {
        // Update child categories to have no parent
        Category::where('parent_id', $category->id)->update(['parent_id' => null]);

        // Delete category image if exists
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        Category::clearCache();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
