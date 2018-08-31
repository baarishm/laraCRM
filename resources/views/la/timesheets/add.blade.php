@extends("la.layouts.app")
<style>
    input[type="search"].form-control.input-sm{
        float : right;
        margin:5px;
        border:none;
        border-bottom: 1px solid #9a9999;
        font-weight: 400;
    }

</style>
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
    <div class="box-header">

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['action' => 'LA\TimesheetsController@store', 'id' => 'timesheet-add-form']) !!}
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
                                        <option data-name="{{$task->name}}" value="{{$task->task_id}}">{{$task->name}}</option>
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
                <input type="hidden" name="task_removed" id="task_removed" value="{{$task_removed}}" />
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Add Entry', ['class'=>'btn btn-success pull-left']) !!} 
                </div>
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
    $("#timesheet-add-form input[type='submit']").click(function (e) {
        e.preventDefault();
        if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
            swal("Number of hours for a task cannot exceed more than 24 hrs!");
            return false;
        } else {
            $.ajax({
                method: "POST",
                url: "{{ url('/hoursWorked') }}",
                data: {type: 'day', date: $('.date>input').val(), _token: "{{ csrf_token()}}"}
            }).success(function (totalHours) {
                if ((parseFloat(totalHours) + parseFloat($('[name="hours"]').val()) + parseFloat($('[name="minutes"]').val() / 60)) > 24) {
                    swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                    return false;
                } else {
                    $('#timesheet-add-form').submit();
                }
            });
        }
    });

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


    $('[name="date"]').parent('.date').on('dp.change', function (e) {
        console.log(new Date($(this).children('input').val()));
        console.log(new Date());
        if (new Date($(this).children('input').val()) > new Date()) {
            $('select[name="task_id"] option').addClass('hide');
            $('select[name="task_id"]').find('option[data-name="Leave"]').removeClass('hide').attr('selected', true);
        } else {
            $('select[name="task_id"] option').removeClass('hide');
        }
    });

    $('.date').data("DateTimePicker").minDate(moment().subtract(7, 'days').millisecond(0).second(0).minute(0).hour(0));
    $('.date').data("DateTimePicker").daysOfWeekDisabled([0]);
//    $('.date').data("DateTimePicker").maxDate(moment());
});
</script>
@endpush
