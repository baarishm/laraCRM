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
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Email Timesheet</a>
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

<!-- Buttons to send email -->
<div class="form-group row">
    @if(!empty($records))
    <a href="#" class="btn btn-primary pull-right" id="send-mail">Send Timesheet Email</a>
    @endif
</div>

<!-- for Last records details -->
<div class="box box-success">
    <!--<div class="box-header"></div>-->
    <div class="box-body">
        <table id="example1" class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>Date</th>
                    <th>Project Name</th>
                    <th>Task Name</th>
                    <th>Time Spent(in hrs)</th>
                    <!--<th>Remove Row from this Sheet</th>-->
                </tr>
            </thead>
            @if(!empty($records))
            @foreach($records as $record)
            <tr class="entry-row" data-value="{{$record->id}}">
                <td>{{date('d M Y',strtotime($record->date))}}</td>
                <td>{{$record->project_name}}</td>
                <td>{{$record->task_name}}</td>
                <td>{{$record->hours+($record->minutes/60)}}</td>
                <!--<td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times"></i></button></td>-->
            </tr>
            @endforeach
            @endif
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<input type="hidden" name="task_removed" id="task_removed" value="{{$task_removed}}" />
<!-- End -->
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {

    //initialize datatable
    $("#example1").DataTable({
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
    });

    //send mail
    function send_timesheet_mail(type, date) {
        $('div.overlay').removeClass('hide');
        $.ajax({
            method: "POST",
            url: "{{ url('/hoursWorked') }}",
            data: {type: type, date: date, task_removed: $('#task_removed').val(), _token: "{{ csrf_token() }}"}
        }).success(function (totalHours) {
            if (parseInt(totalHours) < 9) {
                swal("Number of working hours for a day cannot be less than 9 hrs for a timesheet to be sent!");
                $('div.overlay').addClass('hide');
                return false;
            } else {
                $.ajax({
                    url: "{{ url('/sendEmailToLeadsAndManagers') }}",
                    type: 'POST',
                    data: {
                        task_removed: $('#task_removed').val(),
                        date: date,
                        _token: "{{ csrf_token()}}",
                        type: type
                    },
                    success: function (data) {
                        $('div.overlay').addClass('hide');
                        alert(data);
                        window.location.href = "{{ url('/admin/timesheets') }}";
                    }
                });
            }
        });
    }

    $('#send-mail').click(async function (event) {
        event.preventDefault();
        var mail_pending_dates = {};
        swal({
            title: 'Send Timesheet for?',
            input: 'radio',
            inputOptions: {week: 'This Week', day: 'Day'}
        }).then(function (result) {
            if (result.value == 'day') {
                $.ajax({
                    method: "POST",
                    url: "{{ url('/datesMailPending') }}",
                    data: {'task_removed': $('#task_removed').val(), _token: "{{ csrf_token() }}"},
                    async: false
                }).success(function (dates) {
                    mail_pending_dates = $.parseJSON(dates);
                });

                if (Object.keys(mail_pending_dates).length == 1) {
                    send_timesheet_mail('day', Object.keys(mail_pending_dates)[0]);
                } else {
                    swal({
                        title: 'Select date for which you need to send timesheet.',
                        input: 'radio',
                        inputOptions: mail_pending_dates
                    }).then(function (result) {
                        if (result.value) {
                            send_timesheet_mail('day', result.value);
                        } else {
                            swal({type: 'error', text: "Please select one date!"});
                        }
                    });
                }
            } else if (result.value == 'week') {
                send_timesheet_mail('week', '');
            } else {
                swal({type: 'error', text: "Please select an option!"});
            }
        });

    });

    //remove row from timesheet
    $(document).on('click', '.remove-row', function () {
        $("#task_removed").val($("#task_removed").val() + ',' + $(this).parents('tr').attr('data-value'));
        $(this).parents('tr').addClass('hide').remove();
        if ($('#example1 tr').length == 1) {
            $('ul.pagination li.paginate_button.active').prev('li.paginate_button').trigger('click');
            if ($('#example1 tr').length == 1) {
                $('#send-mail').hide();
            } else {
                $('#send-mail').show();
            }
        }
    });
});
</script>
@endpush
