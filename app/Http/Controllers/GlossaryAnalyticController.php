<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlossaryAnalyticController extends Controller
{
    public function index() {
        return view('admin.analytics.glossary.index');
    }
}
