@extends("la.layouts.app")
@section("contentheader_title")
Leave Manager Dashboard
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

                @endphp

                <tr>

                    <td>{{$leaveMasterRow->Employees_name}}</td>

                    <td>{{$FromDate}}</td>
                    <td>{{$ToDate}}</td>
                    <td>{{$leaveMasterRow->NoOfDays}}</td>
                    <td>{{(($leaveMasterRow->leave_name != '')? $leaveMasterRow->leave_name : "Not Specified" ) }}</td> 

                    <td>{{$leaveMasterRow->LeaveReason}}</td>
                     <!--<td>{{(($leaveMasterRow->Approved != '')? $leaveMasterRow->Approved : 'Pending' ) }}</td>-->



                    <td class="text-center"> 
                        <button type="button" class="btn btn-success" name="Approved" id="Approved" onclick="myfunction(this);"   >Approve</button>
                        <button type="button" class="btn btn" name="Rejected" id="Rejected" onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection
    <script type="text/javascript">
        
        function myfunction(id)
        {
           var controlid=id.id;
           if (controlid=='Approved') 
            {
               alert("Approved");
    
        }
        else{
            
            alert("Rejected");
            
        }
 
}
        

    </script>