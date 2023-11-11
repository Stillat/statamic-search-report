<?php

namespace Stillat\StatamicSearchReport\Tags;

use Statamic\Facades\Site;
use Statamic\Tags\Tags;
use Stillat\StatamicSearchReport\Logger;

class LogSearch extends Tags
{
    public function index()
    {
        $term = $this->params->get('term', null) ?? request($this->params->get('query', 'q'));

        if (! $term) {
            return;
        }

        $index = $this->params->get('index', 'default');
        $site = $this->params->get('site', null) ?? Site::current()->handle();
        $resultCount = $this->params->get('result_count', null) ?? $this->context['total_items'] ?? 0;
        $pageNum = $this->params->get('page', null) ?? $this->context['current_page'] ?? 1;

        Logger::log($index, $term, $site, $pageNum, $resultCount);
    }
}
