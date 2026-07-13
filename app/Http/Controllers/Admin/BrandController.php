<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name',
            'logo' => 'nullable|mimes:png,jpg,jpeg,avif,gif,webp|max:4096',
        ]);

        $data = $request->only('name');

        $data['slug'] = Str::slug($request->name);

        $data['own_brand'] = $request->has('own_brand');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = upload_file($request->file('logo'), 'brands');
        }

        Brand::create($data);

        return back()->with('success', 'Brand created successfully');
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|mimes:png,jpg,jpeg,avif,gif,webp|max:4096',
        ]);

        $data = $request->only('name');

        $data['slug'] = Str::slug($request->name);

        $data['own_brand'] = $request->has('own_brand');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {

            if ($brand->logo) {
                delete_file($brand->logo);
            }

            $data['logo'] = upload_file($request->file('logo'), 'brands');
        }

        $brand->update($data);

        return back()->with('success', 'Brand updated successfully');
    }

    public function delete(Brand $brand)
    {
        if ($brand->logo) {
           delete_file($brand->logo);
        }

        $brand->delete();

        return back()->with('success', 'Brand deleted successfully');
    }
}