function init(removeable_options, leave) {
    //Initialize dropdowns
    $('select').select2();

    //hide stuff on page load
    $('[for="dependency_for"], [for="dependent_on"]').parents('div.form-group').fadeOut('slow');

    //show entry form on add entry button click
    $("#add-entry").click(function (event) {
        event.preventDefault();
        $('.entry-form').show();
        $("#add-entry").hide();
    });

    //show hide dependency based boxes and values
    $('[name="dependency"]').change(function () {
        if ($('[name="dependency"]:checked').val() == 'No') {
            $('[for="dependency_for"], [for="dependent_on"]').parents('div.form-group').fadeOut('slow');
            $('select[name="dependent_on"]').val('');
            $('textarea[name="dependency_for"]').val('');
        } else {
            $('[for="dependency_for"], [for="dependent_on"]').parents('div.form-group').fadeIn('slow');
            $('select[name="dependent_on"]').val($('select[name="dependent_on"] option:first').val());
        }
    });
    $('[name="dependency"][value="No"]').trigger('click');


    //initialize datatable
    $("#example1").DataTable({
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
    });

    // Leave option selection
    $('select[name="task_id"]').append(removeable_options).append(leave);
    $('.date').on('dp.change', function () {

        //to get project against date selected
        var date = dateFormatDB($(this).find('input').val());
        $.ajax({
            url: $('#entry_table').attr('data-url') + '/projectList',
            method: 'POST',
            data: {_token: $('[name="_token"]').val(), date: date}
        }).success(function (project_list) {
            $('select#project_id option').remove();
            $(project_list).each(function (key, item) {
                $('select#project_id').append('<option data-name="' + item.name + '" value="' + item.id + '">' + item.name + '</option>');
            });
        });
        $('#project_id').trigger('change');

        //for leave option in task list
        if (new Date($(this).children('input').val()) > new Date()) {
            $('select[name="task_id"]').append(leave);
            $('select[name="task_id"] option[data-name!="Leave"]').detach();
            $('select[name="task_id"] option[data-name="Leave"]').attr('selected', true);
        } else {
            $('select[name="task_id"]').append(removeable_options);
            $.ajax({
                url: $('#task_id').attr('data-url'),
                method: 'GET',
                data: {date: $(this).find('input').val()},
                success: function (showLeave) {
                    if (showLeave === 'true') {
                        $('select[name="task_id"]').append(leave);
                    } else {
                        $('select[name="task_id"] option[data-name="Leave"]').detach();
                    }
                }
            });
        }
    });

    $('.date').on('dp.show', function () {
        if ($(this).find('input').val() == '') {
            $('.date').data('DateTimePicker').date(moment());
        }
    });

    //datepicker conditions
    $('.date').data("DateTimePicker").minDate(moment().subtract(10, 'days').millisecond(0).second(0).minute(0).hour(0));
//    $('.date').data("DateTimePicker").daysOfWeekDisabled([0]);
//    $('.date').data("DateTimePicker").maxDate(moment());

    //maxlength of comment
    $('[name="comments"]').prop('maxlength', '250');

    //to get sprint against project selected
    $('#project_id').on('change', function () {
        var date = dateFormatDB($('#date').val());
        $.ajax({
            url: $('#entry_table').attr('data-url') + '/sprintList',
            method: 'POST',
            data: {_token: $('[name="_token"]').val(), date: date, project_id: $('#project_id').val()}
        }).success(function (sprint_list) {
            $('select#projects_sprint_id option').remove();
            $(sprint_list).each(function (key, item) {
                $('select#projects_sprint_id').append('<option data-name="' + item.name + '" value="' + item.id + '">' + item.name + '</option>');
            });
        });
        $('#task_id').trigger('change');
    });

    $('#task_id').on('change', function () {
        if (($('#project_id').find('option:selected').html() == "Internal") || ($('#project_id').find('option:selected').html() == "Pipeline") || ($('#task_id').find('option:selected').html() == "Research and Development")) {
            $('[name="comments"]').attr('required', true);
            $('.description').removeClass('hide');
        } else {
            $('[name="comments"]').attr('required', false);
            $('.description').addClass('hide');
        }
    });

}

function update_row(saved_data, id, removeable_options) {
    $('tr.entry-row input').val('');
    $('tr.entry-row select#project_id option:first').prop('selected', true).trigger('change');
    $('tr.entry-row select#task_id option:first').prop('selected', true).trigger('change');
    $('tr.entry-row select#hours option:first').prop('selected', true).trigger('change');
    $('tr.entry-row select#minutes option:first').prop('selected', true).trigger('change');
    $('table#entry_table tbody.entry_body').append(
            '<tr class="recent-entry">' +
            '<td class="date">' + saved_data.date + '</td>' +
            '<td class="project_name" data-value="' + saved_data.project_id + '">' + saved_data.project_name + '</td>' +
            '<td class="projects_sprint_name" data-value="' + saved_data.projects_sprint_id + '">' + saved_data.projects_sprint_name + '</td>' +
            '<td class="task_name" data-value="' + saved_data.task_id + '">' + saved_data.task_name + '</td>' +
            '<td class="comments"><span class="tooltips" title="' + saved_data.comments + '">' + ((saved_data.comments.length > 20) ? (saved_data.comments.substring(0, 20) + '...') : saved_data.comments) + '</span></td>' +
            '<td class="hours">' + saved_data.hours + '</td>' +
            '<td class="minutes">' + saved_data.minutes + '</td>' +
            '<td>' +
            '<button class="btn btn-success update-entry submit-form" data-value=' + id + '><i class="fa fa-edit"></i></button>' +
            '<button class="btn btn-danger delete-entry submit-form" data-value=' + id + '><i class="fa fa-times"></i></button>' +
            '</td>' +
            '</tr>'
            );
    $('select[name="task_id"]').append(removeable_options);
    $('.tooltips').tooltip({'placement': 'bottom'});
}

function show_update_row(el) {
    var parent = el.parents('tr.recent-entry');
    $('tr.entry-row input#date').val(parent.find('td.date').html());
    $('tr.entry-row select#project_id').val(parent.find('td.project_name').attr('data-value')).trigger('change');
    $('tr.entry-row select#projects_sprint_id').val(parent.find('td.projects_sprint_name').attr('data-value')).trigger('change');
    $('tr.entry-row select#task_id').val(parent.find('td.task_name').attr('data-value')).trigger('change');
    $('tr.entry-row input#comments').val(parent.find('td.comments span').attr('data-original-title'));
    $('tr.entry-row select#hours').val(parent.find('td.hours').html()).trigger('change');
    $('tr.entry-row select#minutes').val(parent.find('td.minutes').html()).trigger('change');
    $('tr.entry-row').find('.submit-form').removeClass('add-entry').addClass('update-entry-db').attr('data-value', parent.find('button.update-entry').attr('data-value'));
    $('.update-entry-db').find('i').addClass('fa-edit').removeClass('fa-plus');
    parent.remove();
}

$(document).ready(function () {
    var removeable_options = $('select[name="task_id"] option[data-name!="Leave"]').detach();
    var leave = $('select[name="task_id"] option[data-name="Leave"]').detach();
    init(removeable_options, leave);

    //add form submition
    $(document).on('click', 'button.submit-form.add-entry-form', function () {
        var send_data = {
            _token: $('[name="_token"]').val(),
            project_id: $('select#project_id').val(),
            projects_sprint_id: $('select#projects_sprint_id').val(),
            task_id: $('select#task_id').val(),
            date: $('#date').val(),
            comments: $('#comments').val(),
            hours: $('#hours').val(),
            minutes: $('#minutes').val(),
            submitor_id: $('#submitor_id').val(),
        };
        var saved_data = {
            _method: "POST",
            _token: $('[name="_token"]').val(),
            project_id: $('select#project_id').val(),
            projects_sprint_id: $('select#projects_sprint_id').val(),
            task_id: $('select#task_id').val(),
            project_name: $('select#project_id option:selected').attr('data-name'),
            projects_sprint_name: $('select#projects_sprint_id option:selected').attr('data-name'),
            task_name: $('select#task_id option:selected').attr('data-name'),
            date: $('#date').val(),
            comments: $('#comments').val(),
            hours: $('#hours').val(),
            minutes: $('#minutes').val()
        };
        var el = $(this);

        if (el.hasClass('add-entry') || el.hasClass('update-entry-db')) {
            var url = $('#entry_table').attr('data-url') + '/timesheets';
            var method = "POST";
            if (el.hasClass('update-entry-db')) {
                url = $('#entry_table').attr('data-url') + '/timesheets' + "/" + el.attr('data-value') + "?_method=PUT";
                method = "PUT";
            }
            saved_data['_method'] = method;
            if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
                $('div.overlay').addClass('hide');
                swal("Number of hours for a task cannot exceed more than 24 hrs!");
                return false;
            } else {
                if (validateFields($('[required]'))) {
                    $('div.overlay').removeClass('hide');
                    $.ajax({
                        method: "POST",
                        url: $('#entry_table').attr('data-workHours'),
                        data: {type: 'day', date: $('.date>input').val(), _token: $('[name="_token"]').val(), task_removed: el.attr('data-value')}
                    }).success(function (totalHours) {
                        condition = (parseFloat(totalHours) + parseFloat($('[name="hours"]').val()) + parseFloat($('[name="minutes"]').val() / 60));

                        if (condition > 24) {
                            $('div.overlay').addClass('hide');
                            swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                            return false;
                        } else {
                            $.ajax({
                                method: "POST",
                                url: url,
                                data: send_data,
                                success: function (id) {
                                    update_row(saved_data, id, removeable_options);
                                    $('tr.entry-row').find('.submit-form').addClass('add-entry').removeClass('update-entry-db').attr('data-value', '');
                                    $('.add-entry').find('i').removeClass('fa-edit').addClass('fa-plus');
                                    $('div.overlay').addClass('hide');
                                }
                            });
                        }
                    });
                } else {
                    $('div.overlay').addClass('hide');
                    swal('Please fill all required fields!');
                }
            }
        } else if (el.hasClass('update-entry')) {
            if ($('tr.entry-row button.submit-form').hasClass('update-entry-db')) {
                $('div.overlay').addClass('hide');
                swal('Submit last row first!');
                return false;
            }
            show_update_row(el);
        } else if (el.hasClass('delete-entry')) {
            var parent_row = el.parents('tr.recent-entry');
            $('div.overlay').removeClass('hide');
            $.ajax({
                method: 'POST',
                url: "{{ url(config('laraadmin.adminRoute') . '/timesheets') }}" + "/" + el.attr('data-value'),
                data: {_token: $('[name="_token"]').val(), id: el.attr('data-value'), ajax: true, _method: 'DELETE'},
                success: function () {
                    $('div.overlay').addClass('hide');
                    parent_row.remove();
                    swal('Row deleted successfully!');
                }
            });
        }
    });

    //edit form
    $("#timesheet-edit-form .submit-form").click(function (e) {
        e.preventDefault();
        if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
            swal("Number of hours for a task cannot exceed more than 24 hrs!");
            $('div.overlay').addClass('hide');
            return false;
        } else {
            $.ajax({
                method: "POST",
                url: $('#entry_table').attr('data-workHours'),
                data: {type: 'day', date: $('.date>input').val(), task_removed: $('#timesheet_id').val(), _token: $('[name="_token"]').val()}
            }).success(function (totalHours) {
                if ((parseFloat(totalHours) + parseFloat($('[name="hours"]').val()) + parseFloat($('[name="minutes"]').val() / 60)) > 24) {
                    swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                    $('div.overlay').addClass('hide');
                    return false;
                } else {
                    if (validateFields($('[required]'))) {
                        $('#timesheet-edit-form').submit();
                    } else {
                        $('div.overlay').addClass('hide');
                        swal('Please fill all required fields!');
                    }
                }
            });
        }
    });

    if ($("#timesheet-edit-form").length == 0) {
        $('.date').trigger('dp.change');
    }
});