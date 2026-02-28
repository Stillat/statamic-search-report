<?php

namespace Stillat\StatamicSearchReport\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Stillat\StatamicSearchReport\Logger;
use Stillat\StatamicSearchReport\ServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        Logger::resetScheduled();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('search_report.store_searches', true);
        $app['config']->set('search_report.ignore_authenticated_users', true);
        $app['config']->set('search_report.database.connection', 'testing');
        $app['config']->set('search_report.database.table', 'search_term_logs');
    }

    protected function setUpDatabase(): void
    {
        Schema::connection('testing')->create('search_term_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('index', 250);
            $table->string('term', 300);
            $table->string('site', 300);
            $table->bigInteger('search_frequency')->default(0);
            $table->bigInteger('subsequent_page_count')->default(0);
            $table->bigInteger('result_count')->default(0);
        });
    }

    protected function runTerminatingCallbacks(): void
    {
        $app = app();
        $app->terminate();
    }
}
