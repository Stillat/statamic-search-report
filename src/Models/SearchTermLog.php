<?php

namespace Stillat\StatamicSearchReport\Models;

use Illuminate\Database\Eloquent\Model;

class SearchTermLog extends Model
{
    protected $fillable = [
        'index',
        'term',
        'search_frequency',
        'result_count',
        'subsequent_page_count',
        'site',
    ];

    public function getTable()
    {
        return config('search_report.database.table', 'search_term_logs');
    }
}
