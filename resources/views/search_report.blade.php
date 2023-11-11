@extends('statamic::layout')
@section('title', trans('statamic-search-report::search_report.search_report'))

@section('content')
	@include('statamic::partials.breadcrumb', [
        'url' => cp_route('utilities.index'),
        'title' => __('Utilities')
    ])
	<div class="flex mb-6">
		<h1 class="flex-1">{{ trans('statamic-search-report::search_report.search_report') }}</h1>
	</div>

	<search-report></search-report>
@stop