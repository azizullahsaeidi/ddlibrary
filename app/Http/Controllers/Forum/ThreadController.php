<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    // GET /forum
    public function index()
    {
        $threads = Thread::with(['user', 'category', 'latestPost.user'])
            ->when(!auth()->check(), fn($q) => $q->whereHas('category', fn($q) => $q->where('is_private', false)))
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_post_at')
            ->paginate(20);

        return view('forum.threads.index', compact('threads'));
    }

    // GET /forum/threads/create
    public function create()
    {
        $categories = Category::orderBy('order')->get();
        return view('forum.threads.create', compact('categories'));
    }

    // POST /forum/threads
    public function store(Request $request)
    {
        abort_if(auth()->user()->isForumBanned(), 403, __('You are banned from the forum.'));
        $validated = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'body'        => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
        ]);

        $thread = auth()->user()->threads()->create($validated);

        return redirect()->route('threads.show', $thread);
    }

    // GET /forum/threads/{thread}
    public function show(Thread $thread)
    {
        $posts = $thread->posts()->with('user')->paginate(25);

        return view('forum.threads.show', compact('thread', 'posts'));
    }

    // GET /forum/threads/{thread}/edit
    public function edit(Thread $thread)
    {
        $this->authorize('update', $thread);

        $categories = Category::orderBy('order')->get();
        return view('forum.threads.edit', compact('thread', 'categories'));
    }

    // PUT /forum/threads/{thread}
    public function update(Request $request, Thread $thread)
    {
        $this->authorize('update', $thread);

        $validated = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'body'        => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
        ]);

        $thread->update($validated);

        return redirect()->route('threads.show', $thread);
    }

    // DELETE /forum/threads/{thread}
    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread);

        $thread->delete();

        return redirect()->route('threads.index');
    }

    // PATCH /forum/threads/{thread}/lock
    public function lock(Thread $thread)
    {
        $thread->update(['is_locked' => !$thread->is_locked]);

        return back()->with('alert', [
            'message' => $thread->is_locked ? __('Thread locked.') : __('Thread unlocked.'),
            'level'   => 'success',
        ]);
    }

    // PATCH /forum/threads/{thread}/pin
    public function pin(Thread $thread)
    {
        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return back()->with('alert', [
            'message' => $thread->is_pinned ? __('Thread pinned.') : __('Thread unpinned.'),
            'level'   => 'success',
        ]);
    }

    // PATCH /forum/threads/{thread}/move
    public function move(Request $request, Thread $thread)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $thread->update(['category_id' => $request->category_id]);

        return back()->with('alert', [
            'message' => __('Thread moved.'),
            'level'   => 'success',
        ]);
    }
}
