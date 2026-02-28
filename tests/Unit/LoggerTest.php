<?php

use Illuminate\Support\Facades\Auth;
use Stillat\StatamicSearchReport\Logger;
use Stillat\StatamicSearchReport\Models\SearchTermLog;

it('creates a search term log record', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);

    $log = SearchTermLog::on('testing')->first();

    expect($log)->not->toBeNull()
        ->and($log->index)->toBe('default')
        ->and($log->term)->toBe('test query')
        ->and($log->site)->toBe('en')
        ->and($log->search_frequency)->toBe(1)
        ->and($log->result_count)->toBe(5)
        ->and($log->subsequent_page_count)->toBe(0);
});

it('increments search_frequency on repeated first-page searches', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'en', 1, 5);

    $log = SearchTermLog::on('testing')->first();

    expect($log->search_frequency)->toBe(2);
});

it('stores the latest result_count instead of accumulating', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'en', 1, 8);

    $log = SearchTermLog::on('testing')->first();

    expect($log->result_count)->toBe(8);
});

it('increments subsequent_page_count for paginated requests', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'en', 2, 5);

    $log = SearchTermLog::on('testing')->first();

    expect($log->subsequent_page_count)->toBe(1);
});

it('does not increment search_frequency for paginated requests', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'en', 2, 5);

    $log = SearchTermLog::on('testing')->first();

    expect($log->search_frequency)->toBe(1);
});

it('does not change result_count for paginated requests', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'en', 2, 5);

    $log = SearchTermLog::on('testing')->first();

    expect($log->result_count)->toBe(5);
});

it('skips logging when store_searches config is false', function () {
    config(['search_report.store_searches' => false]);

    Logger::log('default', 'test query', 'en', 1, 5);
    $this->runTerminatingCallbacks();

    expect(SearchTermLog::on('testing')->count())->toBe(0);
});

it('skips logging for authenticated users when ignore_authenticated_users is true', function () {
    $user = new class extends \Illuminate\Foundation\Auth\User
    {
        public $exists = true;

        public function getAuthIdentifier()
        {
            return 1;
        }
    };

    Auth::shouldReceive('user')->andReturn($user);

    config(['search_report.ignore_authenticated_users' => true]);

    Logger::log('default', 'test query', 'en', 1, 5);
    $this->runTerminatingCallbacks();

    expect(SearchTermLog::on('testing')->count())->toBe(0);
});

it('logs for authenticated users when ignore_authenticated_users is false', function () {
    $user = new class extends \Illuminate\Foundation\Auth\User
    {
        public $exists = true;

        public function getAuthIdentifier()
        {
            return 1;
        }
    };

    Auth::shouldReceive('user')->andReturn($user);

    config(['search_report.ignore_authenticated_users' => false]);

    Logger::log('default', 'test query', 'en', 1, 5);
    $this->runTerminatingCallbacks();

    expect(SearchTermLog::on('testing')->count())->toBe(1);
});

it('prevents double-fire within the same request', function () {
    Logger::log('default', 'test query', 'en', 1, 5);
    Logger::log('default', 'test query', 'en', 1, 5);
    $this->runTerminatingCallbacks();

    $log = SearchTermLog::on('testing')->first();

    expect(SearchTermLog::on('testing')->count())->toBe(1)
        ->and($log->search_frequency)->toBe(1);
});

it('creates separate records for different search terms', function () {
    Logger::processLog('default', 'first query', 'en', 1, 3);
    Logger::processLog('default', 'second query', 'en', 1, 7);

    expect(SearchTermLog::on('testing')->count())->toBe(2);

    $first = SearchTermLog::on('testing')->where('term', 'first query')->first();
    $second = SearchTermLog::on('testing')->where('term', 'second query')->first();

    expect($first->result_count)->toBe(3)
        ->and($second->result_count)->toBe(7);
});

it('creates separate records for different indexes', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('blog', 'test query', 'en', 1, 3);

    expect(SearchTermLog::on('testing')->count())->toBe(2);
});

it('creates separate records for different sites', function () {
    Logger::processLog('default', 'test query', 'en', 1, 5);
    Logger::processLog('default', 'test query', 'fr', 1, 3);

    expect(SearchTermLog::on('testing')->count())->toBe(2);
});
