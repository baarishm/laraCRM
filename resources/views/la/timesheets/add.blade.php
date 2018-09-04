@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Add Timesheet Entry</a>
@endsection
@section("section", "Timesheets")
@section("section_url", url(config('laraadmin.adminRoute') . '/timesheets'))


@section("main-content")

@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="box entry-form">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <table id="entry_table">
                    <thead class="entry-header">
                        <tr>
                            <th style="width: 16%;">Date<span class="required">*</span></th>
                            <th style="width: 20%;">Project<span class="required">*</span></th>
                            <th style="width: 20%;">Task<span class="required">*</span></th>
                            <th>Description</th>
                            <th>Hours<span class="required">*</span></th>
                            <th>Minutes<span class="required">*</span></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="entry_body">
                        <tr class="entry-row">
                            <td>
                                <div class="input-group date">
                                    <input class="form-control" placeholder="Enter Date" required name="date" id="date" type="text" value="" autocomplete="off">
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select class="form-control" name="project_id" id="project_id" required>
                                    @foreach($projects as $project)
                                    <option data-name="{{$project->name}}" value="{{$project->id}}">{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="task_id" id="task_id" required>
                                    @foreach($tasks as $task)
                                    <option data-name="{{$task->name}}" value="{{$task->task_id}}">{{$task->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control" placeholder="Enter Description" name="comments" id="comments" type="text" value="">
                            </td>
                            <td>
                                <select class="form-control" name="hours" id="hours" required>
                                    @for($i = 1; $i <= 24 ; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="minutes" id="minutes" required>
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary add-entry submit-form" data-value=''><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="submitor_id" id="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function () {
    var removeable_options = $('select[name="task_id"] option[data-name!="Leave"]').detach();
    init(removeable_options);
    //form submition
    $(document).on('click', 'button.submit-form', function () {
        var send_data = {
            _token: "{{ csrf_token() }}",
            project_id: $('select#project_id').val(),
            task_id: $('select#task_id').val(),
            date: $('#date').val(),
            comments: $('#comments').val(),
            hours: $('#hours').val(),
            minutes: $('#minutes').val(),
            submitor_id: $('#submitor_id').val(),
        };
        var saved_data = {
            _method: "POST",
            _token: "{{ csrf_token() }}",
            project_id: $('select#project_id').val(),
            task_id: $('select#task_id').val(),
            project_name: $('select#project_id option:selected').attr('data-name'),
            task_name: $('select#task_id option:selected').attr('data-name'),
            date: $('#date').val(),
            comments: $('#comments').val(),
            hours: $('#hours').val(),
            minutes: $('#minutes').val()
        };
        var el = $(this);

        if (el.hasClass('add-entry') || el.hasClass('update-entry-db')) {
            var url = "{{ url(config('laraadmin.adminRoute') . '/timesheets') }}";
            var method = "POST";
            if (el.hasClass('update-entry-db')) {
                url = "{{ url(config('laraadmin.adminRoute') . '/timesheets') }}" + "/" + el.attr('data-value') + "?_method=PUT";
                method = "PUT";
            }
            saved_data['_method'] = method;
            if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
                swal("Number of hours for a task cannot exceed more than 24 hrs!");
                return false;
            } else {
                if (validateFields($('[required]'))) {
                    $('div.overlay').removeClass('hide');
                    $.ajax({
                        method: "POST",
                        url: "{{ url('/hoursWorked') }}",
                        data: {type: 'day', date: $('.date>input').val(), _token: "{{ csrf_token()}}", task_removed: el.attr('data-value')}
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
                    swal('Please fill all required fields!');
                }
            }
        } else if (el.hasClass('update-entry')) {
            if ($('tr.entry-row button.submit-form').hasClass('update-entry-db')) {
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
                data: {_token: "{{ csrf_token() }}", id: el.attr('data-value'), ajax: true, _method: 'DELETE'},
                success: function () {
                    $('div.overlay').addClass('hide');
                    parent_row.remove();
                    swal('Row deleted successfully!');
                }
            });
        }
    });

});
</script>

<script src="{{ asset('la-assets/js/pages/timesheets.js') }}"></script>
@endpush
