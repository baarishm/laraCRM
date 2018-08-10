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
                    <th>Lead Email</th>
                    <th>Manager Email</th>
                </tr>
            </thead>
            @if(!empty($records))
            @foreach($records as $record)
            <tr>
                <td>{{$record['project_name']}}</td>
                <td>{{$record['task_name']}}</td>
                <td>{{$record['hours']+($record['minutes']/60)}}</td>
                <td>{{$record['lead_email']}}</td>
                <td>{{$record['manager_email']}}</td>
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
    <a href="#" class="btn btn-primary" id="send-mail">Send Email to Leads and Managers</a>
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
                        @la_input($module, 'project_id')
                        @la_input($module, 'task_id')
                        @la_input($module, 'date')
                        @la_input($module, 'hours')
                        @la_input($module, 'minutes')
                        @la_input($module, 'comments')
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
                <input type="hidden" name="lead_email" />
                <input type="hidden" name="manager_email" />
                <input type="hidden" name="project_name" />
                <input type="hidden" name="task_name" />
                <input type="hidden" name="timesheet_token" value="<?php echo isset($token) ? $token : ''; ?>" />
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success pull-right']) !!} 
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
    var total_entry = 1;
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

    //add lead and manager email in hidden boxes
    $('select[name="manager_id"], select[name="manager_id"]').change(function () {
        $('input[name="lead_email"]').val($('select[name="lead_id"] option:selected').attr('data-mail'));
        $('input[name="manager_email"]').val($('select[name="manager_id"] option:selected').attr('data-mail'));
    });
    $('select[name="manager_id"], select[name="manager_id"]').trigger('change');

    //add task and project names in hidden boxes
    $('select[name="project_id"], select[name="task_id"]').change(function () {
        $('input[name="task_name"]').val($('select[name="task_id"] option:selected').html());
        $('input[name="project_name"]').val($('select[name="project_id"] option:selected').html());
    });
    $('select[name="project_id"], select[name="task_id"]').trigger('change');

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
        $.ajax({
            url: '/sendEmailToLeadsAndManagers',
            type: 'GET',
            data: {
                'token': $('[name="timesheet_token"]').val()
            },
            success: function (data) {
                $('div.overlay').hide();
                alert(data);
                window.location.href = '/admin/timesheets';
            }
        });
    })
});
</script>
@endpush
