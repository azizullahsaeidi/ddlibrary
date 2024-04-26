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

        // Sum of all individual downloaded file sizes
        $sumOfAllIndividualDownloadedFileSizes =
            ResourceAttachment::where(function ($query) use ($request) {
                // Language
                if ($request->language) {
                    $query->whereHas('resource', function ($query) use ($request) {
                        $query->where('language', $request->language);
                    });
                }
            })->sum('file_size') /
            (1024 * 1024); // Change to MB size

        // Total Resources base on Language
        $totalResources = Resource::where(function ($query) use ($request) {
                if ($request->date_from && $request->date_to) {
                    $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
                }

                if ($request->language) {
                    $query->where('language', $request->language);
                }

                if ($request->gender) {
                    $query->whereHas('user.profile', function ($query) use ($request) {
                        $query->where('gender', $request->gender);
                    });
                }
            })
            ->groupBy('language')
            ->select('language', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->get();

        $publishers = $this->getTop10AuthorsOrPublishers($request, 9); // Get top 10 publishers
        $authors = $this->getTop10AuthorsOrPublishers($request, 24); // Get top 10 authors

        return view('admin.library-analytics.index', compact(['records', 'genders', 'languages', 'reportType', 'totalResources', 'sumOfAllIndividualDownloadedFileSizes', 'authors', 'publishers']));
    }

    private function getTop10AuthorsOrPublishers($request, $vid)
    {
        $query = TaxonomyTerm::query()->select('name', TaxonomyTerm::raw('COUNT(*) as resource_count'))->where('vid', $vid);

        if ($request->language) {
            $query->where('language', $request->language);
        }

        return $query->groupBy('name')->orderByRaw('resource_count DESC')->limit(10)->get();
    }

}