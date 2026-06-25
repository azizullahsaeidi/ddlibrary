<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Thread;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /forum/categories
    public function index()
    {
        $categories = Category::withCount('threads')->orderBy('order')->get();
        return view('forum.categories.index', compact('categories'));
    }

    // GET /forum/categories/create
    public function create()
    {
        $parents = Category::orderBy('order')->get();
        return view('forum.categories.create', compact('parents'));
    }

    // POST /forum/categories
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'order'       => 'integer|min:0',
            'is_private'  => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('alert', [
            'message' => __('Category created.'),
            'level'   => 'success',
        ]);
    }

    // GET /forum/categories/{category}/edit
    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)->orderBy('order')->get();
        return view('forum.categories.edit', compact('category', 'parents'));
    }

    // PUT /forum/categories/{category}
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'order'       => 'integer|min:0',
            'is_private'  => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('alert', [
            'message' => __('Category updated.'),
            'level'   => 'success',
        ]);
    }

    // DELETE /forum/categories/{category}
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('alert', [
            'message' => __('Category deleted.'),
            'level'   => 'success',
        ]);
    }

    // GET /forum/categories/{category} (public)
    public function show(Category $category)
    {
        abort_if($category->is_private && !auth()->check(), 403);
        $threads = Thread::with(['user', 'latestPost.user'])
            ->where('category_id', $category->id)
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_post_at')
            ->paginate(20);

        return view('forum.categories.show', compact('category', 'threads'));
    }
}
