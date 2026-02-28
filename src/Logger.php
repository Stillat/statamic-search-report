<?php

namespace Stillat\StatamicSearchReport;

use Exception;
use Illuminate\Support\Facades\Log;
use Stillat\StatamicSearchReport\Models\SearchTermLog;

class Logger
{
    protected static array $scheduled = [];

    public static function log(string $index, string $term, string $site, int $pageNum, int $resultCount): void
    {
        if (! config('search_report.store_searches')) {
            return;
        }

        if (auth()->user() && config('search_report.ignore_authenticated_users', true)) {
            return;
        }

        $key = "{$index}:{$term}:{$site}";

        if (in_array($key, static::$scheduled, true)) {
            return;
        }

        static::$scheduled[] = $key;

        app()->terminating(function () use ($index, $term, $site, $pageNum, $resultCount) {
            static::processLog($index, $term, $site, $pageNum, $resultCount);
        });
    }

    public static function processLog(string $index, string $term, string $site, int $pageNum, int $resultCount): void
    {
        try {
            $log = SearchTermLog::on(config('search_report.database.connection', 'mysql'))
                ->firstOrNew([
                    'index' => $index,
                    'term' => $term,
                    'site' => $site,
                ]);

            if ($log->exists) {
                $subsequentPageCountIncrement = $pageNum > 1 ? 1 : 0;
                $searchFrequencyIncrement = $pageNum > 1 ? 0 : 1;

                $log->update([
                    'search_frequency' => $log->search_frequency + $searchFrequencyIncrement,
                    'result_count' => $pageNum > 1 ? $log->result_count : $resultCount,
                    'subsequent_page_count' => $log->subsequent_page_count + $subsequentPageCountIncrement,
                ]);
            } else {
                $initialSubsequentPageCount = $pageNum > 1 ? 1 : 0;

                $log->fill([
                    'search_frequency' => 1,
                    'result_count' => $resultCount,
                    'index' => $index,
                    'site' => $site,
                    'term' => $term,
                    'subsequent_page_count' => $initialSubsequentPageCount,
                ]);

                $log->save();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage(), [
                'index' => $index,
                'term' => $term,
                'site' => $site,
                'pageNum' => $pageNum,
                'resultCount' => $resultCount,
            ]);
        }
    }

    public static function resetScheduled(): void
    {
        static::$scheduled = [];
    }
}
