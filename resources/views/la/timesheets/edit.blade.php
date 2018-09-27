@extends("la.layouts.app")
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Edit Timesheet Entry</a>
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
    <div class="box-header">

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                {!! Form::model($timesheet, ['route' => [config('laraadmin.adminRoute') . '.timesheets.update', $timesheet->id ], 'method'=>'PUT', 'id' => 'timesheet-edit-form']) !!}
                
                <table id="entry_table">
                    <thead class="entry-header">
                        <tr>
                            <th style="width: 16%;">Date<span class="required">*</span></th>
                            <th style="width:15%;">Project<span class="required">*</span></th>
                            <th style="width:15%;">Sprint<span class="required">*</span></th>
                            <th style="width:15%;">Task<span class="required">*</span></th>
                            <th><span class="required hide description">*</span>Description</th>
                            <th>Hours<span class="required">*</span></th>
                            <th>Minutes<span class="required">*</span></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="entry_body">
                        <tr class="entry-row">
                            <td>
                                <div class="input-group date">
                                    <input class="form-control" placeholder="Enter Date" required name="date" id="date" type="text" value="{{$module->row->date}}" autocomplete="off">
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select class="form-control" name="project_id" id="project_id" required>
                                    @foreach($projects as $project)
                                    <option data-name="{{$project->name}}" value="{{$project->id}}" <?php echo (($project->id == $module->row->project_id) ? 'selected' : ''); ?>>{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="projects_sprint_id" id="projects_sprint_id" required>
                                    @foreach($projects_sprints as $projects_sprint)
                                    <option data-name="{{$projects_sprint->name}}" value="{{$projects_sprint->id}}" <?php echo (($projects_sprint->id == $module->row->projects_sprint_id) ? 'selected' : ''); ?>>{{$projects_sprint->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="task_id" id="task_id">
                                    @foreach($tasks as $task)
                                    <option value="{{$task->task_id}}" <?php echo (($task->task_id == $module->row->task_id) ? 'selected' : ''); ?>>{{$task->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control" placeholder="Enter Description" name="comments" id="comments" type="text" value="{{$module->row->comments}}" maxlength="250">
                            </td>
                            <td>
                                <select class="form-control" name="hours" id="hours" required>
                                    @for($i = 0; $i <= 24 ; $i++)
                                    <option value="{{$i}}"  <?php echo (($i == $module->row->hours) ? 'selected' : ''); ?>>{{$i}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="minutes" id="minutes" required>
                                    <option value="00"  <?php echo (('00' == $module->row->minutes) ? 'selected' : ''); ?>>00</option>
                                    <option value="30"  <?php echo (('30' == $module->row->minutes) ? 'selected' : ''); ?>>30</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary add-entry submit-form" data-value=''><i class="fa fa-edit"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <input type="hidden" name="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />


                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {

    $("#timesheet-edit-form .submit-form").click(function (e) {
        e.preventDefault();
        if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
            swal("Number of hours for a task cannot exceed more than 24 hrs!");
            $('div.overlay').addClass('hide');
            return false;
        } else {
            $.ajax({
                    method: "POST",
                    url: "{{ url('/hoursWorked') }}",
                    data: {type: 'day', date: $('.date>input').val(), task_removed: {{ $timesheet -> id}}, _token : "{{ csrf_token()}}"}
            }).success(function (totalHours) {
                if ((parseFloat(totalHours) + parseFloat($('[name="hours"]').val()) + parseFloat($('[name="minutes"]').val() / 60)) > 24) {
                    swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                    $('div.overlay').addClass('hide');
                    return false;
                } else {
                    if(validateFields($('[required]'))){
                        $('#timesheet-edit-form').submit();
                    }
                    else{
                        $('div.overlay').addClass('hide');
                        swal('Please fill all required fields!');
                    }
                }
            });
        }
    });
    
     //to get project against date selected
    $('.date').on('dp.change', function () {
        var date = dateFormatDB($(this).find('input').val());
        $.ajax({
            url: "{{url(config('laraadmin.adminRoute') . '/projectList')}}",
            method: 'POST',
            data: {_token: "{{ csrf_token() }}", date: date}
        }).success(function (project_list) {
            $('select#project_id option').remove();
            $(project_list).each(function (key, item) {
                $('select#project_id').append('<option data-name="'+item.name+'" value="' + item.id + '">' + item.name + '</option>');
            });
        });
        $('#project_id').trigger('change');
    });

    //to get sprint against project selected
    $('#project_id').on('change', function () {
        var date = dateFormatDB($('[name="date"]').val());
        $.ajax({
            url: "{{url(config('laraadmin.adminRoute') . '/sprintList')}}",
            method: 'POST',
            data: {_token: "{{ csrf_token() }}", date: date, project_id: $('#project_id').val()}
        }).success(function (sprint_list) {
            $('select#projects_sprint_id option').remove();
            $(sprint_list).each(function (key, item) {
                $('select#projects_sprint_id').append('<option data-name="'+item.name+'" value="' + item.id + '">' + item.name + '</option>');
            });
        });
        $('#task_id').trigger('change');
    });

    //hide stuff on page load
    $('[for="dependency_for"], [for="dependent_on"]').parents('div.form-group').fadeOut('slow');
    
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
    
    //initialize select2
    $('#project_id, #projects_sprint_id, #task_id').select2();
    
    $('[name="dependency"][value="No"]').trigger('click');
    $('.date').data("DateTimePicker").minDate(moment().subtract(7, 'days').millisecond(0).second(0).minute(0).hour(0));
    $('.date').data("DateTimePicker").daysOfWeekDisabled([0]);
    
    
    //maxlength of comment
    $('[name="comments"]').prop('maxlength', '250');
    
    $('#task_id').on('change', function(){
        if(($('#project_id').find('option:selected').html() == "Internal") || ($('#project_id').find('option:selected').html() == "Pipeline") || ($('#task_id').find('option:selected').html() == "Research and Development")){
            $('[name="comments"]').attr('required', true);
            $('label[for="comments"]').html('Description*:');
        }
        else{
             $('[name="comments"]').attr('required', false);
            $('label[for="comments"]').html('Description:');
        }
    });
    
    $('#project_id').trigger('change');
});
</script>
@endpush
