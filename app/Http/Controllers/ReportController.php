<?php

namespace App\Http\Controllers;

use Analytics;
use App\Models\Resource;
use App\Models\ResourceLevel;
use App\Models\ResourceSubjectArea;
use App\Models\TaxonomyTerm;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Analytics\Period;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function ddlReport(): View
    {
        return view('admin.reports.ddl_resource_report');
    }

    public function ddlReportData(Request $request): View
    {
        $supportedLocales = \LaravelLocalization::getSupportedLocales();

        $request->validate([
            'type' => 'required|in:unique_resources,subject_coverage,translated_adopted,partner_contributions',
            'language' => 'required|in:all,'.implode(',', array_keys($supportedLocales)),
        ]);

        $type = $request->type;
        $language = $request->language;
        $selectedLanguage = $language == 'all' ? null : $language;
        $languageName = $selectedLanguage ? ($supportedLocales[$selectedLanguage]['name'] ?? $selectedLanguage) : 'All languages';
        $resource = new Resource;

        if ($type == 'unique_resources') {
            $totalResources = Resource::when($selectedLanguage, function ($query) use ($selectedLanguage) {
                return $query->where('language', $selectedLanguage);
            })->count();
            $resourcesByLanguage = $resource->totalResourcesByLanguage($selectedLanguage);
            $resourcesBySubject = $resource->totalResourcesBySubject($selectedLanguage);
            $resourcesByType = $resource->totalResourcesByType($selectedLanguage);

            return view('admin.reports.ddl_resource_report_data', compact('type', 'languageName', 'totalResources', 'resourcesByLanguage', 'resourcesBySubject', 'resourcesByType'));
        } elseif ($type == 'translated_adopted') {
            $translated = Resource::whereNotNull('tnid')->whereColumn('tnid', '!=', 'id')
                ->when($selectedLanguage, function ($query) use ($selectedLanguage) {
                    return $query->where('language', $selectedLanguage);
                });
            $adopted = Resource::where('primary_tnid', true)
                ->when($selectedLanguage, function ($query) use ($selectedLanguage) {
                    return $query->where('language', $selectedLanguage);
                });
            $translatedCount = (clone $translated)->count();
            $adoptedCount = (clone $adopted)->count();
            $translatedUtilization = $this->percentage($translatedCount, (clone $translated)->where('views_count', '>', 0)->count());
            $adoptedUtilization = $this->percentage($adoptedCount, (clone $adopted)->where('views_count', '>', 0)->count());

            $translatedLanguagePairs = DB::table('resources AS translated')
                ->join('resources AS original', 'original.id', '=', 'translated.tnid')
                ->whereColumn('translated.id', '!=', 'translated.tnid')
                ->when($selectedLanguage, function ($query) use ($selectedLanguage) {
                    return $query->where('translated.language', $selectedLanguage);
                })
                ->selectRaw('original.language AS from_language, translated.language AS to_language, COUNT(DISTINCT translated.id) AS total')
                ->groupBy('original.language', 'translated.language')
                ->get();

            $adoptedLanguagePairs = DB::table('resources AS original')
                ->join('resources AS translated', 'translated.tnid', '=', 'original.id')
                ->where('original.primary_tnid', true)
                ->whereColumn('translated.id', '!=', 'translated.tnid')
                ->when($selectedLanguage, function ($query) use ($selectedLanguage) {
                    return $query->where('original.language', $selectedLanguage);
                })
                ->selectRaw('original.language AS from_language, translated.language AS to_language, COUNT(DISTINCT original.id) AS total')
                ->groupBy('original.language', 'translated.language')
                ->get();

            return view('admin.reports.ddl_resource_report_data', compact(
                'type',
                'languageName',
                'translatedCount',
                'adoptedCount',
                'translatedUtilization',
                'adoptedUtilization',
                'translatedLanguagePairs',
                'adoptedLanguagePairs'
            ));
        } elseif ($type == 'partner_contributions') {
            $partnerResources = Resource::whereNotNull('partner')->where('partner', '!=', '')
                ->when($selectedLanguage, function ($query) use ($selectedLanguage) {
                    return $query->where('language', $selectedLanguage);
                });
            $partnerCount = (clone $partnerResources)->count();
            $partnerUtilization = $this->percentage($partnerCount, (clone $partnerResources)->where('views_count', '>', 0)->count());
            $partners = (clone $partnerResources)
                ->selectRaw('partner, COUNT(*) AS total, SUM(CASE WHEN views_count > 0 THEN 1 ELSE 0 END) AS utilized')
                ->groupBy('partner')
                ->orderByDesc('total')
                ->get()
                ->each(function ($partner) {
                    $partner->utilization = $this->percentage($partner->total, $partner->utilized);
                });

            return view('admin.reports.ddl_resource_report_data', compact(
                'type',
                'languageName',
                'partnerCount',
                'partnerUtilization',
                'partners'
            ));
        } elseif ($type == 'subject_coverage') {
            $languages = $selectedLanguage ? [$selectedLanguage] : array_keys($supportedLocales);
            $coverageRows = collect();

            foreach ($languages as $code) {
                $subjects = $resource->totalResourcesBySubject($code);
                $totalSubjects = TaxonomyTerm::where('vid', 8)->where('language', $code)->count();
                $coveredSubjects = $subjects->where('total', '>', 10)->count();
                $coverageRows->push((object) [
                    'language' => $supportedLocales[$code]['name'] ?? $code,
                    'totalSubjects' => $totalSubjects,
                    'coveredSubjects' => $coveredSubjects,
                    'coverage' => $this->percentage($totalSubjects, $coveredSubjects),
                ]);
            }
        }

        return view('admin.reports.ddl_resource_report_data', compact('type', 'coverageRows'));
    }

    private function percentage($total, $count): float
    {
        return $total ? round(($count / $total) * 100, 1) : 0;
    }

    private static function subjectSort($a, $b): int
    {
        return $a['count'] <=> $b['count'];
    }

    private static function getSubjects_list($subjects, $language = false): array
    {
        $subjects_list = [];
        foreach ($subjects as $subject) {
            $subjects_list[$subject->id]['id'] = $subject->id;
            $subjects_list[$subject->id]['name'] = $subject->name;
            if ($language) {
                $supported_locales = \LaravelLocalization::getSupportedLocales();
                $subjects_list[$subject->id]['language'] = $supported_locales[$subject->language]['native'];
            }
        }

        foreach ($subjects_list as $subject_id => $name) {
            $subject_resources = ResourceSubjectArea::where('tid', $subject_id)->get();
            $subjects_list[$subject_id]['count'] = $subject_resources->count();
        }

        return $subjects_list;
    }

    public function gaReport(): \Illuminate\View\View
    {
        // retrieve visitors and pageview data for the current day and the last seven days
        $totalVisitorsAndPageViews = Analytics::fetchTotalVisitorsAndPageViews(Period::days(30));
        $mostVisitedPages = Analytics::fetchMostVisitedPages(Period::days(30), 10);
        $topReferrers = Analytics::fetchTopReferrers(Period::days(30), 10);
        $userTypes = Analytics::fetchUserTypes(Period::days(30));
        $topBrowsers = Analytics::fetchTopBrowsers(Period::days(30), 10);
        $topCountries = Analytics::performQuery(Period::days(30), 'ga:sessions');

        return view('admin.reports.reportsga', compact(
            'totalVisitorsAndPageViews',
            'mostVisitedPages',
            'topReferrers',
            'userTypes',
            'topBrowsers'
        ));
    }

    public function resourceReport(): Factory|View|Application
    {
        $supported_locales = \LaravelLocalization::getSupportedLocales();

        return view('admin.reports.resource_reports', compact('supported_locales'));
    }

    public function resourceSubjectReport(Request $request): BinaryFileResponse
    {
        $language = $request->input('lang', 'en');

        $subjects = TaxonomyTerm::where([
            ['vid', '=', 8],  // 8 = Subject
            ['language', '=', $language],
        ])->get();

        $subjects_list = [];
        foreach ($subjects as $subject) {
            $subjects_list[$subject->id] = $subject->name;
        }

        $levels = TaxonomyTerm::where([
            ['vid', '=', 13],  // 13 = Grade level
            ['language', '=', $language],
        ])->get();

        $levels_list = [];
        foreach ($levels as $level) {
            $levels_list[$level->id]['name'] = $level->name;
            $levels_list[$level->id]['count'] = 0;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=theincircle_csv.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $filename = tempnam('/tmp', 'csv_');
        $file = fopen($filename, 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'Subject name',
            'Resource Level',
            '# of resources',
        ]);

        foreach ($subjects_list as $subject_id => $name) {
            $subject_resources = ResourceSubjectArea::where('tid', $subject_id)->get();
            fputcsv($file, [
                $name,
                '',
                $subject_resources->count(),
            ]);

            foreach ($subject_resources as $subject_resource) {
                $levels = ResourceLevel::where('resource_id', '=', $subject_resource->resource_id)->get();
                foreach ($levels as $level) {
                    $levels_list[$level->tid]['count'] += 1;
                }
            }
            foreach ($levels_list as $level_id => $level) {
                if ($level['count'] != 0 && $level['name'] != '') {
                    fputcsv($file, [
                        '',
                        $level['name'],
                        $level['count'],
                    ]);
                }
                $levels_list[$level_id]['count'] = 0;
            }
        }

        fclose($file);

        return response()->download($filename, 'resource_subject_report.csv', $headers);
    }

    public function resourceLanguageReport(): BinaryFileResponse
    {

        $all_languages = Resource::select('language')->distinct()->get();

        $languages_list = [];
        foreach ($all_languages as $language) {
            $languages_list[$language->language]['language_resources_count'] = Resource::where(
                'language', '=', $language->language
            )->get()->count();
            $levels = TaxonomyTerm::where([
                ['vid', '=', 13],  // 13 = Grade level
                ['language', '=', $language->language],
            ])->get();
            foreach ($levels as $level) {
                $levels_count = ResourceLevel::where('tid', '=', $level->id)->get()->count();
                $languages_list[$language->language]['level_resources_count'][$level->name] = $levels_count;
            }
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=theincircle_csv.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $filename = tempnam('/tmp', 'csv_');
        $file = fopen($filename, 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'Language',
            'Resource Level',
            '# of resources',
        ]);

        $supported_locales = \LaravelLocalization::getSupportedLocales();

        foreach ($languages_list as $language => $counts) {
            fputcsv($file, [
                $supported_locales[$language]['native'],
                '',
                $counts['language_resources_count'],
            ]);
            foreach ($counts as $count) {
                if (is_array($count)) {
                    foreach ($count as $level_name => $level_resources_count) {
                        fputcsv($file, [
                            '',
                            $level_name,
                            $level_resources_count,
                        ]);
                    }
                }
            }
        }

        fclose($file);

        return response()->download($filename, 'resource_language_report.csv', $headers);
    }

    public function resourcePriorities(): Factory|View|Application
    {
        $language = app()->getLocale();

        $subjects = TaxonomyTerm::where([
            ['vid', '=', 8],  // 8 = Subject
            ['language', '=', $language],
            ['excluded', '=', false],
        ])->get();

        $subjects_list = self::getSubjects_list($subjects);

        usort($subjects_list, 'self::subjectSort');

        return view('reports.priorities', compact('subjects_list'));
    }

    public function resourcePrioritiesExclusion(): Factory|View|Application
    {
        $subjects = TaxonomyTerm::where([
            ['vid', '=', 8],  // 8 = Subject
            ['excluded', '=', true],
        ])->get();

        $subjects_list = self::getSubjects_list($subjects, true);

        return view('reports.exclusion', compact('subjects_list'));
    }

    public function resourcePrioritiesExclusionModify($id): JsonResponse
    {
        $subject = TaxonomyTerm::where('id', '=', $id)->first();

        if (! $subject) {
            return response()->json(['msg' => 'error'], 400);
        }

        $subject->excluded ? $subject->excluded = false : $subject->excluded = true;
        $subject->save();

        return response()->json(['msg' => 'success']);
    }

    public function impactReport(Request $request)
    {
        $request->validate([
            'from' => 'date|before:to',
            'to' => 'date|after:from',
        ]);
        $from = $request->input('from');
        $to = $request->input('to');
        $resources_count = DB::table('resources')->whereBetween('created_at', [$from, $to])->count();
        $registered_users_count = DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->whereBetween('users.created_at', [$from, $to])
            ->selectRaw("
            count(*) as total_users_count,
            count(case when user_profiles.gender = 'Male' then 1 end) as male_count,
            count(case when user_profiles.gender = 'Female' then 1 end) as female_count,
            count(case when user_profiles.gender = 'None' then 1 end) as undisclosed_count,
            count(case when user_profiles.gender is null then 1 end) as unknown_count
            ")
            ->first();
        $resources_download_count = DB::table('download_counts')->whereBetween('created_at', [$from, $to])->count();

        return view('admin.reports.impact_report', compact('resources_count', ('registered_users_count'), 'resources_download_count'));
    }
}
