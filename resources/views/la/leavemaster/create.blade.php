@extends("la.layouts.app")
@section("contentheader_title")

<?php
// start the session
session_start();
// form token 
$csrf_token = uniqid();

// create form token session variable and store generated id in it.
$_SESSION['csrf_token'] = $csrf_token;
?>
Apply For Leave
@endsection

@section("main-content")
@if(count($errors))
<div class="form-group">
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
<div class="box entry-form">
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <form method="POST" action="{{url(config('laraadmin.adminRoute').'/leaves/store')}}" >
                    <input type="hidden" name="_token" value="{{ csrf_token()}}">

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Manager Name</label>
                            <input type="text" class="form-control" value="{{$manager}}" disabled/>
                        </div>
                        <div class="form-group col-md-3 hide">
                            <label for="Name">Employee Id:</label>
                            <input type="text" class="form-control" name="EmpId" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="EmpId" placeholder="EmpId" required readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Leave Type</label>
                            <select name="LeaveType" class="form-control" >
                                <?php
                                if (!empty($leave_types)) {
                                    foreach ($leave_types as $value) {
                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-3">

                            <label for="StartDate" class="control-label">Start Date*</label>

                            <input type="text" value="{{ old('FromDate')}}" class="form-control" 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="From" readonly='true' />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="EndDate" class="control-label">End Date*</label>
                            <input type="text" value="{{ old('ToDate')}}" class="form-control " id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="To" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' />	
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Name">Number Of Days</label>
                            <input type="text" value="{{ old('NoOfDays')}}" class="form-control" readonly="readonly" name="NoOfDays" id="NoOfDays" autocomplete="off" >

                        </div>
                        <div class="form-group col-md-3">
                            <label for="Number">Leave Purpose*</label>
                            <input type="text" value="{{ old('LeaveReason')}}" class="form-control" name="LeaveReason" autocomplete="off" placeholder="Reason" maxlength="180"  >   
                        </div>
                        <div class="col-md-3" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-success" onclick="this.disabled = true; this.value = 'Sending, please wait...'; this.form.submit();">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')


<script type="text/javascript">
//    $(document).ready(function () {
//
//        // To calulate difference b/w two dates
//        function CalculateDiff(isstart)
//        {
//            if ($("#datepicker").val() != "" && $("#datepickerto").val() != "") {
//                var start = $("#datepicker").datepicker("getDate");
//                var end = $("#datepickerto").datepicker("getDate");
//
//                if (end < start)
//                    return 0;
//
//                // Calculate days between dates
//                var millisecondsPerDay = 86400 * 1000; // Day in milliseconds
//                start.setHours(0, 0, 0, 1);  // Start just after midnight
//                end.setHours(23, 59, 59, 999);  // End just before midnight
//                var diff = end - start;  // Milliseconds between datetime objects  
//                
//                var days = Math.ceil(diff / millisecondsPerDay);
//
//                // Subtract two weekend days for every week in between
//                var weeks = Math.floor(days / 7);
//                days = days - (weeks * 2);
//
//                // Handle special cases
//                var start = start.getDay();
//                var end = end.getDay();
//
//                // Remove weekend not previously removed.   
//                if (start - end > 1)
//                    days = days - 2;
//
//                // Remove start day if span starts on Sunday but ends before Saturday
//                if (start == 0 && end != 6)
//                    days = days - 1
//
//                // Remove end day if span ends on Saturday but starts after Sunday
//                if (end == 6 && start != 0)
//                    days = days - 1
//                if (days > '{{ $number_of_leaves }}') {
//                    swal('You cannot take more than {{ $number_of_leaves }} leaves at a time!');
//                    $('#datepickerto').val('');
//                    $('button[type="submit"]').attr('disabled', true);
//                } else {
//                    $('button[type="submit"]').attr('disabled', false);
//                }
//               
//                $("#NoOfDays").val(days);
//                // alert(Math.round(days));
//
//            }
//        }
//
//        //get dates from session
//        var dates = "{{ Session::get('holiday_list') }}";
//        dates = JSON.parse(dates.replace(/&quot;/g, '\"'));
//        $('#datepicker').datepicker({
//            todayHighlight: 'true',
//            format: 'd M yyyy',
//            daysOfWeekDisabled: [0],
//            startDate: '-{{ $before_days }}d',
//            endDate: '+{{ $after_days }}d',
//            beforeShowDay: function (date) {
//                var date = date.getFullYear() + "-" + ("0" + (date.getMonth()+1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
//                var Highlight = dates[date];
//                var re = [];
//                if (Highlight) {
//                    re = {enabled: false, classes: "Highlighted tooltips", tooltip: dates[date]['occasion']};
//                } else {
//                    re = {enabled: true};
//                }
//                return re;
//            }
//
//        }).on('changeDate', function (e) {
//            $("#datepickerto").val('');
//            $("#NoOfDays").val('');
//            $("#datepickerto").datepicker('setStartDate', e.date).datepicker("setDate", e.date);
//            CalculateDiff(true);
//        });
//
//        $("#datepickerto").datepicker({
//            todayHighlight: 'true',
//            autoclose: true,
//            format: 'd M yyyy',
//            daysOfWeekDisabled: [0],
//
//            beforeShowDay: function (date) {
//                var date = date.getFullYear() + "-" + ("0" + (date.getMonth()+1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
//                var Highlight = dates[date];
//                if (Highlight) {
//                    re = {enabled: false, classes: "Highlighted", tooltip: 'Holiday'};
//                } else {
//                    re = {enabled: true};
//                }
//                return re;
//            }
//
//        }).on('changeDate', function () {
//
//            CalculateDiff(false);
//        });
//    });
//
//

    $(document).ready(function () {

        //get dates from session
        var dates = "{{ Session::get('holiday_list') }}";
        dates = JSON.parse(dates.replace(/&quot;/g, '\"'));
        
        // To calulate difference b/w two dates
        function CalculateDiff(first, last)
        {
            var aDay = 24 * 60 * 60 * 1000,
                    daysDiff = parseInt((last.getTime() - first.getTime()) / aDay, 10) + 1;
            
            if (daysDiff > 0) {
                for (var i = first.getTime(), lst = last.getTime(); i <= lst; i += aDay) {
                    var d = new Date(i);
                    var date = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
                    if (d.getDay() == 6 || d.getDay() == 0 // weekend
                            || dates[date]) {
                        daysDiff--;
                    }
                }
            }

            $("#NoOfDays").val(daysDiff);
            return daysDiff;
        }


        $('#datepicker').datepicker({
            todayHighlight: 'true',
            format: 'd M yyyy',
            daysOfWeekDisabled: [0,6],
            startDate: '-{{ $before_days }}d',
            endDate: '+{{ $after_days }}d',
            beforeShowDay: function (date) {
                var date = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
                var Highlight = dates[date];
                var re = [];
                if (Highlight) {
                    re = {enabled: false, classes: "Highlighted tooltips", tooltip: dates[date]['occasion']};
                } else {
                    re = {enabled: true};
                }
                return re;
            }

        }).on('changeDate', function (e) {
            $("#datepickerto").val('');
            $("#NoOfDays").val('');
            $("#datepickerto").datepicker('setStartDate', e.date).datepicker("setDate", e.date);
            CalculateDiff(new Date($("#datepicker").datepicker("getDate")), new Date($("#datepickerto").datepicker("getDate")));
        });

        $("#datepickerto").datepicker({
            todayHighlight: 'true',
            autoclose: true,
            format: 'd M yyyy',
            daysOfWeekDisabled: [0,6],
            beforeShowDay: function (date) {
                var date = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
                var Highlight = dates[date];
                if (Highlight) {
                    re = {enabled: false, classes: "Highlighted", tooltip: 'Holiday'};
                } else {
                    re = {enabled: true};
                }
                return re;
            }

        }).on('changeDate', function () {
            CalculateDiff(new Date($("#datepicker").datepicker("getDate")), new Date($("#datepickerto").datepicker("getDate")));
        });
    }
    );



</script>



@endpush
