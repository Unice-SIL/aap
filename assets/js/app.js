import '../scss/app.scss';

require('admin-lte');
require('bootstrap');
require('datatables.net-bs4');
require('moment/locale/fr');
require('tempusdominus-bootstrap-4');
require('jquery-ui/ui/widgets/sortable');


$(document).ready(function () {

    /**
     * Init
     */
    $(document).on('init', function () {
        initDateTimePicker();
        initSortable();
        bsCustomFileInput.init()
    });

    $(document).trigger('init');

    /**
     * Sortable
     */
    function initSortable () {
        $( '.sortable' ).sortable({
            appendTo: document.body
        });

        $( '#active-widget-container' ).sortable( "option", "handle", ".sortable-handle" );
    }

    $( '#active-widget-container' ).on( "sortupdate", function( event, ui ) {
        let item = ui.item;

        $.post(
            item.data('url'),
            { 'position': item.parent().children().index(item) + 1 },
            function () {
                console.log('ok');
            }
        );
    } );

    /**
     * Datetime picker (or only Date)
     */
    function initDateTimePicker() {
        $('input.datetimepicker-input[data-linked-target]').each(function() {

            let first = $(this).closest('.dateimepicker-container');
            let id = $(this).data('linked-target');
            let second = $('input.datetimepicker-input[data-linked-id=' + id + ']').closest('.dateimepicker-container');


            first.datetimepicker({
                allowInputToggle: false,
                sideBySide: true
            });
            second.datetimepicker({
                allowInputToggle: false,
                useCurrent: false,
                sideBySide: true
            });
            first.on("change.datetimepicker", function (e) {
                second.datetimepicker('minDate', e.date);
            });
            second.on("change.datetimepicker", function (e) {
                first.datetimepicker('maxDate', e.date);
            });

        });

        $('.datetimepicker-input').each(function () {
            let element = $(this).closest('.dateimepicker-container');
            element.datetimepicker({
                sideBySide: true
            });
        });

        $('.datepicker-input').each(function () {
            let element = $(this).closest('.dateimepicker-container');
            element.datetimepicker({
                format: 'L',
                sideBySide: true
            });
        });
    }

    /**
     * Flash message => toastr
     */
    $('.flash-message').each(function () {
        let label = $(this).data('label');
        let message = $(this).data('message');

        if (typeof toastr[label] === 'function') {
            toastr[label](message);
        }

    });

    /**
     * alert modal
     */
    $(document).on('click', '.alert-button-modal', function (e) {
        e.preventDefault();

        let modal = $('#alert-form-modal');
        let title = $(this).data('modal-title');
        let alertLabel = $(this).data('modal-warning-label');
        let alertClass = $(this).data('modal-warning-class');

        let form = $(this).closest('form').clone().removeAttr('class').addClass('d-inline') ;
        form.find('.alert-button-modal').removeAttr('class').addClass(alertClass).text(alertLabel);

        modal.find('.modal-title').text(title);
        let body = modal.find('.modal-body');
        body.find('form').remove();
        body.append(form);
        modal.modal('show');
    });

    /**
     * CollectionType js
     * ================================================================
     */
    //Add a listener on remove activity button
    $(document).on('click', '.remove-collection-widget', function(e) {
        e.preventDefault();
        let item = $(this).closest('.item-collection');
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
        let list = $($(this).attr('data-list-selector'));

        // Try to find the counter of the list or use the length of the list
        let counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        let newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);

        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
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
    let dataTables = $('.dataTable');

    dataTables.each(function () {

        let dataTable = $(this);
        dataTable.DataTable({
            language: {
                url: dataTable.data('translation-url')
            }
        });
    });


    /**
     * Call of Project edit modal
     */
    $('#call-of-project-information-form-modal').modal({
        backdrop: 'static'
    });

    /**
     * Widget modal
     */
    $('#widget-form-modal').modal({
        backdrop: 'static',
        show: false
    });

    $('#widget-form-modal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal
        let modal = $(this);
        let url = button.data('url');

        $.get(url).done(function (html) {
            modal.find('#form-container').html(html);
        });
    });

    /**
     * Widget Form
     */
    $(document).on('submit', '.form-widget', function (e) {
        e.preventDefault();

        let form = $(this);

        $.post(form.attr('action'), form.serialize(), function (html) {

            let element = $(html);

            if (element.hasClass('form-widget')) {

                $('.form-widget').replaceWith(element);
                return;
            }

            if (element.data('widget-edit')) {
                $('#' + element.attr('id')).replaceWith(element);
            } else {
                $('#active-widget-container').append(element);
            }

            $('#widget-form-modal').modal('hide');

            $([document.documentElement, document.body]).animate({
                scrollTop: element.offset().top - $('#main-navbar').outerHeight()
            }, 500);

            $(document).trigger('init');
        })
    });

    /**
     *
     * Call of Project form admin
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