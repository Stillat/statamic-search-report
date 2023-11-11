<?php

namespace Stillat\StatamicSearchReport\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchLogResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $searchLogs = parent::toArray($request);
        $dateFormat = config('statamic.cp.date_format', 'Y-m-d');

        foreach ($searchLogs as $index => $log) {
            $searchLogs[$index]['updated_at'] = Carbon::parse($log['updated_at'])->format($dateFormat);
            $searchLogs[$index]['created_at'] = Carbon::parse($log['created_at'])->format($dateFormat);
        }

        return $searchLogs;
    }

    public function with(Request $request)
    {
        return [
            'meta' => [
                'columns' => [
                    [
                        'label' => trans('statamic-search-report::search_report.index'),
                        'field' => 'index',
                        'visible' => true,
                        'sortable' => true,
                    ],
                    [
                        'label' => trans('statamic-search-report::search_report.term'),
                        'field' => 'term',
                        'visible' => true,
                        'sortable' => true,
                    ],
                    [
                        'label' => trans('statamic-search-report::search_report.search_frequency'),
                        'field' => 'search_frequency',
                        'visible' => true,
                        'sortable' => true,
                    ],
                    [
                        'label' => trans('statamic-search-report::search_report.subsequent_page_frequency'),
                        'field' => 'subsequent_page_count',
                        'visible' => true,
                        'sortable' => true,
                    ],
                    [
                        'label' => trans('statamic-search-report::search_report.result_count'),
                        'field' => 'result_count',
                        'visible' => true,
                        'sortable' => true,
                    ],
                    [
                        'label' => trans('statamic-search-report::search_report.last_searched'),
                        'field' => 'updated_at',
                        'visible' => true,
                        'sortable' => true,
                    ],
                ],
            ],
        ];
    }
}
