<?php

namespace App\Http\Controllers;

use App\Models\DownloadCount;
use App\Models\Resource;
use App\Models\ResourceAttachment;
use App\Models\TaxonomyTerm;
use App\Traits\GenderTrait;
use App\Traits\LanguageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LibraryAnalyticsController extends Controller
{
    use LanguageTrait, GenderTrait;

    public function index(Request $request): View
    {
        
        $genders = $this->genders();
        $languages = $this->getLanguages();
        $sumOfAllIndividualDownloadedFileSizes = ResourceAttachment::sum('file_size') / (1024 * 1024); // Change to MB size

        // Total Resources base on Language
        $totalResources = Resource::where(function($query) use ($request){
            if($request->date_from && $request->date_to){
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }

            if ($request->gender) {
                $query->whereHas('user.profile', function ($query) use ($request) {
                    $query->where('gender', $request->gender);
                });
            }
        })->groupBy('language')->select('language', DB::raw('count(*) as count'))->get();

        return view('admin.library-analytics.index', compact(['records', 'genders', 'languages', 'reportType', 'totalResources', 'sumOfAllIndividualDownloadedFileSizes']));
    }
}
