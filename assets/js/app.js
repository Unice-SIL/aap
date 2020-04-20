import '../scss/app.scss';

require('admin-lte');
require('bootstrap');
require('datatables.net-bs4');

$(document).ready(function () {

    /**
     * CollectionType js
     * ================================================================
     */
    //Add a listener on remove activity button
    $(document).on('click', '.remove-collection-widget', function(e) {
        e.preventDefault();
        var item = $(this).closest('.item-collection');
        let list = item.closest('ul');
        item.slideUp(500, function() {
            $(this).remove();

            let number = 1;
            $('.item-collection').each(function () {
                $(this).find('.item-collection-number').text(number);
                number++;
            });

            let collection = list.find('.item-collection');
            if (collection.length <= 0) {
                list.siblings('.no-item').slideDown();
            }
        });

    });

    //Add a listener on add activity button
    $(document).on('click', '.add-another-collection-widget', function (e) {
        var list = $($(this).attr('data-list-selector'));

        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);

        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.hide().appendTo(list).slideDown();

        let number = 1;
        $('.item-collection').find('.item-collection-number').each(function () {
            $(this).text(number);
            number++;
        });

        $([document.documentElement, document.body]).animate({
            scrollTop: newElem.offset().top
        }, 500);

        if (list.length > 0) {
            list.siblings('.no-item').slideUp();
        }

        $(this).trigger('add-item-collection');

    });
    /**
     * End CollectionType js
     * ================================================================
     */


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
    /**
     * End Call of Project form admin
     * ==================================================
     * */
});