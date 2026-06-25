<?php

namespace Tests\Feature\Http\Controllers\Forum;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Forum\ForumUserController
 */
class ForumUserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ban_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $actor = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($actor)->patch("en/forum/users/{$target->id}/ban");

        $response->assertForbidden();
        $this->assertNull($target->fresh()->forum_banned_at);
    }

    #[Test]
    public function ban_bans_an_unbanned_user(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $target = User::factory()->create(['forum_banned_at' => null]);

        $this->actingAs($admin)->patch("en/forum/users/{$target->id}/ban");

        $this->assertNotNull($target->fresh()->forum_banned_at);
    }

    #[Test]
    public function ban_unbans_a_banned_user(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $target = User::factory()->create(['forum_banned_at' => now()]);

        $this->actingAs($admin)->patch("en/forum/users/{$target->id}/ban");

        $this->assertNull($target->fresh()->forum_banned_at);
    }

    #[Test]
    public function ban_redirects_back_with_success_alert(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $target = User::factory()->create(['forum_banned_at' => null]);

        $response = $this->actingAs($admin)->patch("en/forum/users/{$target->id}/ban");

        $response->assertRedirect();
        $response->assertSessionHas('alert.level', 'success');
    }
}