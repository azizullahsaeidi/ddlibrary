<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HomeController
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $survey = \App\Models\Survey::factory()->create();
        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();
        $news = \App\Models\News::factory()->times(3)->create();
        $resources = \App\Models\Resource::factory()->times(3)->create();
        $surveyQuestionOptions = \App\Models\SurveyQuestionOption::factory()->times(3)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertViewHas('latestNews');
        $response->assertViewHas('subjectAreas');
        $response->assertViewHas('featured');
        $response->assertViewHas('latestResources');
        $response->assertViewHas('surveys');
        $response->assertViewHas('surveyQuestions');
        $response->assertViewHas('surveyQuestionOptions', $surveyQuestionOptions);

        // TODO: perform additional assertions
    }

    // test cases...
}