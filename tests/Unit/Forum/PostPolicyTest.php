<?php

namespace Tests\Unit\Forum;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PostPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PostPolicy();
    }

    #[Test]
    public function owner_can_update_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($this->policy->update($user, $post));
    }

    #[Test]
    public function admin_can_update_any_post(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $post = Post::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue($this->policy->update($admin, $post));
    }

    #[Test]
    public function non_owner_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        $this->assertFalse($this->policy->update($other, $post));
    }

    #[Test]
    public function owner_can_delete_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($this->policy->delete($user, $post));
    }

    #[Test]
    public function admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $post = Post::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue($this->policy->delete($admin, $post));
    }

    #[Test]
    public function non_owner_cannot_delete_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        $this->assertFalse($this->policy->delete($other, $post));
    }
}