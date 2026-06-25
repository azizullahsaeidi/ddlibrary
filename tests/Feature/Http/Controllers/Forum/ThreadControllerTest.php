<?php

namespace Tests\Feature\Http\Controllers\Forum;

use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Forum\ThreadController
 */
class ThreadControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    #[Test]
    public function index_returns_view_for_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        Thread::factory()->create();

        $response = $this->get('en/forum/threads');

        $response->assertOk();
        $response->assertViewIs('forum.threads.index');
    }

    #[Test]
    public function index_hides_private_category_threads_from_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $private = Category::factory()->private()->create();
        $thread = Thread::factory()->create(['category_id' => $private->id]);

        $response = $this->get('en/forum/threads');

        $response->assertOk();
        $response->assertDontSee($thread->title);
    }

    #[Test]
    public function index_shows_private_category_threads_to_authenticated_users(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $private = Category::factory()->private()->create();
        $thread = Thread::factory()->create(['category_id' => $private->id]);

        $response = $this->actingAs($user)->get('en/forum/threads');

        $response->assertOk();
        $response->assertSee($thread->title);
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    #[Test]
    public function create_redirects_guests_to_login(): void
    {
        $this->refreshApplicationWithLocale('en');

        $response = $this->get('en/forum/threads/create');

        $response->assertRedirect();
    }

    #[Test]
    public function create_returns_view_for_authenticated_users(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('en/forum/threads/create');

        $response->assertOk();
        $response->assertViewIs('forum.threads.create');
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    #[Test]
    public function store_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $category = Category::factory()->create();

        $response = $this->post('en/forum/threads', [
            'title'       => 'A valid thread title',
            'body'        => 'A valid body with enough content.',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('threads', ['title' => 'A valid thread title']);
    }

    #[Test]
    public function store_creates_thread_and_redirects(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/threads', [
            'title'       => 'A valid thread title',
            'body'        => 'A valid body with enough content.',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('threads', [
            'title'       => 'A valid thread title',
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);
    }

    #[Test]
    public function store_returns_403_for_banned_users(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create(['forum_banned_at' => now()]);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/threads', [
            'title'       => 'A valid thread title',
            'body'        => 'A valid body with enough content.',
            'category_id' => $category->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('threads', ['user_id' => $user->id]);
    }

    #[Test]
    public function store_validates_required_fields(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/threads', []);

        $response->assertSessionHasErrors(['title', 'body', 'category_id']);
    }

    #[Test]
    public function store_validates_minimum_title_length(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/threads', [
            'title'       => 'Hi',
            'body'        => 'A valid body with enough content.',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    #[Test]
    public function store_validates_category_exists(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/threads', [
            'title'       => 'A valid thread title',
            'body'        => 'A valid body with enough content.',
            'category_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    #[Test]
    public function show_returns_view_for_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $thread = Thread::factory()->create();

        $response = $this->get("en/forum/threads/{$thread->id}");

        $response->assertOk();
        $response->assertViewIs('forum.threads.show');
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    #[Test]
    public function edit_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $thread = Thread::factory()->create();

        $response = $this->get("en/forum/threads/{$thread->id}/edit");

        $response->assertRedirect();
    }

    #[Test]
    public function edit_returns_view_for_thread_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("en/forum/threads/{$thread->id}/edit");

        $response->assertOk();
        $response->assertViewIs('forum.threads.edit');
    }

    #[Test]
    public function edit_returns_403_for_non_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->get("en/forum/threads/{$thread->id}/edit");

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    #[Test]
    public function update_redirects_guests(): void
    {
        $this->refreshApplicationWithLocale('en');

        $thread = Thread::factory()->create();

        $response = $this->put("en/forum/threads/{$thread->id}", [
            'title'       => 'Updated title here',
            'body'        => 'Updated body with enough content.',
            'category_id' => $thread->category_id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('threads', ['title' => 'Updated title here']);
    }

    #[Test]
    public function update_allows_thread_owner_to_update(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("en/forum/threads/{$thread->id}", [
            'title'       => 'Updated title here',
            'body'        => 'Updated body with enough content.',
            'category_id' => $thread->category_id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('threads', [
            'id'    => $thread->id,
            'title' => 'Updated title here',
        ]);
    }

    #[Test]
    public function update_returns_403_for_non_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->put("en/forum/threads/{$thread->id}", [
            'title'       => 'Hijacked title',
            'body'        => 'Updated body with enough content.',
            'category_id' => $thread->category_id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('threads', ['title' => 'Hijacked title']);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    #[Test]
    public function destroy_allows_thread_owner_to_delete(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("en/forum/threads/{$thread->id}");

        $response->assertRedirect(route('threads.index'));
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    }

    #[Test]
    public function destroy_returns_403_for_non_owner(): void
    {
        $this->refreshApplicationWithLocale('en');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->delete("en/forum/threads/{$thread->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    }

    // -------------------------------------------------------------------------
    // lock (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function lock_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)->patch("en/forum/threads/{$thread->id}/lock");

        $response->assertForbidden();
    }

    #[Test]
    public function lock_toggles_thread_lock_status(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $thread = Thread::factory()->create(['is_locked' => false]);

        $this->actingAs($admin)->patch("en/forum/threads/{$thread->id}/lock");

        $this->assertTrue($thread->fresh()->is_locked);

        $this->actingAs($admin)->patch("en/forum/threads/{$thread->id}/lock");

        $this->assertFalse($thread->fresh()->is_locked);
    }

    // -------------------------------------------------------------------------
    // pin (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function pin_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)->patch("en/forum/threads/{$thread->id}/pin");

        $response->assertForbidden();
    }

    #[Test]
    public function pin_toggles_thread_pin_status(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $thread = Thread::factory()->create(['is_pinned' => false]);

        $this->actingAs($admin)->patch("en/forum/threads/{$thread->id}/pin");

        $this->assertTrue($thread->fresh()->is_pinned);

        $this->actingAs($admin)->patch("en/forum/threads/{$thread->id}/pin");

        $this->assertFalse($thread->fresh()->is_pinned);
    }

    // -------------------------------------------------------------------------
    // move (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function move_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $thread = Thread::factory()->create();
        $newCategory = Category::factory()->create();

        $response = $this->actingAs($user)->patch("en/forum/threads/{$thread->id}/move", [
            'category_id' => $newCategory->id,
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function move_moves_thread_to_new_category(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $thread = Thread::factory()->create();
        $newCategory = Category::factory()->create();

        $this->actingAs($admin)->patch("en/forum/threads/{$thread->id}/move", [
            'category_id' => $newCategory->id,
        ]);

        $this->assertEquals($newCategory->id, $thread->fresh()->category_id);
    }
}