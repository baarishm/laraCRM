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
                            <label for="Name">Employee Id:</label>
                            <input type="text" class="form-control" name="EmpId" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="EmpId" placeholder="EmpId" required readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <!--            <label for="StartDate" class="control-label">Start Date:</label>-->
                            <span for="StartDate" class="control-label" >Start Date*</span>

                            <input type="text" value="{{ old('FromDate')}}" class="form-control" 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="From" required  readonly='true' />
                        </div>

                        <div class="form-group col-md-3">

                            <!--            <label for="text" class="control-label">End Date:</label>-->
                            <span for="text" class="control-label">End Date*</span>

                            <input type="text" value="{{ old('ToDate')}}" class="form-control " id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="To" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' />	

                        </div>


                        <div class="form-group col-md-3">
                            <label for="Name">Number Of Days</label>
                            <input type="text" value="{{ old('NoOfDays')}}" class="form-control" readonly="readonly" name="NoOfDays" id="NoOfDays" autocomplete="off" >
                            <!--<div style="margin:1%;" > </div> -->
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Number">Leave Purpose</label>

                            <input type="text" value="{{ old('LeaveReason')}}" class="form-control" name="LeaveReason" autocomplete="off" placeholder="Reason" required maxlength="180"  >   
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
                        <div class="col-md-3" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-success"onclick="this.disabled = true;this.value = 'Sending, please wait...';this.form.submit();">Submit</button>
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
    $(document).ready(function () {



        // To calulate difference b/w two dates
        function CalculateDiff(isstart)
        {
            if ($("#datepicker").val() != "" && $("#datepickerto").val() != "") {
                var start = $("#datepicker").datepicker("getDate");
                var end = $("#datepickerto").datepicker("getDate");

                if (end < start)
                    return 0;

                // Calculate days between dates
                var millisecondsPerDay = 86400 * 1000; // Day in milliseconds
                start.setHours(0, 0, 0, 1);  // Start just after midnight
                end.setHours(23, 59, 59, 999);  // End just before midnight
                var diff = end - start;  // Milliseconds between datetime objects    
                var days = Math.ceil(diff / millisecondsPerDay);

                // Subtract two weekend days for every week in between
                var weeks = Math.floor(days / 7);
                days = days - (weeks * 2);

                // Handle special cases
                var start = start.getDay();
                var end = end.getDay();

                // Remove weekend not previously removed.   
                if (start - end > 1)
                    days = days - 2;

                // Remove start day if span starts on Sunday but ends before Saturday
                if (start == 0 && end != 6)
                    days = days - 1

                // Remove end day if span ends on Saturday but starts after Sunday
                if (end == 6 && start != 0)
                    days = days - 1
                if(days > '{{ $number_of_leaves }}'){
                    swal('You cannot take more than {{ $number_of_leaves }} leaves at a time!');
                    $('button[type="submit"]').attr('disabled', true);
                }
                else{
                    $('button[type="submit"]').attr('disabled', false);
                }
                $("#NoOfDays").val(days);
                // alert(Math.round(days));

            }
        }


//        function CalculateDiff(isstart)
//        {
//            if ($("#datepicker").val() != "" && $("#datepickerto").val() != "") {
//                var start = $("#datepicker").datepicker("getDate");
//                var end = $("#datepickerto").datepicker("getDate");
//                days = ((end - start) / (1000 * 60 * 60 * 24)) + 1;
//                $("#NoOfDays").val(days);
//                // alert(Math.round(days));
//
//            }
//        }

        $("#datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',
            startDate: '-{{ $before_days }}d',
            endDate: '+{{ $after_days }}d',
            todayHighlight: 'true',

        }).on('changeDate', function (e) {
            $("#datepickerto").val('');
            $("#NoOfDays").val('');

            $("#datepickerto").datepicker('setStartDate', e.date);


            CalculateDiff(true);
        });
        $("#datepickerto").datepicker({

            autoclose: true,
            format: 'd M yyyy',
            todayHighlight: 'true',

        }).on('changeDate', function () {

            CalculateDiff(false);
        });
    }
    );


</script>
@endpush
