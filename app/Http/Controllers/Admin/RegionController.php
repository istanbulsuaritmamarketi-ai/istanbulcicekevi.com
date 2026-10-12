<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = \App\Models\Region::orderBy('name')->get();
        return view('admin.regions.index', compact('regions'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate(['name' => 'required|string|unique:regions']);
        \App\Models\Region::create(['name' => $request->name, 'is_active' => true]);
        return redirect()->route('admin.regions.index')->with('success', 'Bölge eklendi.');
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Region $region)
    {
        $region->update(['is_active' => $request->has('is_active')]);
        return redirect()->route('admin.regions.index')->with('success', 'Bölge durumu güncellendi.');
    }

    public function destroy(\App\Models\Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Bölge silindi.');
    }
}
