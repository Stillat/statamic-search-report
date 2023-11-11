<?php

namespace Stillat\StatamicSearchReport\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearSearchTermLogs extends Command
{
    protected $signature = 'search-report:clear-search-term-logs';

    protected $description = 'Clear the search term logs database table.';

    public function handle()
    {
        DB::connection(config('search_report.database.connection', 'mysql'))
            ->table(config('search_report.database.table', 'search_term_logs'))
            ->truncate();

        $this->info('Search term logs cleared.');
    }
}
