function init() {

    //datepicker conditions
    $('.date').data("DateTimePicker").minDate(moment('2016-08-29'));
//    $('.date').data("DateTimePicker").maxDate(moment());

}

function update_row(saved_data, id) {
    $('tr.entry-row input').val('');
    $('table#entry_table tbody.entry_body').append(
            '<tr class="recent-entry">' +
            '<td class="name">' + saved_data.name + '</td>' +
            '<td class="start_date" data-value="' + (new Date(saved_data.start_date)).toShortFormat() + '">' + (new Date(saved_data.start_date)).toShortFormat() + '</td>' +
            '<td class="end_date" data-value="' + (new Date(saved_data.end_date)).toShortFormat() + '">' + (new Date(saved_data.end_date)).toShortFormat() + '</td>' +
            '<td>' +
            '<button class="btn btn-success update-entry submit-form" data-value=' + id + '><i class="fa fa-edit"></i></button>' +
            '<button class="btn btn-danger delete-entry submit-form" data-value=' + id + '><i class="fa fa-times"></i></button>' +
            '</td>' +
            '</tr>'
            );
}

function show_update_row(el) {
    var parent = el.parents('tr.recent-entry');
    $('tr.entry-row input#name').val(parent.find('td.name').html());
    $('tr.entry-row input#start_date').val(parent.find('td.start_date').html());
    $('tr.entry-row input#end_date').val(parent.find('td.end_date').html());
    $('tr.entry-row').find('.submit-form').removeClass('add-entry').addClass('update-entry-db').attr('data-value', parent.find('button.update-entry').attr('data-value'));
    $('.update-entry-db').find('i').addClass('fa-edit').removeClass('fa-plus');
    parent.remove();
}