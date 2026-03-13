import SearchReport from './control-panel/SearchReport.vue';

Statamic.booting(() => {
    Statamic.$inertia.register('search-report::SearchReport', SearchReport);
});
