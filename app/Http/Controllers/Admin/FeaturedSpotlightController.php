<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedSpotlight;
use Illuminate\Http\Request;

class FeaturedSpotlightController extends Controller
{
    public function index()
    {
        $spotlights = FeaturedSpotlight::orderBy('sort_order')->get();
        return view('admin.spotlights.index', compact('spotlights'));
    }

    public function create()
    {
        return view('admin.spotlights.form', ['spotlight' => new FeaturedSpotlight()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'link_url'   => 'required|string|max:500',
            'link_text'  => 'nullable|string|max:100',
            'type'       => 'required|in:news,resource,external,collection',
            'active'     => 'boolean',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'integer',
        ]);

        $data['active'] = $request->boolean('active');

        FeaturedSpotlight::create($data);

        return redirect()->route('admin.spotlights.index')
            ->with('success', __('Spotlight created successfully.'));
    }

    public function edit(FeaturedSpotlight $spotlight)
    {
        return view('admin.spotlights.form', compact('spotlight'));
    }

    public function update(Request $request, FeaturedSpotlight $spotlight)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'link_url'   => 'required|string|max:500',
            'link_text'  => 'nullable|string|max:100',
            'type'       => 'required|in:news,resource,external,collection',
            'active'     => 'boolean',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'integer',
        ]);

        $data['active'] = $request->boolean('active');

        $spotlight->update($data);

        return redirect()->route('admin.spotlights.index')
            ->with('success', __('Spotlight updated successfully.'));
    }

    public function destroy(FeaturedSpotlight $spotlight)
    {
        $spotlight->delete();

        return redirect()->route('admin.spotlights.index')
            ->with('success', __('Spotlight deleted successfully.'));
    }
}
