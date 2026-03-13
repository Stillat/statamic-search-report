<?php

namespace Stillat\StatamicSearchReport\Http\Controllers;

use App\Http\Controllers\Controller;
use Stillat\StatamicSearchReport\Http\Resources\SearchLogResource;
use Stillat\StatamicSearchReport\Models\SearchTermLog;

class SearchReportController extends Controller
{
    public function index()
    {
        $sortableColumns = [
            'index', 'term', 'search_frequency', 'result_count', 'subsequent_page_count', 'site',
        ];

        $sortCol = 'result_count';
        $sortOrder = 'asc';
        $perPage = request()->input('perPage', 10);
        $search = request()->input('search');
        $query = SearchTermLog::query();

        if ($sortParam = request()->input('sort')) {
            if (in_array($sortParam, $sortableColumns)) {
                $sortCol = $sortParam;
            }
        }

        if ($orderParam = request()->input('order')) {
            if (in_array($orderParam, ['asc', 'desc'])) {
                $sortOrder = $orderParam;
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('term', 'like', '%'.$search.'%');
            });
        }

        return new SearchLogResource(
            $query->orderBy($sortCol, $sortOrder)->paginate($perPage)
        )
        ->columnPreferenceKey('searchReport.columns');
    }
}
