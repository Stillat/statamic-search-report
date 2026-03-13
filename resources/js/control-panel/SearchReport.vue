<script setup>
import { Head } from '@statamic/cms/inertia';
import {
    Header,
    Listing,
    ListingSearch,
    ListingCustomizeColumns,
    ListingTable,
    Panel,
    ListingPagination,
} from '@statamic/cms/ui';

const listingKey = 'searchReport';
const preferencesPrefix = 'searchReport';
const requestUrl = cp_url('search-report');
</script>

<template>
    <Head title="Search Report" />
    <Header icon="map-search" :title="__('statamic-search-report::search_report.search_report')" />
    <Listing
        :url="requestUrl"
        :preferences-prefix="preferencesPrefix"
        :listing-key="listingKey"
        :columns="columns"
    >
        <div class="relative flex flex-1 items-center gap-3 mb-2">
            <ListingSearch />
            <ListingCustomizeColumns />
        </div>

        <Panel class="overflow-x-auto">
            <ListingTable />
        </Panel>
        <ListingPagination />
    </Listing>
</template>
