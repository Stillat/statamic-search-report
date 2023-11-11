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
        $perPage = request()->get('perPage', 10);

        if ($sortParam = request()->get('sort')) {
            if (in_array($sortParam, $sortableColumns)) {
                $sortCol = $sortParam;
            }
        }

        if ($orderParam = request()->get('order')) {
            if (in_array($orderParam, ['asc', 'desc'])) {
                $sortOrder = $orderParam;
            }
        }

        return new SearchLogResource(
            SearchTermLog::orderBy($sortCol, $sortOrder)->paginate($perPage)
        );
    }
}
