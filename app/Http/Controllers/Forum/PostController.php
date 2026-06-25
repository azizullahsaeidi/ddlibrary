<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // POST /forum/threads/{thread}/posts
    public function store(Request $request, Thread $thread)
    {
        abort_if($thread->is_locked, 403, 'This thread is locked.');
        abort_if(auth()->user()->isForumBanned(), 403, __('You are banned from the forum.'));

        $validated = $request->validate([
            'body' => 'required|string|min:3',
        ]);

        $thread->posts()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        // Redirect to the last page so user sees their new post
        $lastPage = $thread->posts()->paginate(25)->lastPage();

        return redirect()->route('threads.show', [$thread, 'page' => $lastPage])
            ->withFragment('posts');
    }

    // PUT /forum/threads/{thread}/posts/{post}
    public function update(Request $request, Thread $thread, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'body' => 'required|string|min:3',
        ]);

        $post->update($validated);

        return redirect()->route('threads.show', $thread);
    }

    // DELETE /forum/threads/{thread}/posts/{post}
    public function destroy(Thread $thread, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('threads.show', $thread);
    }
}
