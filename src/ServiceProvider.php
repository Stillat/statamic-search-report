<?php

namespace Stillat\StatamicSearchReport;

use Statamic\Facades\Utility;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Console\ClearSearchTermLogs::class,
        Console\SearchTermLogsTable::class,
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $tags = [
        Tags\LogSearch::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/search_report.php', 'search_report');

        $this->publishes([
            __DIR__.'/../config/search_report.php' => config_path('search_report.php'),
        ], 'statamic-search-report-config');
    }

    public function bootAddon()
    {
        view()->addNamespace('search-report', __DIR__.'/../resources/views');

        Utility::extend(function () {
            Utility::register('search_report')
                ->view('search-report::search_report')
                ->title(trans('statamic-search-report::search_report.search_report'))
                ->icon('search')
                ->description(trans('statamic-search-report::search_report.search_report_desc'));
        });
    }
}
