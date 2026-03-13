<?php

namespace Stillat\StatamicSearchReport\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Statamic\CP\Column;
use Statamic\Facades\Blueprint;
use Statamic\Http\Resources\CP\Concerns\HasRequestedColumns;

class SearchLogResource extends ResourceCollection
{
    use HasRequestedColumns;

    protected $blueprint;

    protected $columns;

    protected $columnPreferenceKey;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->blueprint = Blueprint::makeFromFields([]);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        $searchLogs = parent::toArray($request);
        $dateFormat = config('statamic.cp.date_format', 'Y-m-d');

        foreach ($searchLogs as $index => $log) {
            $searchLogs[$index]['updated_at'] = Carbon::parse($log['updated_at'])->format($dateFormat);
            $searchLogs[$index]['created_at'] = Carbon::parse($log['created_at'])->format($dateFormat);
        }

        return $searchLogs;
    }

    public function blueprint($blueprint)
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function columnPreferenceKey($key)
    {
        $this->columnPreferenceKey = $key;

        return $this;
    }

    public function setColumns()
    {
        $columns = $this->blueprint->columns();

        $index = Column::make('index')
            ->label(trans('statamic-search-report::search_report.index'))
            ->visible(true)
            ->sortable(true);

        $term = Column::make('term')
            ->label(trans('statamic-search-report::search_report.term'))
            ->visible(true)
            ->sortable(true)
            ->required(true);

        $searchFrequency = Column::make('search_frequency')
            ->label(trans('statamic-search-report::search_report.search_frequency'))
            ->visible(true)
            ->sortable(true);

        $subsequentPageCount = Column::make('subsequent_page_count')
            ->label(trans('statamic-search-report::search_report.subsequent_page_frequency'))
            ->visible(true)
            ->sortable(true);

        $resultCount = Column::make('result_count')
            ->label(trans('statamic-search-report::search_report.result_count'))
            ->visible(true)
            ->sortable(true);

        $lastSearched = Column::make('updated_at')
            ->label(trans('statamic-search-report::search_report.last_searched'))
            ->visible(true)
            ->sortable(true);

        $columns->put('index', $index);
        $columns->put('term', $term);
        $columns->put('search_frequency', $searchFrequency);
        $columns->put('subsequent_page_count', $subsequentPageCount);
        $columns->put('result_count', $resultCount);
        $columns->put('updated_at', $lastSearched);

        if ($key = $this->columnPreferenceKey) {
            $columns->setPreferred($key);
        }

        $this->columns = $columns->rejectUnlisted()->values();
    }

    public function with($request)
    {
        $this->setColumns();

        return [
            'meta' => [
                'columns' => $this->visibleColumns(),
            ],
        ];
    }
}
