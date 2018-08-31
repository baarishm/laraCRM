@extends("la.layouts.app")
@section("contentheader_title")
Leave Dashboard
@endsection
@section("main-content")

<div class="">
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
    <div class="card" style="background: #FFF">
    <table class="table table-striped table-bordered">


        <tr>
        <thead>
        <th >From Date</th>
        <th >To Date</th>
        <th >No Of Days</th>
        <th >Leave Type</th>
        <th >Leave Status</th>
        <th style="width: 103px; text-align:center;" >Action</th>  
        </thead>
        </tr>

        <tbody>

            @foreach($leaveMaster as $leaveMasterRow)
            @php
            $FromDate=date('d M Y',  strtotime($leaveMasterRow->FromDate));
            $ToDate=date('d M Y',  strtotime($leaveMasterRow->ToDate));

            @endphp

            <tr>

                <td>{{$FromDate}}</td>
                <td>{{$ToDate}}</td>
                <td>{{$leaveMasterRow->NoOfDays}}</td>
                <td>{{(($leaveMasterRow->leave_name != '')? $leaveMasterRow->leave_name : "Not Specified" ) }}</td>
                @if( $leaveMasterRow->Approved === 1)
                    <td>Approved </td>
                    @elseif( $leaveMasterRow->Approved === 0 )
                    <td>Rejected</td>
                    @else 
                    <td>Pending </td>
                    @endif
          
                @if($leaveMasterRow->Approved == '1') 
                <td>
                        <span  class="text-success">Approved</span>
                </td>
               @elseif($leaveMasterRow->Approved == '0')
               
                <td>
                        <span   class="text-danger" >Rejected</span>
                        @else($leaveMasterRow->Approved =='' || $leaveMasterRow->Approved=='NULL')
                <td class="text-center">
                
                    <form action="{{action('LA\LeaveMasterController@destroy', $leaveMasterRow->id)}}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token()}}">
                       
                        
                     <a href="{{action('LA\LeaveMasterController@edit',$leaveMasterRow->id)}}" class="btn btn-warning "><i class="fa fa-edit"></i></a>
                        <input name="_method" type="hidden" value="DELETE" >
                        <button class="btn btn-danger pull-left" type="submit"><i class="fa fa-remove"></i></button>
                    @endif
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
</div>

@endsection