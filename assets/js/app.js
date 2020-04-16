import '../scss/app.scss';

require('admin-lte');
require('bootstrap');
require('datatables.net-bs4');

$(document).ready(function () {

    $('.dataTable').DataTable();

    /**
     * Widget modal
     */
    $('#widget-form-modal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal
        let modal = $(this);
        let data = {
            widgetName: button.data('widget-name'),
        };
        let url = modal.data('url');

        $.get(url, data).done(function (html) {
            modal.find('#form-container').html(html);
        });
    });

    /**
     * Project List by Call of Project Datatable
     */
    $('#dataTable-projects-list').DataTable({
        language: {
            url: $('#dataTable-projects-list').data('translation-url')
        }
    });

    /**
     * Widget modal
     */
    $('#widget-form-modal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal
        let modal = $(this);
        let url = button.data('url');

        $.get(url).done(function (html) {
            modal.find('#form-container').html(html);
        });
    });

    /**
     *
     * Call of Project form admin
     * todo: move to a specific file
     * ==================================================
     */
    /*let $fromTemplate = $('#call_of_project_fromTemplate');
    // When fromTemplate changed
    $fromTemplate.change(function() {

        // ... retrieve the corresponding form.
        let $form = $(this).closest('form');
        // Simulate form data, but only include fromTemplate value.
        let data = {};
        data[$fromTemplate.attr('name')] = $fromTemplate.is(':checked');

        // Submit data via AJAX to the form's action path.
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {
                // Replace current projectFormLayout field ...
                $('#call_of_project_projectFormLayout').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#call_of_project_projectFormLayout')
                );
            }
        });
    });*/
    /** ================================================== */
});