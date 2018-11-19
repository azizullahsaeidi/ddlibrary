<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Survey;
use App\SurveyQuestion;
use App\SurveyAnswer;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::find(1);
        $surveyQuestions = SurveyQuestion::where('survey_id', 1)->first();
        return view('survey.survey_view', compact('surveys', 'surveyQuestions'));
    }

    public function storeSurvey(Request $request)
    {
        parse_str(request('mydata'), $output);
        $answer = $output['useful'];  // value

        $surveyAnswer = new SurveyAnswer();
        $surveyAnswer->question_id = 1;
        $surveyAnswer->answer = $answer;
        $surveyAnswer->ip = \Request::ip();
        $surveyAnswer->save();
        echo true;   
    }
}
