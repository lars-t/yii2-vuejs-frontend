Vue.component('grid-summary', {
    template: '<span v-html="summary"></span>',
    style: '',
    data: function () {
        return {};
    },
    props: {
        summary: String
    }
}
)