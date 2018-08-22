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
                    <th>Time Spent</th>
                    <th>Remove Row form this Sheet</th>
                </tr>
            </thead>
            @if(!empty($records))
            @foreach($records as $record)
            <tr class="entry-row" data-value="{{$record->id}}">
                <td>{{$record->project_name}}</td>
                <td>{{$record->task_name}}</td>
                <td>{{$record->hours+($record->minutes/60)}}</td>
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
                            <div class="col-md-4">
                                @la_input($module, 'project_id')
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="task_id">Task Name:</label>
                                    <select class="form-control" name="task_id">
                                        @foreach($tasks as $task)
                                        <option value="{{$task->task_id}}">{{$task->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @la_input($module, 'date')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                @la_input($module, 'hours')
                            </div>
                            <div class="col-md-4">
                                @la_input($module, 'minutes')
                            </div>
                            <div class ="col-md-4">
                                @la_input($module, 'comments')
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
                <input type="hidden" name="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->id)); ?>" />
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
    $("#project-edit-form").validate({

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
    $('#send-mail').click(function (event) {
        event.preventDefault();
        $('div.overlay').show();
        var entry_list = [];
        $(".entry-row").each(function () {
            entry_list.push($(this).attr('data-value'));
        });
        $.ajax({
            url: '/sendEmailToLeadsAndManagers',
            type: 'GET',
            data: {
                'entry_ids': entry_list,
                'task_removed' : $('#task_removed').val()
            },
            success: function (data) {
                $('div.overlay').hide();
                alert(data);
                window.location.href = '/admin/timesheets';
            }
        });
    });

    $('.remove-row').click(function () {
        $("#task_removed").val($("#task_removed").val()+','+$(this).parents('tr').attr('data-value'));
        $(this).parents('tr').remove();
        if ($('#example1 tr').length == 1) {
            $('#send-mail').hide();
        } else {
            $('#send-mail').show();
        }
    });
    $('.date').data("DateTimePicker").minDate(moment().startOf('week'));
    $('.date').data("DateTimePicker").maxDate(moment().endOf('week')).daysOfWeekDisabled([0, 6]);
});
</script>
@endpush
