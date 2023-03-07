import '../scss/app.scss';

require('admin-lte');
require('admin-lte/plugins/bootstrap-switch/js/bootstrap-switch');
require('admin-lte/plugins/jquery-knob/jquery.knob.min');
require('admin-lte/plugins/summernote/summernote-bs4');
require('admin-lte/plugins/summernote/lang/summernote-fr-FR');
require('jquery-csv');
require('bootstrap');
require('datatables.net-bs4');
require('datatables.net-responsive-bs4');
require('moment/locale/fr');
require('tempusdominus-bootstrap-4');
require('jquery-ui/ui/widgets/sortable');
require('select2');
require('select2/dist/js/i18n/fr');
require('../../public/bundles/tetranzselect2entity/js/select2entity');
require('./custom')

$(document).ready(function () {
    /**
     * Init
     */
    $(document).on('init', function () {
        initDateTimePicker();
        initSortable();
        initSelect2();
        initSummernote();
        initBootstrapSwitch();
        bsCustomFileInput.init()
        $('.select2entity[data-autostart="true"]').select2entity();
    });

    $(document).trigger('init');

    /**
     * Bootstrap switch
     */
    function initBootstrapSwitch() {
        $('.bootstrap-switch').bootstrapSwitch()
    }

    /**
     * Summernote
     */
    function initSummernote() {
        $('.summernote').each(function () {
            $(this).summernote({
                lang: 'fr-FR',
                placeholder: $(this).attr('placeholder'),
                fontNames: ['Apex New Book', 'Apex New Light', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana']
            })
        });
    }

    /**
     * Select2
     */
    function initSelect2() {
        $('.select-2').each(function () {
            $(this).select2({
                ajax: {
                    url: $(this).data('url'),
                    dataType: 'json'
                },
                minimumInputLength: $(this).data('minimum-input-length'),
                language: $(this).data('language'),
                multiple: $(this).data('multiple'),
            })

        });
    }

    /**
     * Datetime picker (or only Date)
     */
    function initDateTimePicker() {
        $('input.datetimepicker-input[data-linked-target]').each(function () {

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
     * Flash message => toastr/Swal
     */
    $('.flash-message').each(function () {
        let label = $(this).data('label');
        let message = $(this).data('message');

        if (typeof Swal[label] === 'function') {
            Swal.fire({
                'html': message,
                'confirmButtonColor': getComputedStyle(document.documentElement).getPropertyValue('--primary')
            });
        }

        if (typeof toastr[label] === 'function') {
            toastr[label](message);
        }

    });

    /**
     * Alert modal
     */
    $(document).on('click', '.alert-button-modal', function (e) {
        e.preventDefault();

        let modal = $('#alert-form-modal');
        let title = $(this).data('modal-title');
        let alertLabel = $(this).data('modal-warning-label');
        let alertClass = $(this).data('modal-warning-class');

        let form = $(this).closest('form').clone().removeAttr('class').addClass('d-inline');
        form.find('.alert-button-modal').removeAttr('class').addClass(alertClass).text(alertLabel);

        modal.find('.modal-title').text(title);
        let body = modal.find('.modal-body');
        body.find('form').remove();
        body.find('.dismiss-modal').before(form);
        modal.modal('show');
    });

    /**
     * CollectionType js
     * ================================================================
     */
    //Add a listener on remove activity button
    $(document).on('click', '.remove-collection-widget', function (e) {
        e.preventDefault();
        let item = $(this).closest('.item-collection');
        let list = item.closest('ul');
        item.slideUp(500, function () {
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

    function addItemToList(list) {
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

        return newElem;
    }

    //Add a listener on add activity button
    $(document).on('click', '.add-another-collection-widget', function (e) {
        let list = $($(this).attr('data-list-selector'));

        let newElem = addItemToList(list)

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
        let defaultThOrder = 0;
        table.find('th').each(function (index) {

            if ($(this).data('no-filter') === true) {
                thIndexes.push(index);
            } else if (defaultThOrder === 0) {
                defaultThOrder = index;
            }

        });

        let dataTable = table.DataTable({
            language: {
                url: table.data('translation-url')
            },
            "order": [[defaultThOrder, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": thIndexes}
            ],
            "dom": '<"row"' +
                '<"col-sm-12 col-md-6"l>' +
                '<"col-sm-12 col-md-6"f>' +
                '<"col-12"t>' +
                '<"col-12"B>' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
        });
    });


    /**
     * Sortable
     */
    function initSortable() {
        $('.sortable').sortable({
            appendTo: document.body
        });

        $('#active-widget-container').sortable("option", "handle", ".sortable-handle");
    }

    $('#active-widget-container').on("sortupdate", function (event, ui) {
        let item = ui.item;

        $.post(
            item.data('url'),
            {'position': item.parent().children().index(item) + 1}
        );
    });

    /**
     * Call of Project edit modal
     */
    $('#call-of-project-information-form-modal').modal({
        backdrop: 'static'
    });

    /**
     * Project Validation/Refusal modal
     */
    $('.validation-form-modal').modal({
        backdrop: 'static',
        show: false
    });

    /**
     * Widget modal
     */
    function chargeFormOnShowBsModal() {
        $(this).modal({
            backdrop: 'static',
            show: false
        });

        $(this).on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget); // Button that triggered the modal
            let modal = $(this);
            let url = button.data('url');

            modal.find('#form-container').addClass('loader-active');
            $.get(url).done(function (html) {
                modal.find('#form-container').html(html).hide().fadeIn(500);
                modal.find('#form-container').removeClass('loader-active');
                $(document).trigger('init');

                if (modal.find('#form_choice_widget_dictionary').length > 0) {
                    toggleOptionContainer(modal.find('#form_choice_widget_dictionary'));
                }
            });
        });


    }

    let formOnShowBSModal = [$('#import-widget-modal'), $('#widget-form-modal')]
    $.each(formOnShowBSModal, chargeFormOnShowBsModal);

    /**
     * Widget Form
     */
    $(document).on('submit', '.form-widget', function (e) {
        e.preventDefault();

        let form = $(this);
        form.find('.submit-button').attr("disabled", true).addClass('disabled');

        $.post(form.attr('action'), form.serialize(), function (html) {

            let element = $(html);

            if (element.find('form.form-widget').length > 0) {
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

    function toggleOptionContainer(dictionarySelect) {
        let optionContainer = dictionarySelect.closest('form').find('.options-container');

        let inputs = optionContainer.map(function () {
            return $(this).find('.item-collection').find('input');
        });


        if (dictionarySelect.val() === '') {
            optionContainer.show()
            inputs.each(function () {
                $(this).attr('required', 'required');
            })
        } else {
            optionContainer.hide()
            inputs.each(function () {
                $(this).removeAttr('required');
            })
        }
    }

    $(document).on('change', '#form_choice_widget_dictionary', function () {
        toggleOptionContainer($(this))
    })

    /**
     * CallOfProjectInformationType
     */
    let $initProject = $('#call_of_project_information_initProject');
    // When initProject gets selected ...
    $initProject.change(function () {
        // ... retrieve the corresponding form.
        let $form = $(this).closest('form');
        // Simulate form data, but only include the selected initProject value.
        let data = {};
        data[$initProject.attr('name')] = $initProject.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
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
     * Project/ValidationType
     */
    $(document).on('switchChange.bootstrapSwitch', 'form.validation-form .automatic-sending-switch', function (e, state) {
        let $automaticSending = $(this);
        // ... retrieve the corresponding form.
        let $form = $(this).closest('form');
        let $action = $form.find('[name="validation[action]"]')
        console.log(data);
        // Simulate form data, but only include the selected automaticSending value.
        let data = {};
        data[$automaticSending.attr('name')] = state;
        data[$action.attr('name')] = $action.val();

        // Submit data via AJAX to the form's action path.
        $form.find('.submit-button').attr("disabled", true).addClass('disabled');
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                let containerSelector = 'form[name ="' + $form.attr('name') + '"]' + ' .mail-template-container';
                $(containerSelector).replaceWith(
                    $(html).find(containerSelector)
                );
                initSummernote();
                $form.find('.submit-button').attr("disabled", false).removeClass('disabled');
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
    $('.knob-render-success').knob({
        'fgColor': window.getComputedStyle(document.body).getPropertyValue('--success')
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
    });

    $(document).on('change', '.toggle-multiple-checkboxes', function () {
        let element = $(this);
        $('input[type="checkbox"]').filter('[data-toggle-element="' + element.attr('id') + '"]').each(function () {
            $(this).prop("checked", element.is(":checked"));
        });
    });

    /**
     *  Help
     */
    let timeoutReference;
    let helpSearchElement = $('#help-search');

    helpSearchElement.on('paste keyup', function () {
        if (helpSearchElement.val().length < 2) {
            return;
        }
        clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function () {
            $.ajax({
                'url': helpSearchElement.data('action'),
                'data': {
                    'terms': helpSearchElement.val()
                },
                'beforeSend': function () {
                    $('#help-search-prepend').html(
                        '<i class="fas fa-circle-notch fa-spin"></i>'
                    );
                },
                'success': function (data) {
                    $('#help-results').html(data);
                },
                'complete': function () {
                    $('#help-search-prepend').html(
                        '<i class="fas fa-search"></i>'
                    );
                }
            });
        }, 650);
    });

    $(document).on('click', '#help-more-results-button', function () {
        $(this).hide();
    })

    /**
     * Dynamic import widget form modal
     */

    $(document).on('change', '#import_widget_callOfProject', function () {
        let $form = $(this).closest('form');
        let data = {};
        data[$(this).attr('name')] = $(this).val();
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {

                $('#import_widget_projectFormWidget').replaceWith(
                    $(html).find('#import_widget_projectFormWidget')
                );

                $(document).trigger('init');

            }
        });
    });

    $(document).on('submit', 'form[name="import_widget"]', function () {
        $(this).find('button').disable(true);
    })

    /**
     * Aap Delete Form
     */
    $('.delete-aap-form').find('.name-field').keyup(function () {
        let button = $(this).closest('form').find('button');
        button.attr('disabled', 'disabled');

        if ($(this).val() === $(this).data('name')) {
            button.removeAttr('disabled');
        }
    });

    /**
     * Load widget's choices from csv
     */
    $(document).on('change', '#form_choice_widget_file', function (e) {
        let data = null;
        let file = e.target.files[0];
        let reader = new FileReader();
        let input = $(this);

        if (file === undefined) {
            return;
        }

        let splitFileName = file.name.split('.');
        if (splitFileName[splitFileName.length - 1] !== 'csv') {
            toastr.error(input.data('wrong-format-message'));
            return;
        }

        reader.readAsText(file);

        reader.onload = function (event) {
            let csvData = event.target.result;
            data = $.csv.toArrays(csvData);
            if (data && data.length > 0) {
                let list = $(input.attr('data-list-selector'));
                list.find('li').remove();
                data.forEach(function (element) {
                    addItemToList(list).find('.input-text-choice').val(element[0]);
                })
            } else {
                toastr.error(input.data('no-data-message'));
            }
        };

        reader.onerror = function () {
            toastr.error(input.data('error-message'));
        };


    });

    $(document).on('click', '#callOfProjectSubmissionUrlClipboard i', function () {
        let callOfProjectSubmissionUrl = document.getElementById("callOfProjectSubmissionUrl");
        callOfProjectSubmissionUrl.select();
        callOfProjectSubmissionUrl.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");
        toastr.success('URL copiée');
    });

    $(document).on('submit', 'form[name="project_form_layout"]', function (e) {
        e.preventDefault();
        const url = $('#button_edit_title').data('url');
        $.ajax({
            type: "POST",
            url: url,
            data:   $('form[name="project_form_layout"]').serialize(),
            success: function (response) {
                if(response.statut) {
                    $('#edit_title_form_modal').modal('hide');
                    $("#dynamic_widgets_name").prev("label").html(response.newLabel);
                }
            },
            error: function () {
                alert('Le label n\'a pas pu être modifié');
            }
        });
    });
    })

    let $helpNewCallOfProjectModal = $('#help-new-call-of-project-modal');
    if ($helpNewCallOfProjectModal.length) {
        $helpNewCallOfProjectModal.modal('show');
    }
});
