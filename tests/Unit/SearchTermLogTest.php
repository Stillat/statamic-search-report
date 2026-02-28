<?php

use Stillat\StatamicSearchReport\Models\SearchTermLog;

it('uses the configured table name', function () {
    $model = new SearchTermLog;

    expect($model->getTable())->toBe('search_term_logs');
});

it('uses a custom table name from config', function () {
    config(['search_report.database.table' => 'custom_search_logs']);

    $model = new SearchTermLog;

    expect($model->getTable())->toBe('custom_search_logs');
});

it('has the correct fillable attributes', function () {
    $model = new SearchTermLog;

    expect($model->getFillable())->toBe([
        'index',
        'term',
        'search_frequency',
        'result_count',
        'subsequent_page_count',
        'site',
    ]);
});
