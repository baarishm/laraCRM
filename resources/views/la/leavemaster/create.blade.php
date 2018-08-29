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

                            <input type="text" class="form-control" 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="From" required  readonly='true' />
                        </div>

                        <div class="form-group col-md-3">

                            <!--            <label for="text" class="control-label">End Date:</label>-->
                            <span for="text" class="control-label">End Date*</span>

                            <input type="text" class="form-control " id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="To" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' />	

                        </div>


                        <div class="form-group col-md-3">
                            <label for="Name">Number Of Days</label>
                            <input type="text" class="form-control" readonly="readonly" name="NoOfDays" id="NoOfDays" autocomplete="off" >
                            <!--<div style="margin:1%;" > </div> -->
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Number">Leave Purpose</label>

                            <input type="text" class="form-control" name="LeaveReason" autocomplete="off" placeholder="Reason" required  >   
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
                            <button type="submit" class="btn btn-success"  onclick="this.disabled = true;this.value = 'Sending, please wait...';this.form.submit();">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!--<script type="text/javascript">
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


</script>-->
<script type="text/javascript">
    $(document).ready(function () {


        // To calulate difference b/w two dates
        function CalculateDiff(isstart)
        {
            if ($("#datepicker").val() != "" && $("#datepickerto").val() != "") {
                var start = $("#datepicker").datepicker("getDate");
                var end = $("#datepickerto").datepicker("getDate");
                days = ((end - start) / (1000 * 60 * 60 * 24)) + 1;
                $("#NoOfDays").val(days);
                // alert(Math.round(days));

            }
        }

        $("#datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',
        }).on('changeDate', function (e) {
            $("#datepickerto").datepicker('setStartDate', e.date);

            CalculateDiff(true);
        });
        $("#datepickerto").datepicker({

            autoclose: true,
            format: 'd M yyyy'


        }).on('changeDate', function () {
            CalculateDiff(false);
        });
    });


</script>
@endpush
