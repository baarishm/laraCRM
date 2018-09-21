function init(removeable_options) {
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
    $('select[name="task_id"]').append(removeable_options);
    $('[name="date"]').parent('.date').on('dp.change', function (e) {
        if (new Date($(this).children('input').val()) > new Date()) {
            $('select[name="task_id"] option[data-name!="Leave"]').detach();
            $('select[name="task_id"] option[data-name="Leave"]').attr('selected', true);
        } else {
            $('select[name="task_id"]').append(removeable_options);
        }
    });


    //datepicker conditions
    $('.date').data("DateTimePicker").minDate(moment().subtract(7, 'days').millisecond(0).second(0).minute(0).hour(0));
    $('.date').data("DateTimePicker").daysOfWeekDisabled([0]);
//    $('.date').data("DateTimePicker").maxDate(moment());

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