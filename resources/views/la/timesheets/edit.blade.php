@extends("la.layouts.app")
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Edit Timesheet Entry</a>
@endsection
<?php
//echo $module->row->id;die;
// echo "<pre>"; print_r($module->row->id);die;
?>
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
<!--            <div class="col-md-10 col-md-offset-1">-->
                  <div class="col-md-12">
                {!! Form::model($timesheet, ['route' => [config('laraadmin.adminRoute') . '.timesheets.update', $timesheet->id ], 'method'=>'PUT', 'id' => 'timesheet-edit-form']) !!}
                <div id="entry_parent">
                    <div class="entry" id="1">
                         <div class="row">
                              <div class="col-md-2">
                                @la_input($module, 'date')
                            </div>
                             
                             
                             
                             
                            <div class="col-md-2">
                                @la_input($module, 'project_id')
                            </div>
                            <div class="col-md-2">
                                    <label for="task_id">Task Name:</label>
                                    <select class="form-control" name="task_id">
                                        @foreach($tasks as $task)
                                        <option value="{{$task->task_id}}" <?php echo (($task->task_id == $module->row->task_id) ? 'selected' : ''); ?>>{{$task->name}}</option>
                                        @endforeach
                                    </select>
                                    
<!--                                </div>-->
                            </div>
                              <div class="col-md-3">
                               @la_input($module, 'comments')
                               
                            </div>
                             
                               <div class="col-md-1">
                                @la_input($module, 'hours')
                            </div>
                            <div class="col-md-1">
                                @la_input($module, 'minutes')
                            </div>
                             <div class="col-md-1" style="margin-top: 20px;">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success pull-left']) !!} 
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
	
	$("#timesheet-edit-form input[type='submit']").click(function (e) {
        e.preventDefault();
        if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
            swal("Number of hours for a task cannot exceed more than 24 hrs!");
            return false;
        } else {
            $.ajax({
                method: "POST",
                url: "{{ url('/hoursWorked') }}",
                data: {type: 'day', date: $('.date>input').val(), task_removed: {{ $timesheet->id}}, _token : "{{ csrf_token()}}"}
            }).success(function (totalHours) {
                if ((parseFloat(totalHours) + parseFloat($('[name="hours"]').val()) + parseFloat($('[name="minutes"]').val()/60)) > 24) {
                    swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                    return false;
                } else {
                    $('#timesheet-edit-form').submit();
                }
            });
        }
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
    $('[name="dependency"][value="No"]').trigger('click');

    $('.date').data("DateTimePicker").minDate(moment().subtract(7, 'days').millisecond(0).second(0).minute(0).hour(0));
    $('.date').data("DateTimePicker").daysOfWeekDisabled([0]);
//    $('.date').data("DateTimePicker").maxDate(moment());
});
</script>
@endpush
