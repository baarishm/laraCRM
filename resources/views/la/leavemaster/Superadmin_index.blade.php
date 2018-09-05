@extends("la.layouts.app")
@section("contentheader_title")
Leave SuperAdmin Dashboard
@endsection
@section("main-content")


<div class="">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />

    @endif
    <div class="card" style="background: #FFF">

        <table class="table table-striped table-bordered" >


            <tr>
            <thead>

            <th>Name</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>No Of Days</th>
            <th>Leave Type</th>
            <th>Purpose</th>
            <th style="width: 103px; text-align:center;">Action</th>

            </thead>
            </tr>

            <tbody>

                @foreach($leaveMaster as $leaveMasterRow)
                @php
                $FromDate=date('d M Y', strtotime($leaveMasterRow->FromDate));
                $ToDate=date('d M Y', strtotime($leaveMasterRow->ToDate));
                $Approved=$leaveMasterRow->Approved;
                @endphp

                <tr id="ps">

                    <td>{{$leaveMasterRow->Employees_name}}</td>

                    <td>{{$FromDate}}</td>
                    <td>{{$ToDate}}</td>
                    <td>{{$leaveMasterRow->NoOfDays}}</td>
                    <td>{{(($leaveMasterRow->leave_name != '')? $leaveMasterRow->leave_name : "Not Specified" ) }}</td> 

<!--                    <td>{{$leaveMasterRow->LeaveReason}}</td>-->
                    <td><span  id="btn2" data-toggle="popover"  title="{{$leaveMasterRow->LeaveReason}}" data-content="Default popover">Leave Reason ..</span>

                    </td>
                     <!--<td>{{(($leaveMasterRow->Approved != '')? $leaveMasterRow->Approved : 'Pending' ) }}</td>-->
                    <td class="text-center"> 
                        @if($Approved=='1' || $Approved=='0')

                        <button type="button" class="btn" name="Approved" id="Approved" style="background: green" >done</button>


                        @else
                        <button type="button" class="btn btn-success" name="Approved" id="Approved" data-id = <?php echo $leaveMasterRow->id; ?> onclick="myfunction(this);"   >Approve</button>
                        <button type="button" class="btn btn" name="Rejected" id="Rejected" data-id = <?php echo $leaveMasterRow->id; ?> onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> 
                        @endif


                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection
    <script type="text/javascript">

        function myfunction(button)
        {

            var controlid = $(button).attr('id');

            var approved = 0;
            if (controlid == 'Approved')
            {
                approved = 1;

            }
            $.ajax({
                url: '/approveLeave',
                type: 'GET',
                data: {'approved': approved, 'id': $(button).attr('data-id')},
                success: function (data) {
                    console.log(data);
                    swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');

                }
            });
            var element = document.getElementById("ps");
            element.classList.add("mystyle");
            var vid = $(button).attr('data-id');
            $('[data-id=' + vid + ']').attr('disabled', 'disabled');

            // $("button").data("data-id").attr('disabled', 'disabled');


        };

    </script>


