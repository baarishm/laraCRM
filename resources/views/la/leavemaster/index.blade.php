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

    @elseif (\Session::has('error'))
    <div class="alert alert-error">
        <p>{{ \Session::get('error') }}</p>
    </div><br />

    @endif
    <div class="row" style="background: #dee2f7;">


        <div class="col-md-2 mt5">
            <label for="Name" style="color:blue;">Total Leaves : {{$empdetail->total_leaves}}</label>

        </div>

        <div class="col-md-2 mt5">
            <label for="Name" style="color:red;">Availed leave : {{$empdetail->availed_leaves}}</label>


        </div>
        <div class="col-md-2 mt5">
            <label for="Name" style="color:green;">Available Leaves : {{$empdetail->available_leaves}}</label>

        </div>
        <div class="col-md-2 mt5">
            <label for="Name" style="color:green;">Available Comp-Offs : {{$empdetail->comp_off}}</label>

        </div>
        <div class="col-md-3">
            <a  href="leaves/create" class="btn btn-info pull-right">Apply Leave</a>


        </div>
    </div>
    <div class="card" style="background: #FFF">
        <table class="table table-striped table-bordered" id="table">


            <tr>
            <thead>
            <th>From Date</th>
            <th>To Date</th>
            <th>No Of Days</th>
            <th>Leave Type</th>
            <!--<th>Comment</th>-->
            <th> Status</th>
            <th style="width: 103px; text-align:center;">Action</th>  
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
                    <td><span class="text-success">Approved</span></td>
                    @elseif( $leaveMasterRow->Approved === 0 )
                    <td><span class="text-danger">Rejected</span></td>
                    @else 
                    <td>Pending </td>
                    @endif


                    @if(($leaveMasterRow->Approved == '1' || $leaveMasterRow->Approved == '0') && !$leaveMasterRow->withdraw  && (date('Y-m-d') <= $leaveMasterRow->FromDate) && (($leaveMasterRow->comp_off_deleted == null || $leaveMasterRow->comp_off_deleted == ''))) 

                    <td>
                        <a href="" class="btn btn-default withdraw" data-removed="{{$leaveMasterRow->id}}">Withdraw</a>
                    </td>
                    @elseif(($leaveMasterRow->Approved == '1' || $leaveMasterRow->Approved == '0') && $leaveMasterRow->withdraw) 
                    <td style="text-align: center">
                        Withdrawn
                    </td>
                    @elseif((($leaveMasterRow->Approved =='' || $leaveMasterRow->Approved=='NULL') && date('Y-m-d', strtotime('-'.$before_days.' days')) <= $leaveMasterRow->FromDate))
                    <td class="text-center">

                        <form action="{{action('LA\LeaveMasterController@destroy', $leaveMasterRow->id)}}" method="post" class="delete">
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">


                            <a href="{{action('LA\LeaveMasterController@edit',$leaveMasterRow->id)}}" class="btn btn-warning "><i class="fa fa-edit"></i></a>
                            <input name="_method" type="hidden" value="DELETE" >
                            <button class="btn btn-danger pull-left delete-btn" type="submit"><i class="fa fa-remove"></i></button>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
</div>

@endsection
<!--<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>-->
@push('scripts')

<!--<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>-->
<script>
    $(function () {
        $('.withdraw').on('click', function (e) {
            e.preventDefault();
            var link = $(this);
            $.ajax({
                method: "POST",
                url: "{{ url(config('laraadmin.adminRoute') . '/leave/withdraw') }}",
                data: {id: link.attr('data-removed'), _token: "{{ csrf_token()}}"}
            }).success(function (message) {
                link.parents('td').html('Withdrawn');
                swal(message);
            });
        });
    });


//$(document).ready(function() {
//var table = $("#table").DataTable({
//     destroy: true,
//           ordering: true,
//            responsive: true,
//processing: true,
//        serverSide: true,
//        searching: false,
//        
//        ajax: {
//             "dataType": "json",
//        url:"{{url(config('laraadmin.adminRoute').'/leave/Datatable')}}",
//         
//                type : 'get',
//                
//         
//        },
//        
//         "columnDefs": [ {
//                    "data" : "FromDate"
//                }, {
//                    "data" : "ToDate"
//                }, {
//                    "data" : "NoOfDays"
//                }, {
//                    "data" : "leave_name"
//                }, {
//                    "data" : "Approved"
//               
//                } ]
//        })
//});


</script>
@endpush