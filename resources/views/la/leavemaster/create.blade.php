@extends("la.layouts.app")
@section("contentheader_title")
Apply For Leave
@endsection
@section("main-content")
<form method="POST" action="{{url(config('laraadmin.adminRoute').'/leaves/store')}}">
    <input type="hidden" name="_token" value="{{ csrf_token()}}">
    <div class="row">

        <div class="form-group col-md-3">
            <label for="Name">Employee Id:</label>
            <input type="text" class="form-control" name="EmpId" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="EmpId" placeholder="EmpId" required readonly>
        </div>
        <div class="form-group col-md-3">
            <label for="StartDate" class="control-label">Start Date:</label>

            <input type="text" class="form-control datepicker" 
                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required  readonly='true' />
        </div>

        <div class="form-group col-md-3">

            <label for="text" class="control-label">End Date:</label>

            <input type="text" class="form-control datepicker" id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' />	

        </div>


        <div class="form-group col-md-3">
            <label for="Name">Number Of Days</label>
            <input type="text" class="form-control" readonly="readonly" name="NoOfDays" id="NoOfDays" autocomplete="off" >
            <!--<div style="margin:1%;" > </div> -->
        </div>
        <div class="form-group col-md-3">
            <label for="Number">Leave Purpose</label>

            <input type="text" class="form-control" name="LeaveReason" autocomplete="off" placeholder="LeaveReason" required  >   
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
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </div>
    @if(count($errors))
    <div class="form-group col-md-3">
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</form>
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

                //   days = ((end - start) / (1000 * 60 * 60 * 24))+1;
                //  $("#NoOfDays").val(days);

                if (start <= end)

                {
                    days = ((end - start) / (1000 * 60 * 60 * 24)) + 1;
                    $("#NoOfDays").val(days);




                } else

                {
                    if (!isstart)
                        alert(" End date not less then start date");
                    $("#datepickerto").val('');
                    $("#NoOfDays").val('');

                }


                // alert(Math.round(days));

            }
        }

        $(".datepicker").datepicker().on('changeDate', function (e) {
            CalculateDiff(false);
        });
    });


</script>
@endpush
