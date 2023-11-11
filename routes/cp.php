<?php

use Illuminate\Support\Facades\Route;

Route::get('search-report', '\Stillat\StatamicSearchReport\Http\Controllers\SearchReportController@index');
