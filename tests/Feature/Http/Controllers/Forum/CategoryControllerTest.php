<?php

namespace Tests\Feature\Http\Controllers\Forum;

use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Forum\CategoryController
 */
class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function index_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('en/forum/categories');

        $response->assertForbidden();
    }

    #[Test]
    public function index_returns_view_for_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get('en/forum/categories');

        $response->assertOk();
        $response->assertViewIs('forum.categories.index');
    }

    // -------------------------------------------------------------------------
    // create (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function create_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('en/forum/categories/create');

        $response->assertForbidden();
    }

    #[Test]
    public function create_returns_view_for_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get('en/forum/categories/create');

        $response->assertOk();
        $response->assertViewIs('forum.categories.create');
    }

    // -------------------------------------------------------------------------
    // store (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function store_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('en/forum/categories', [
            'name' => 'Test Category',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('categories', ['name' => 'Test Category']);
    }

    #[Test]
    public function store_creates_category_and_redirects(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post('en/forum/categories', [
            'name'        => 'Test Category',
            'description' => 'A test category description.',
            'order'       => 1,
            'is_private'  => false,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    #[Test]
    public function store_validates_name_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post('en/forum/categories', []);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function store_supports_parent_category(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $parent = Category::factory()->create();

        $this->actingAs($admin)->post('en/forum/categories', [
            'name'      => 'Child Category',
            'parent_id' => $parent->id,
        ]);

        $this->assertDatabaseHas('categories', [
            'name'      => 'Child Category',
            'parent_id' => $parent->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // edit (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function edit_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get("en/forum/categories/{$category->id}/edit");

        $response->assertForbidden();
    }

    #[Test]
    public function edit_returns_view_for_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->get("en/forum/categories/{$category->id}/edit");

        $response->assertOk();
        $response->assertViewIs('forum.categories.edit');
    }

    // -------------------------------------------------------------------------
    // update (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function update_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put("en/forum/categories/{$category->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('categories', ['name' => 'Updated Name']);
    }

    #[Test]
    public function update_updates_category_and_redirects(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->put("en/forum/categories/{$category->id}", [
            'name'        => 'Updated Category Name',
            'description' => 'Updated description.',
            'order'       => 2,
            'is_private'  => true,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id'         => $category->id,
            'name'       => 'Updated Category Name',
            'is_private' => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // destroy (admin only)
    // -------------------------------------------------------------------------

    #[Test]
    public function destroy_requires_admin(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete("en/forum/categories/{$category->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    #[Test]
    public function destroy_deletes_category_and_redirects(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete("en/forum/categories/{$category->id}");

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // -------------------------------------------------------------------------
    // show (public)
    // -------------------------------------------------------------------------

    #[Test]
    public function show_returns_view_for_public_category(): void
    {
        $this->refreshApplicationWithLocale('en');

        $category = Category::factory()->create();

        $response = $this->get("en/forum/categories/{$category->id}");

        $response->assertOk();
        $response->assertViewIs('forum.categories.show');
    }

    #[Test]
    public function show_returns_403_for_private_category_when_guest(): void
    {
        $this->refreshApplicationWithLocale('en');

        $category = Category::factory()->private()->create();

        $response = $this->get("en/forum/categories/{$category->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function show_returns_view_for_private_category_when_authenticated(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $category = Category::factory()->private()->create();

        $response = $this->actingAs($user)->get("en/forum/categories/{$category->id}");

        $response->assertOk();
    }

    #[Test]
    public function show_lists_threads_in_category(): void
    {
        $this->refreshApplicationWithLocale('en');

        $category = Category::factory()->create();
        $thread = Thread::factory()->create(['category_id' => $category->id]);

        $response = $this->get("en/forum/categories/{$category->id}");

        $response->assertOk();
        $response->assertSee($thread->title);
    }
}