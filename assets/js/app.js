import '../scss/app.scss';

require('jquery');
require('bootstrap');

$(document).ready(function () {

    /**
     *
     * Call of Project form admin
     * todo: move to a specific file
     * ==================================================
     */
    let $fromTemplate = $('#call_of_project_fromTemplate');
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
    });
    /** ================================================== */
});