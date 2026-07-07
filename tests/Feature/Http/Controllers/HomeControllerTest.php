<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FeaturedSpotlight;
use App\Models\News;
use App\Models\Resource;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HomeController
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        Survey::factory()->create();
        SurveyQuestion::factory()->create();
        News::factory(3)->create(['status' => 1]);
        Resource::factory()->count(3)->create();
        SurveyQuestionOption::factory()->count(10)->create();

        $response = $this->get('/en');

        $response->assertOk();

        $response->assertViewIs('home');

        $response->assertViewHas('subjectAreas');
        $response->assertViewHas('featured');
        $response->assertViewHas('spotlights');
    }

    #[Test]
    public function index_passes_active_spotlights_to_the_view(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->count(2)->create();
        FeaturedSpotlight::factory()->inactive()->create();

        $response = $this->get('/en');

        $response->assertOk();
        $response->assertViewHas('spotlights', fn ($spotlights) => $spotlights->count() === 2);
    }

    #[Test]
    public function index_renders_spotlight_carousel_when_active_spotlights_exist(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->create(['title' => 'Test Spotlight Title', 'type' => 'news']);

        $response = $this->get('/en');

        $response->assertOk();
        $response->assertSee('spotlightCarousel');
        $response->assertSee('Test Spotlight Title');
    }

    #[Test]
    public function index_does_not_render_spotlight_carousel_when_no_active_spotlights_exist(): void
    {
        $this->refreshApplicationWithLocale('en');

        $response = $this->get('/en');

        $response->assertOk();
        $response->assertDontSee('spotlightCarousel');
    }

    #[Test]
    public function index_excludes_inactive_spotlights(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->inactive()->create(['title' => 'Hidden Spotlight']);

        $response = $this->get('/en');

        $response->assertViewHas('spotlights', fn ($spotlights) => $spotlights->isEmpty());
        $response->assertDontSee('Hidden Spotlight');
    }

    #[Test]
    public function index_excludes_spotlights_that_have_not_started_yet(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->notYetStarted()->create(['title' => 'Future Spotlight']);

        $response = $this->get('/en');

        $response->assertViewHas('spotlights', fn ($spotlights) => $spotlights->isEmpty());
        $response->assertDontSee('Future Spotlight');
    }

    #[Test]
    public function index_excludes_expired_spotlights(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->expired()->create(['title' => 'Expired Spotlight']);

        $response = $this->get('/en');

        $response->assertViewHas('spotlights', fn ($spotlights) => $spotlights->isEmpty());
        $response->assertDontSee('Expired Spotlight');
    }

    #[Test]
    public function index_renders_carousel_controls_only_when_multiple_spotlights_exist(): void
    {
        $this->refreshApplicationWithLocale('en');

        FeaturedSpotlight::factory()->create(['type' => 'news']);

        $response = $this->get('/en');

        $response->assertDontSee('carousel-control-prev');
        $response->assertDontSee('carousel-control-next');

        FeaturedSpotlight::factory()->create(['type' => 'external']);

        $response = $this->get('/en');

        $response->assertSee('carousel-control-prev');
        $response->assertSee('carousel-control-next');
    }
}