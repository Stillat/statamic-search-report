<template>
    <div>
        <div v-if="initializing" class="card loading">
            <loading-graphic />
        </div>

        <data-list
            v-if="!initializing"
            ref="dataList"
            :columns="columns"
            :rows="items"
        >
            <div>
                <div class="card overflow-hidden p-0 relative">
                    <div v-show="items.length === 0" class="p-6 text-center text-gray-500" v-text="__('No results')" />

                    <div class="overflow-x-auto overflow-y-hidden">
                        <data-list-table
                            v-show="items.length"
                            :loading="loading"
                        >
                        </data-list-table>
                    </div>
                </div>

                <data-list-pagination
                    class="mt-6"
                    :resource-meta="meta"
                    :per-page="perPage"
                    :show-totals="true"
                    @page-selected="selectPage"
                    @per-page-changed="changePerPage"
                />
            </div>
        </data-list>
    </div>
</template>
 
<script>
export default {
    mixins: [window.Listing],
    props: [

    ],
    data() {
        return {
            listingKey: 'searchReport',
            preferencesPrefix: 'searchReport',
            requestUrl: cp_url('search-report'),
            meta: {},
            columns: [
                { label: __('statamic-search-report::search_report.index'), field: 'index', visible: true, sortable: true, },
                { label: __('statamic-search-report::search_report.term'), field: 'term', visible: true, sortable: true, },
                { label: __('statamic-search-report::search_report.search_frequency'), field: 'search_frequency', visible: true, sortable: true, },
                { label: __('statamic-search-report::search_report.subsequent_page_frequency'), field: 'subsequent_page_count', visible: true, sortable: true, },
                { label:  __('statamic-search-report::search_report.result_count'), field: 'result_count', visible: true, sortable: true, },
                { label:  __('statamic-search-report::search_report.last_searched'), field: 'updated_at', visible: true, sortable: true, },
            ],
        };
    },
    methods: {
    },
    created() {
    }
};
</script>