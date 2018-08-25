@extends("la.layouts.app")
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Timesheets</a> :
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
            <div class="col-md-10 col-md-offset-1">
                {!! Form::model($timesheet, ['route' => [config('laraadmin.adminRoute') . '.timesheets.update', $timesheet->id ], 'method'=>'PUT', 'id' => 'timesheet-edit-form']) !!}
                <div id="entry_parent">
                    <div class="entry" id="1">
                         <div class="row">
                            <div class="col-md-3">
                                @la_input($module, 'project_id')
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="task_id">Task Name:</label>
                                    <select class="form-control" name="task_id">
                                        @foreach($tasks as $task)
                                        <option value="{{$task->task_id}}" <?php echo (($task->task_id == $module->row->task_id) ? 'selected' : ''); ?>>{{$task->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
<!--                        </div>
                        <div class="row">-->
                            <div class ="col-md-6">
                                @la_input($module, 'comments')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                @la_input($module, 'date')
                            </div>
                            <div class="col-md-2">
                                @la_input($module, 'hours')
                            </div>
                            <div class="col-md-2">
                                @la_input($module, 'minutes')
                            </div>
                            <div class ="col-md-6">
                                @la_input($module, 'remarks')
                            </div>
                        </div>
                        <div class="hide">
                            @la_input($module, 'dependency')
                            @la_input($module, 'dependency_for')
                            @la_input($module, 'dependent_on')
                            <div class="form-group">
                                <label for="lead_id">Lead Name:</label>
                                <select class="form-control" name="lead_id">
                                    @foreach($leads as $lead)
                                    <option value="{{$lead->lead_id}}" data-mail = "{{$lead->lead_email}}">{{$lead->lead_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="manager_id">Manager Name:</label>
                                <select class="form-control" name="manager_id">
                                    @foreach($managers as $manager)
                                    <option value="{{$manager->manager_id}}" data-mail = "{{$manager->manager_email}}">{{$manager->manager_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success pull-left']) !!} 
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="overlay">
    <div class="loader"/>
</div>
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
    $("#project-edit-form").validate({

    });

    //hide stuff on page load
    $('div.overlay').hide();
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
    $('[name="dependency"][value="No"]').trigger('click');

    $('.date').data("DateTimePicker").minDate(moment().startOf('week'));
    $('.date').data("DateTimePicker").maxDate(moment().endOf('week')).daysOfWeekDisabled([0, 6]);
});
</script>
@endpush
