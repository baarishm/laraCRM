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
            <th>No Of Days</th>
            <th>Leave Purpose</th>
            <th>Leave Type</th>
             <th>Leave Duration</th>
        </tr>
           @php
            $FromDate=date('d M Y',  strtotime($leaveMaster->FromDate));
            $ToDate=date('d M Y',  strtotime($leaveMaster->ToDate));

            @endphp
        <tr>
            <td>{{$leaveMaster->EmpId}}</td>
            <td>{{$leaveMaster->FromDate}}</td>
            <td>{{$leaveMaster->ToDate}}</td>
            <td>{{$leaveMaster->NoOfDays}}</td>
            <td>{{$leaveMaster->LeaveReason}}</td>
            <td>{{$leaveMaster->leave_name}}</td>
            <td>{{$leaveMaster->half_day}}</td>
        </tr>
    </table>
   
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
            format: 'd M YYYY',

        }).on('changeDate', function (e) {
            //$("#datepickerto").datepicker('setStartDate', e.date);
            CalculateDiff(true);
        });

        $("#datepickerto").datepicker({

            autoclose: true,
             format: 'd M YYYY',

        }).on('changeDate', function () {
            CalculateDiff(false);
        });
    });

</script>
@endpush