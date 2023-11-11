<?php

namespace Stillat\StatamicSearchReport;

use Exception;
use Illuminate\Support\Facades\Log;
use Stillat\StatamicSearchReport\Models\SearchTermLog;

class Logger
{
    public static function log(string $index, string $term, string $site, int $pageNum, int $resultCount): void
    {
        if (! config('search_report.store_searches')) {
            return;
        }

        if (auth()->user() && config('search_report.ignore_authenticated_users', true)) {
            return;
        }

        app()->terminating(function () use ($index, $term, $site, $pageNum, $resultCount) {
            // Wrap this in a try/catch to prevent any exceptions from bubbling up.
            // We'll just log the exception and move on, since this is a non-critical
            // thing to do, and we don't want to break the site if it fails.
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
                    $resultCountIncrement = $pageNum > 1 ? 0 : $resultCount;

                    $log->update([
                        'search_frequency' => $log->search_frequency + $searchFrequencyIncrement,
                        'result_count' => $log->result_count + $resultCountIncrement,
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
        });
    }
}
