@extends("la.layouts.app")
@section("contentheader_title")
Leave Dashboard
@endsection
@section("main-content")

<div class="container">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />

    @endif
    <div class="row">

        <div class="form-group col-md-3">
            <label for="Name">Totel Leaves:10</label>

        </div>

        <div class="form-group col-md-3">
            <label for="Name">availed leave:8</label>


        </div>
        <div class="form-group col-md-3">
            <label for="Name">available Leaves:2</label>


        </div>
        <div class="form-group col-md-3" style="margin-bottom: 15px">
            <a  href="leaves/create" class="btn btn-info">Apply Leave</a>


        </div>
    </div>

    <table class="table table-striped">


        <tr>
        <thead>
        <th style="width: 10px;">EmpId</th>
        <th style="width: 20px;">From Date</th>
        <th style="width: 20px;">To Date</th>
        <th style="width: 10px;">Total Day</th>
        <th style="width: 20px;">Leave Type</th>
        <th style="width: 10px;">Leave Status</th>
        <th style="width: 10px;" >Action</th>  
        </thead>
        </tr>

        <tbody>

            @foreach($leaveMaster as $leaveMasterRow)
            @php
            $FromDate=date('Y-m-d', strtotime($leaveMasterRow->FromDate));
            $ToDate=date('Y-m-d', strtotime($leaveMasterRow->ToDate));

            @endphp

            <tr>
                <td>{{$leaveMasterRow->EmpId}}</td>
                <td>{{$FromDate}}</td>
                <td>{{$ToDate}}</td>
                <td>{{$leaveMasterRow->NoOfDays}}</td>
                <td>{{$leaveMasterRow->leave_name}}</td>
                <td>{{$leaveMasterRow->Approved}}</td>
                <td><a href="{{action('LA\LeaveMasterController@show',$leaveMasterRow->id)}}" class="btn btn-warning "><i class="fa fa-eye"></i></a>
                
                    <form action="{{action('LA\LeaveMasterController@destroy', $leaveMasterRow->id)}}" method="post">
                        
                        <input name="_method" type="hidden" value="DELETE" >
                        <button class="btn btn-danger pull-left" type="submit"><i class="fa fa-remove"></i></button>
                        
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection