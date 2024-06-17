<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceView;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitewideAnalyticsController extends Controller
{
    use LanguageTrait;

    public function index(Request $request): View
    {
        $languages = $this->getLanguages();
        $top10ViewedResources = $this->getTop10ViewedResources($request);
        $totalViews = $this->getTotalViews($request);
        $totalRegisteredUsersViews = $this->getTotalViews($request, 'no');
        $totalGuestViews = $this->getTotalViews($request, 'yes');
        return view('admin.analytics.sitewide.index', compact('languages', 'top10ViewedResources', 'totalViews', 'totalRegisteredUsersViews', 'totalGuestViews'));
    }

    private function getTop10ViewedResources($request): Collection
    {
        return Resource::select(['id', 'title'])
            ->withCount([
                'views' => function ($query) use ($request) {
                    if ($request->date_from && $request->date_to) {
                        return $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
                    }
                },
            ])
            ->when($request->language, function ($query) use ($request) {
                return $query->whereLanguage($request->language);
            })
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTotalViews($request, $isGuest = null): float
    {
        $query = ResourceView::query();

        if ($request->language) {
            $query->whereHas('resource', function ($query) use ($request) {
                $query->where('language', $request->language);
            });
        }

        if ($request->date_from && $request->date_to) {
            $query->whereBetween('resource_views.created_at', [$request->date_from, $request->date_to]);
        }

        if ($isGuest) {
            $query->where('resource_views.user_id', $isGuest == 'no' ? '>' : '=', 0);
        }

        return $query->count();
    }

    public function viewResource(Request $request)
    {
        $languages = $this->getLanguages();
        $resourceViews = ResourceView::with(['resource:id,title,language', 'user:id', 'user.profile:id,user_id,first_name,last_name'])
            ->when($request->language, function ($query) use ($request) {
                $query->whereHas('resource', function ($query) use ($request) {
                    $query->where('language', $request->language);
                });
            })
            ->when($request->date_from && $request->date_to, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            })
            ->paginate()
            ->appends($request->except(['page']));

        return view('admin.analytics.sitewide.get-views', compact(['resourceViews', 'languages']));
    }
}
