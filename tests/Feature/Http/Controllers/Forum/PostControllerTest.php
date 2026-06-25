<?php

namespace Tests\Feature\Http\Controllers\Forum;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Forum\PostController
 */
class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    #[Test]
    public function store_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $thread = Thread::factory()->create();

        $response = $this->post("en/forum/threads/{$thread->id}/posts", [
            'body' => 'A valid reply body.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['thread_id' => $thread->id]);
    }

    #[Test]
    public function store_creates_post_and_redirects(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)->post("en/forum/threads/{$thread->id}/posts", [
            'body' => 'A valid reply body.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'thread_id' => $thread->id,
            'user_id'   => $user->id,
            'body'      => 'A valid reply body.',
        ]);
    }

    #[Test]
    public function store_updates_thread_last_post_at(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create(['last_post_at' => null]);

        $this->actingAs($user)->post("en/forum/threads/{$thread->id}/posts", [
            'body' => 'A valid reply body.',
        ]);

        $this->assertNotNull($thread->fresh()->last_post_at);
    }

    #[Test]
    public function store_returns_403_on_locked_thread(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->locked()->create();

        $response = $this->actingAs($user)->post("en/forum/threads/{$thread->id}/posts", [
            'body' => 'A valid reply body.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['thread_id' => $thread->id]);
    }

    #[Test]
    public function store_returns_403_for_banned_users(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create(['forum_banned_at' => now()]);
        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)->post("en/forum/threads/{$thread->id}/posts", [
            'body' => 'A valid reply body.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['thread_id' => $thread->id]);
    }

    #[Test]
    public function store_validates_body_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)->post("en/forum/threads/{$thread->id}/posts", []);

        $response->assertSessionHasErrors(['body']);
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    #[Test]
    public function update_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $post = Post::factory()->create();

        $response = $this->put("en/forum/threads/{$post->thread_id}/posts/{$post->id}", [
            'body' => 'Updated body content.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['body' => 'Updated body content.']);
    }

    #[Test]
    public function update_allows_post_owner_to_update(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("en/forum/threads/{$post->thread_id}/posts/{$post->id}", [
            'body' => 'Updated body content.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id'   => $post->id,
            'body' => 'Updated body content.',
        ]);
    }

    #[Test]
    public function update_returns_403_for_non_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->put("en/forum/threads/{$post->thread_id}/posts/{$post->id}", [
            'body' => 'Hijacked body content.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['body' => 'Hijacked body content.']);
    }

    #[Test]
    public function update_allows_admin_to_update_any_post(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->put("en/forum/threads/{$post->thread_id}/posts/{$post->id}", [
            'body' => 'Admin updated body.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id'   => $post->id,
            'body' => 'Admin updated body.',
        ]);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    #[Test]
    public function destroy_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $post = Post::factory()->create();

        $response = $this->delete("en/forum/threads/{$post->thread_id}/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    #[Test]
    public function destroy_allows_post_owner_to_delete(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("en/forum/threads/{$post->thread_id}/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    #[Test]
    public function destroy_returns_403_for_non_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->delete("en/forum/threads/{$post->thread_id}/posts/{$post->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    #[Test]
    public function destroy_allows_admin_to_delete_any_post(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete("en/forum/threads/{$post->thread_id}/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
