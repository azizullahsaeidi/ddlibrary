<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SurveyQuestionController
 */
class SurveyQuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function add_translate_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();
        $survey = \App\Models\Survey::factory()->create();

        $response = $this->get('admin/survey/question/add/translate/{id}/{lang}');

        $response->assertOk();
        $response->assertViewIs('admin.surveys.question.add_translation');
        $response->assertViewHas('tnid');
        $response->assertViewHas('lang');
        $response->assertViewHas('survey', $survey);
        $response->assertViewHas('question');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $survey = \App\Models\Survey::factory()->create();
        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();

        $response = $this->get('admin/survey/question/add/{id}');

        $response->assertOk();
        $response->assertViewIs('admin.surveys.question.create');
        $response->assertViewHas('survey', $survey);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function delete_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();

        $response = $this->get('admin/survey/question/delete/{id}');

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $survey = \App\Models\Survey::factory()->create();
        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();
        $surveyQuestions = \App\Models\SurveyQuestion::factory()->times(3)->create();

        $response = $this->get('admin/survey/questions/{id}');

        $response->assertOk();
        $response->assertViewIs('admin.surveys.question.list');
        $response->assertViewHas('survey', $survey);
        $response->assertViewHas('survey_questions');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $surveyQuestionOption = \App\Models\SurveyQuestionOption::factory()->create();
        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();

        $response = $this->post(route('create_question'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function view_returns_an_ok_response(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $surveyQuestion = \App\Models\SurveyQuestion::factory()->create();
        $survey = \App\Models\Survey::factory()->create();

        $response = $this->get('admin/survey/{surveyid}/question/view/{id}/{tnid}');

        $response->assertOk();
        $response->assertViewIs('admin.surveys.question.view');
        $response->assertViewHas('questions');
        $response->assertViewHas('question_self');
        $response->assertViewHas('survey', $survey);

        // TODO: perform additional assertions
    }

    // test cases...
}