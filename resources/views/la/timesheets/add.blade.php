@extends("la.layouts.app")
<style>
    div.overlay{
        background: none repeat scroll 0 0 #00000026;
        position: absolute;
        display: block;
        z-index: 1000001;
        top: 0;
        height: 100%;
        width: 100%;
        margin-top: 50px;
    }
    .loader {
        position: relative;
        border: 8px solid #7b7b7b;
        border-top: 8px solid #fbfbfb;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        top: 280px;
        left: 520px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


    input[type="search"].form-control.input-sm{
        float : right;
        margin:5px;
        border:none;
        border-bottom: 1px solid #9a9999;
        font-weight: 400;
    }

</style>
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
<!-- for Last records details -->
<div class="box box-success">
    <!--<div class="box-header"></div>-->
    <div class="box-body">
        <table id="example1" class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>Project Name</th>
                    <th>Task Name</th>
                    <th>Time Spent(in hrs)</th>
                    <th>Date</th>
                    <th>Remove Row form this Sheet</th>
                </tr>
            </thead>
            @if(!empty($records))
            @foreach($records as $record)
            <tr class="entry-row" data-value="{{$record->id}}">
                <td>{{$record->project_name}}</td>
                <td>{{$record->task_name}}</td>
                <td>{{$record->hours+($record->minutes/60)}}</td>
                <td>{{date('d M Y',strtotime($record->date))}}</td>
                <td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times"></i></button></td>
            </tr>
            @endforeach
            @endif
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<!-- End -->
<!-- Buttons to add form or to send email -->
<div class="form-group">
    <a href="#" class="btn btn-success " id ="add-entry">Add Entry</a>
    @if(!empty($records))
    <a href="#" class="btn btn-primary" id="send-mail">Send Timesheet Email</a>
    @endif
</div>
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
                                        <option value="{{$task->task_id}}">{{$task->name}}</option>
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
    $("#timesheet-add-form input[type='submit']").click(function (e) {
        e.preventDefault();
        if (($('[name="hours"]').val() == '24') && ($('[name="minutes"]').val() == '30')) {
            swal("Number of hours for a task cannot exceed more than 24 hrs!");
            return false;
        } else {
            $.ajax({
                method: "GET",
                url: "/hoursWorked",
                data: {date: $('.date>input').val()}
            }).success(function (totalHours) {
                if (parseInt(totalHours) > 24) {
                    swal("Number of working hours for a day cannot exceed more than 24 hrs!");
                    return false;
                } else {
                    $('#timesheet-add-form').submit();
                }
            });
        }
    });

    //hide stuff on page load
    $('.entry-form').hide();
    $('div.overlay').hide();
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

    //send mail
    function send_timesheet_mail(date) {
        $('div.overlay').show();
        $.ajax({
            method: "GET",
            url: "/hoursWorked",
            data: {date: date, task_removed: $('#task_removed').val()}
        }).success(function (totalHours) {
            if (parseInt(totalHours) < 9) {
                swal("Number of working hours for a day cannot be less than 9 hrs for a timesheet to be sent!");
                $('div.overlay').hide();
                return false;
            } else {
                $.ajax({
                    url: '/sendEmailToLeadsAndManagers',
                    type: 'GET',
                    data: {
                        'task_removed': $('#task_removed').val(),
                        'date': date
                    },
                    success: function (data) {
                        $('div.overlay').hide();
                        alert(data);
                        window.location.href = '/admin/timesheets';
                    }
                });
            }
        });
    }

    $('#send-mail').click(async function (event) {
        event.preventDefault();

        var mail_pending_dates = {};
        $.ajax({
            method: "GET",
            url: "/datesMailPending",
            data: {'task_removed': $('#task_removed').val()},
            async: false
        }).success(function (dates) {
            mail_pending_dates = $.parseJSON(dates);
        });

        if (Object.keys(mail_pending_dates).length == 1) {
            send_timesheet_mail(Object.keys(mail_pending_dates)[0]);
        } else {
            swal({
                title: 'Select date for which you need to send timesheet.',
                input: 'radio',
                inputOptions: mail_pending_dates
            }).then(function (result) {
                if (result.value) {
                    send_timesheet_mail(result.value);
                } else {
                    swal({type: 'error', text: "Please select one date!"});
                }
            });
        }
    });

    //remove row from timesheet
    $('.remove-row').click(function () {
        $("#task_removed").val($("#task_removed").val() + ',' + $(this).parents('tr').attr('data-value'));
        $(this).parents('tr').remove();
        if ($('#example1 tr').length == 1) {
            $('#send-mail').hide();
        } else {
            $('#send-mail').show();
        }
    });
    $('.date').data("DateTimePicker").minDate(moment().subtract(1, 'days').millisecond(0).second(0).minute(0).hour(0));
    $('.date').data("DateTimePicker").maxDate(moment()).daysOfWeekDisabled([0, 6]);
});
</script>
@endpush
