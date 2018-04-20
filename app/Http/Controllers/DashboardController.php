<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Resource;
use App\Page;
use App\News;

class DashboardController extends Controller
{
    public function index()
    {
        $usersModel         = new User();
        $resourceModel      = new Resource();
        $pagesModel         = new Page();
        $newsModel          = new News();
        //total users in number for the dashboard
        $totalUsers         = $usersModel->totalUsers();
        //latest users for the dashboard
        $latestUsers        = $usersModel->users()->sortByDesc('userid')->take(5);
        //total resources in number for the dashboard
        $totalResources     = $resourceModel->totalResources();
        //latest resources for the dashboard
        $latestResources    = $resourceModel->resources()->sortByDesc('created')->take(5);
        $totalNews          = $newsModel->totalNews();
        $totalPages         = $pagesModel->totalPages();
        return view('admin.main', compact(
            'totalUsers', 
            'latestUsers', 
            'totalResources', 
            'latestResources',
            'totalNews', 
            'totalPages'
        ));
    }
}
