<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NewsController
 */
class NewsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */

     /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get('en/admin/news');

        $response->assertOk();
        $response->assertViewIs('admin.news.news_list');
    }

    public function add_post_translate_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->post(route('add_news_translate', ['newsId' => $news->newsId, 'lang' => $news->lang]), [
            // TODO: send request data
        ]);

        $response->assertRedirect('news/'.$news->id);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function add_translate_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->get('news/add/translate/{newsId}/{lang}');

        $response->assertOk();
        $response->assertViewIs('news.news_add_translate');
        $response->assertViewHas('tnid');
        $response->assertViewHas('lang');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get('news/create');

        $response->assertOk();
        $response->assertViewIs('news.news_create');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->get('news/edit/{newsId}');

        $response->assertOk();
        $response->assertViewIs('news.news_edit');
        $response->assertViewHas('news', $news);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function get_news_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('getnews'));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->post(route('add_news'), [
            // TODO: send request data
        ]);

        $response->assertRedirect('news/'.$news->id);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function translate_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->get('news/translate/{newsId}/{newsTnid}');

        $response->assertOk();
        $response->assertViewIs('news.news_translate');
        $response->assertViewHas('news', $news);
        $response->assertViewHas('news_self');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->create();

        $response = $this->post(route('update_news', ['newsId' => $news->newsId]), [
            // TODO: send request data
        ]);

        $response->assertRedirect('news/'.$id);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function view_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $news = \App\Models\News::factory()->times(3)->create();

        $response = $this->get('news/{newsId}');

        $response->assertOk();
        $response->assertViewIs('news.news_view');
        $response->assertViewHas('news', $news);
        $response->assertViewHas('translations');

        // TODO: perform additional assertions
    }

    // test cases...
}
