<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerPosition;
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
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'position' => 'required|in:' . implode(',', BannerPosition::values()),
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|string',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $imagePath = upload_file($request->file('image'), 'banners');

        $mobileImagePath = null;
        if ($request->hasFile('mobile_image')) {
            $mobileImagePath = upload_file($request->file('mobile_image'), 'banners');
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'description' => $validated['description'],
            'button_text' => $validated['button_text'],
            'button_link' => $validated['button_link'],
            'image' => $imagePath,
            'mobile_image' => $mobileImagePath,
            'position' => $validated['position'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->back()->with('success', 'Banner created successfully!');
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'position' => 'required|in:' . implode(',', BannerPosition::values()),
            'sort_order' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Handle main image upload
        if ($request->hasFile('image')) {
            delete_file($banner->image);
            $banner->image = upload_file($request->file('image'), 'banners');
        }

        // Handle mobile image upload
        if ($request->hasFile('mobile_image')) {
            delete_file($banner->mobile_image);
            $banner->mobile_image = upload_file($request->file('mobile_image'), 'banners');
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'description' => $validated['description'],
            'button_text' => $validated['button_text'],
            'button_link' => $validated['button_link'],
            'position' => $validated['position'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active'),
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->back()->with('success', 'Banner updated successfully!');
    }

    public function delete(Banner $banner)
    {
        // Delete banner images if exist
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
            Storage::disk('public')->delete($banner->mobile_image);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully!');
    }
}
