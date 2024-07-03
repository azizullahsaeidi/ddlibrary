<?php

namespace App\Http\Controllers;

use App\Enums\LanguageEnum;
use App\Models\Browser;
use App\Models\Device;
use App\Models\PageType;
use App\Models\PageView;
use App\Models\Platform;
use App\Traits\GenderTrait;
use App\Traits\LanguageTrait;
use App\Traits\PageViewConditionTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SitewideAnalyticsController extends Controller
{
    use LanguageTrait, GenderTrait, PageViewConditionTrait;

    public function index(Request $request): View
    {
        $languages = $this->getLanguages();
        $genders = $this->genders();
        $devices = Device::all(['id', 'name']);
        $pageTypes = PageType::all(['id', 'name']);
        $browsers = Browser::all(['id', 'name']);
        $platforms = Platform::all(['id', 'name']);

        $top10ViewedPages = $this->getTop10ViewedPages($request);
        $totalViews = $this->getTotalViews($request);
        $totalRegisteredUsersViews = $this->getTotalViews($request, 'no');

        $platformCounts = Platform::select(['id', 'name'])
            ->withCount([
                'PageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $browserCounts = Browser::select(['id', 'name'])
            ->withCount([
                'PageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $deviceCounts = Device::select(['id', 'name'])
            ->withCount([
                'PageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $pageTypeCounts = PageType::select(['id', 'name'])
            ->withCount([
                'PageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $totalGuestViews = $this->getTotalViews($request, 'yes');
        $totalViewBasedOnLanguage = $this->getTotalViewBasedOnLanguage($request);

        return view('admin.analytics.sitewide.index', compact('languages', 'genders', 'pageTypes', 'devices', 'platforms', 'browsers', 'top10ViewedPages', 'totalViews', 'totalRegisteredUsersViews', 'totalGuestViews', 'platformCounts', 'browserCounts', 'deviceCounts', 'pageTypeCounts', 'totalViewBasedOnLanguage'));
    }

    private function getTop10ViewedPages($request): Collection
    {
        $query = PageView::selectRaw('page_url, title, COUNT(*) AS visit_count')->groupBy('page_url', 'title')->orderByDesc('visit_count')->limit(10);

        $query = $this->filterPageViews($query, $request);

        return $query->get();
    }

    private function getTotalViews($request, $isGuest = null): float
    {
        $query = PageView::query();

        if ($isGuest) {
            if ($isGuest == 'yes') {
                $query->whereNull('user_id');
            } else {
                $query->whereNotNull('user_id');
            }
        }

        $query = $this->filterPageViews($query, $request);

        return $query->count();
    }

    private function getTotalViewBasedOnLanguage($request): Collection
    {
        $totalResources = PageView::where(function ($query) use ($request) {
            return $this->filterPageViews($query, $request);
        })
            ->groupBy('language')
            ->select('language', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->get();

        return $totalResources->map(function ($item) {
            $item['language'] = LanguageEnum::tryFrom($item['language'])?->name ?? $item['language'];
            return $item;
        });
    }

    public function viewResource(Request $request)
    {
        $languages = $this->getLanguages();
        $genders = $this->genders();
        $devices = Device::all(['id', 'name']);
        $pageTypes = PageType::all(['id', 'name']);
        $browsers = Browser::all(['id', 'name']);
        $platforms = Platform::all(['id', 'name']);

        $query = PageView::query()->with(['platform:id,name', 'device:id,name', 'browser:id,name', 'user:id', 'user.profile:id,user_id,first_name,last_name']);
        $query = $this->filterPageViews($query, $request);   
        $views = $query->paginate()
            ->appends($request->except(['page']));

        return view('admin.analytics.sitewide.get-views', compact(['views', 'languages', 'genders', 'devices','pageType', 'browsers', 'platforms']));
    }
}
