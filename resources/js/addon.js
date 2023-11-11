import SearchReport from './control-panel/SearchReport.vue';

Statamic.booting(() => {
    Statamic.$components.register('search-report', SearchReport);
});
