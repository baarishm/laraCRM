@extends("la.layouts.app")
@section("contentheader_title")
View Apply  Leave
@endsection
@section("main-content")

<div class="col-sm-12"> 
    <div class="text-right" style="margin-bottom:20px;">  <a href="{{action('LA\LeaveMasterController@edit', $leaveMaster->id)}}" class="btn btn-warning">Edit</a></div>
    <table class="custom-table table-bordered" >
        <tr>
            <th>Employee Id</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Number Of Days</th>
            <th>Leave Purpose</th>
            <th>Leave Type</th>
        </tr>
        <tr>
            <td>{{$leaveMaster->EmpId}}</td>
            <td>{{$leaveMaster->FromDate}}</td>
            <td>{{$leaveMaster->ToDate}}</td>
            <td>{{$leaveMaster->NoOfDays}}</td>
            <td>{{$leaveMaster->LeaveReason}}</td>
            <td>{{$leaveMaster->leave_name}}</td>
        </tr>
    </table>
    <!--	<div class="form-group col-md-4">
    <label>Leave Duration Type</label>
    <select name="LeaveDurationType" class="form-control"> 
                     <option value="" @if($leaveMaster->LeaveDurationType=="") selected @endif>select</option>
      <option value=".5"  @if($leaveMaster->LeaveDurationType==".5") selected @endif>Half Day</option>
      <option value="1"  @if($leaveMaster->LeaveDurationType=="1") selected @endif>Full Day</option>
     
    </select>
</div>  -->
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
                // days = ((end- start) / (1000 * 60 * 60 * 24))+1;
                // $("#NoOfDays").val(days);
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
        $("#datepicker").datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',

        }).on('changeDate', function (e) {
            //$("#datepickerto").datepicker('setStartDate', e.date);
            CalculateDiff(true);
        });

        $("#datepickerto").datepicker({

            autoclose: true,
            format: 'yyyy-mm-dd'

        }).on('changeDate', function () {
            CalculateDiff(false);
        });
    });

</script>
@endpush