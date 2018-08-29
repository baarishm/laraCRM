@extends("la.layouts.app")
@section("contentheader_title")
View Employee on Leave
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
            <th>Date</th>
            <th>Leave Type</th>
            <th>Purpose</th>
<!--            <th style="width: 103px; text-align:center;">Action</th>-->

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
                    <td>{{$leaveMasterRow->leave_name}}</td> 
                    <td><span  id="btn2" data-toggle="popover"  title="{{$leaveMasterRow->LeaveReason}}" data-content="Default popover">Leave Reason ..</span>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection

       


