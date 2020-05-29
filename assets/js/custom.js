jQuery.fn.extend({
    disable: function (state) {
        return this.each(function () {
            var $this = $(this);
            $this.toggleClass('disabled', state);
        });
    }
});


$.fn.dataTable.ext.feature.push( {
    fnInit: function ( settings ) {
        return $('div[data-batch-datatable-target="#' + settings.sTableId + '"]'); // input element
    },
    cFeature: 'B'
} );