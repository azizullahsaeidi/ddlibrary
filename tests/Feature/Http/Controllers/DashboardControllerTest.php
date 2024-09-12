<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DashboardController
 */
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $users = \App\Models\User::factory()->times(3)->create();
        $resources = \App\Models\Resource::factory()->times(3)->create();
        $news = \App\Models\News::factory()->times(3)->create();
        $pages = \App\Models\Page::factory()->times(3)->create();

        $response = $this->get('admin');

        $response->assertOk();
        $response->assertViewIs('admin.main');
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('latestUsers');
        $response->assertViewHas('totalResources');
        $response->assertViewHas('latestResources');
        $response->assertViewHas('totalNews');
        $response->assertViewHas('latestNews');
        $response->assertViewHas('totalPages');
        $response->assertViewHas('latestPages');

        // TODO: perform additional assertions
    }

    // test cases...
}