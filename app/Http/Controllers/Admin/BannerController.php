<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255'
        ], [
            'image.required' => 'Lütfen bir banner görseli seçin.',
            'image.mimes' => 'Banner görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Banner görseli en fazla 25 MB olabilir.',
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = \App\Services\ImageOptimizerService::optimizeAndStore($request->file('image'), 'banners', 1920, 800, 88);
        }
        
        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner eklendi.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255'
        ], [
            'image.mimes' => 'Banner görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Banner görseli en fazla 25 MB olabilir.',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = \App\Services\ImageOptimizerService::optimizeAndStore($request->file('image'), 'banners', 1920, 800, 88);
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner güncellendi.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        
        return redirect()->route('admin.banners.index')->with('success', 'Banner silindi.');
    }
}
