@extends("la.layouts.app")
@section("contentheader_title")
Edit Apply  Leave
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


                <form method="post" action="{{action('LA\LeaveMasterController@update', $leaveMaster -> id)}}">

                    <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="row">

                        <div class="form-group col-md-4">
                            <label for="name">Employee Id:</label>
                            <input type="text" class ="form-control" autocomplete="off" readonly="readonly" name="EmpId" value="{{$leaveMaster -> EmpId}}">
                        </div>


                        <div class="form-group col-md-4">
                            <label for="StartDate" class="control-label">Start Date:</label>
                            <input type="text" class="form-control " 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required  readonly='true' value="{{$leaveMaster -> FromDate or old('FromDate') }}" />

                        </div>
                        <div class="form-group col-md-4">
                            <label for="text" class="control-label">End Date:</label>

                            <input type="text" class="form-control" id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' value="{{$leaveMaster -> ToDate or old('ToDate') }}" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name">Number Of Days</label>
                            <input type="text" class="form-control" name="NoOfDays" autocomplete="off" readonly="readonly" id="NoOfDays" value="{{$leaveMaster -> NoOfDays or old('NoOfDays') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="number">Leave Purpose</label>

                            <input type="text" class="form-control" name="LeaveReason" autocomplete="off"  placeholder="Leave Purpose" required maxlength="180" value="{{$leaveMaster -> LeaveReason or old('LeaveReason') }}"> 
                        </div>
                        <div class="form-group col-md-4">
                            <label>Leave Type</label>
                            <select name="LeaveType" class="form-control" >

                                <?php
                                if (!empty($leaveMaster->leave_type)) {
                                    foreach ($leaveMaster->leave_type as $value) {
                                        echo '<option value="' . $value->id . '" ' . (($leaveMaster->LeaveType == $value->id) ? 'selected' : '' ) . '>' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="col-sm-12 text-right" style="margin-top:60px">
                            <button type="submit" class="btn btn-success" style="margin-left:38px">Update</button>
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
    start.setHours(0,0,0,1);  // Start just after midnight
    end.setHours(23,59,59,999);  // End just before midnight
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
//
//
//                // alert(Math.round(days));
//
//            }
//        }

        $("#datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',
             startDate: '-20d',
             endDate: '+60d',

        }).on('changeDate', function (e) {
            $("#datepickerto").val('');
            $("#NoOfDays").val('');
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