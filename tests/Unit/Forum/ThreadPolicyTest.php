<?php

namespace Tests\Unit\Forum;

use App\Models\Thread;
use App\Models\User;
use App\Policies\ThreadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ThreadPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ThreadPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ThreadPolicy();
    }

    #[Test]
    public function owner_can_update_their_thread(): void
    {
        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($this->policy->update($user, $thread));
    }

    #[Test]
    public function admin_can_update_any_thread(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $thread = Thread::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue($this->policy->update($admin, $thread));
    }

    #[Test]
    public function non_owner_cannot_update_thread(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        $this->assertFalse($this->policy->update($other, $thread));
    }

    #[Test]
    public function owner_can_delete_their_thread(): void
    {
        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($this->policy->delete($user, $thread));
    }

    #[Test]
    public function admin_can_delete_any_thread(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $thread = Thread::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue($this->policy->delete($admin, $thread));
    }

    #[Test]
    public function non_owner_cannot_delete_thread(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        $this->assertFalse($this->policy->delete($other, $thread));
    }
}