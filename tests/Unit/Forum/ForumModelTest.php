<?php

namespace Tests\Unit\Forum;

use App\Models\Category;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForumModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // User
    // -------------------------------------------------------------------------

    #[Test]
    public function user_is_forum_banned_when_forum_banned_at_is_set(): void
    {
        $user = User::factory()->create(['forum_banned_at' => now()]);

        $this->assertTrue($user->isForumBanned());
    }

    #[Test]
    public function user_is_not_forum_banned_when_forum_banned_at_is_null(): void
    {
        $user = User::factory()->create(['forum_banned_at' => null]);

        $this->assertFalse($user->isForumBanned());
    }

    #[Test]
    public function user_has_many_threads(): void
    {
        $user = User::factory()->create();
        Thread::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->threads);
    }

    // -------------------------------------------------------------------------
    // Thread
    // -------------------------------------------------------------------------

    #[Test]
    public function thread_generates_slug_from_title(): void
    {
        $thread = Thread::factory()->create(['title' => 'My Test Thread Title']);

        $this->assertNotNull($thread->slug);
        $this->assertStringContainsString('my-test-thread-title', $thread->slug);
    }

    #[Test]
    public function thread_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $thread->user->id);
    }

    #[Test]
    public function thread_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $thread = Thread::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $thread->category->id);
    }

    #[Test]
    public function thread_has_many_posts(): void
    {
        $thread = Thread::factory()->create();
        Post::factory()->count(3)->create(['thread_id' => $thread->id]);

        $this->assertCount(3, $thread->posts);
    }

    #[Test]
    public function thread_has_one_latest_post(): void
    {
        $thread = Thread::factory()->create();
        Post::factory()->count(3)->create(['thread_id' => $thread->id]);
        $latest = Post::factory()->create(['thread_id' => $thread->id]);

        $this->assertEquals($latest->id, $thread->latestPost->id);
    }

    // -------------------------------------------------------------------------
    // Post
    // -------------------------------------------------------------------------

    #[Test]
    public function post_belongs_to_thread(): void
    {
        $thread = Thread::factory()->create();
        $post = Post::factory()->create(['thread_id' => $thread->id]);

        $this->assertEquals($thread->id, $post->thread->id);
    }

    #[Test]
    public function post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $post->user->id);
    }

    #[Test]
    public function creating_post_updates_thread_last_post_at(): void
    {
        $thread = Thread::factory()->create(['last_post_at' => null]);

        Post::factory()->create(['thread_id' => $thread->id]);

        $this->assertNotNull($thread->fresh()->last_post_at);
    }

    // -------------------------------------------------------------------------
    // Category
    // -------------------------------------------------------------------------

    #[Test]
    public function category_generates_slug_from_name(): void
    {
        $category = Category::factory()->create(['name' => 'My Category Name']);

        $this->assertNotNull($category->slug);
        $this->assertStringContainsString('my-category-name', $category->slug);
    }

    #[Test]
    public function category_has_many_threads(): void
    {
        $category = Category::factory()->create();
        Thread::factory()->count(2)->create(['category_id' => $category->id]);

        $this->assertCount(2, $category->threads);
    }

    #[Test]
    public function category_supports_parent_child_relationship(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }
}