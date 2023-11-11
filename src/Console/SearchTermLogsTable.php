<?php

namespace Stillat\StatamicSearchReport\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class SearchTermLogsTable extends Command
{
    protected $signature = 'search-report:search-term-logs-table';

    protected $description = 'Create a migration for the search term logs database table.';

    protected Filesystem $files;

    protected Composer $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle()
    {
        $table = config('search_report.database.table', 'search_term_logs');

        $this->replaceMigration(
            $this->createBaseMigration($table), $table
        );

        $this->info('Migration created successfully.');
        $this->composer->dumpAutoloads();
    }

    protected function createBaseMigration($table = 'search_term_logs')
    {
        return $this->laravel['migration.creator']->create(
            'create_'.$table.'_table', $this->laravel->databasePath().'/migrations'
        );
    }

    protected function replaceMigration($path, $table)
    {
        $stub = str_replace(
            '{{table}}', $table, $this->files->get(__DIR__.'/stubs/search_term_logs.stub')
        );

        $this->files->put($path, $stub);
    }
}
