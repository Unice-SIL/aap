import '../scss/app.scss';

require('./custom')
require('admin-lte');
require('admin-lte/plugins/jquery-knob/jquery.knob.min');
require('bootstrap');
require('datatables.net-bs4');
require('moment/locale/fr');
require('tempusdominus-bootstrap-4');
require('jquery-ui/ui/widgets/sortable');
require('select2');
require('select2/dist/js/i18n/fr');
require('../../public/bundles/tetranzselect2entity/js/select2entity');


$(document).ready(function () {

    /**
     * Init
     */
    $(document).on('init', function () {
        initDateTimePicker();
        initSortable();
        bsCustomFileInput.init()
        $('.select2entity[data-autostart="true"]').select2entity();
    });

    $(document).trigger('init');

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
     * DataTable
     */
    let dataTables = $('.dataTable');

    dataTables.each(function () {
        let table = $(this);
        let thIndexes = [];
        table.find('th').each(function (index) {

            if ($(this).data('no-filter') === true) {
                thIndexes.push(index);
            }
        });

        let dataTable = table.DataTable({
            language: {
                url: table.data('translation-url')
            },
            "columnDefs": [
                { "orderable": false, "targets": thIndexes }
            ],
        });
    });


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

        modal.find('#form-container').addClass('loader-active');
        $.get(url).done(function (html) {
            modal.find('#form-container').html(html).hide().fadeIn(500);
            modal.find('#form-container').removeClass('loader-active');
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
     * CallOfProjectInformationType
     */
    var $initProject = $('#call_of_project_information_initProject');
    // When initProject gets selected ...
    $initProject.change(function() {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected initProject value.
        var data = {};
        data[$initProject.attr('name')] = $initProject.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {
                // Replace current #init-call-of-project-by field ...
                $('#init-call-of-project-by').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#init-call-of-project-by')
                );
                $(document).trigger('init');
            }
        });
    });


    /**
     * jQuery Knob
     */
    $('.knob-render').knob();
    $('.knob-render-primary').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--primary')
    });
    $('.knob-render-secondary').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--secondary')
    });
    $('.knob-render-info').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--info')
    });
    $('.knob-render-warning').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--warning')
    });
    $('.knob-render-danger').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--danger')
    });

    /**
     * Batch action
     */
    $('.batch-input').click(function () {

        let input = $(this);
        let form = $(input.data('form-target'));
        let batchActionButton = form.find('.main-button');
        let entitiesField = form.find('select.entities-field')

        if (input.is(':checked')) {
            let newOption = entitiesField.data('prototype');
            newOption = newOption.replace(/__VALUE__/g, input.val());
            newOption = newOption.replace(/__LABEL__/g, input.val());
            newOption = $(newOption);
            newOption.appendTo(entitiesField);
        } else {
            let elementToRemove = entitiesField.find('option[value="' + input.val() + '"]')
            elementToRemove.remove();
        }

        batchActionButton.disable(entitiesField.find('option').length < 1);
    })


});