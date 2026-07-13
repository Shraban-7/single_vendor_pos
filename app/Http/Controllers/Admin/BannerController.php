<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|string',
        ]);

        $imagePath = upload_file($request->file('image'), 'banners');

        Banner::create([
            'title' => $validated['title'],
            'button_text' => $validated['button_text'],
            'button_link' => $validated['button_link'],
            'image' => $imagePath,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Banner created successfully!');
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
        ]);

        // Handle main image upload
        if ($request->hasFile('image')) {
            delete_file($banner->image);
            $banner->image = upload_file($request->file('image'), 'banners');
        }

        $banner->update([
            'title' => $validated['title'],
            'button_text' => $validated['button_text'],
            'button_link' => $validated['button_link'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Banner updated successfully!');
    }

    public function delete(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully!');
    }
}
