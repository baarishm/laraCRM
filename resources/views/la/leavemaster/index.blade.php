@extends("la.layouts.app")
@section("contentheader_title")
Leave Dashboard
@endsection
@section("main-content")
<?php
$role = \Session::get('role');
?>

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
        <table class="table table-striped table-bordered" id="example">
            <tr>
            <thead>
            <th>From Date</th>
            <th>To Date</th>
            <th>No Of Days</th>
            <th>Leave Type</th>
            <th>Leave duration</th>
            <th> Status</th>
            <th style="width: 103px; text-align:center;">Action</th>  
            </thead>
            </tr>
        </table>
    </div>
</div>

@endsection
<!--<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>-->
@push('scripts')

<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
    $(document).on('click', '.withdraw', function (e) {
        e.preventDefault();
        var link = $(this);
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, Withdraw!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then(function (isConfirm) {
            if (isConfirm.value) {
                $.ajax({
                    method: "POST",
                    url: "{{ url(config('laraadmin.adminRoute') . '/leave/withdraw') }}",
                    data: {
                        id: link.attr('data-removed'), _token: "{{ csrf_token()}}"
                    }
                }).success(function (message) {
                    link.parents('td').html('Withdrawn');
                    swal(message);
                });
            } else {
                return false;
            }
        });
    });
});
$(document).ready(function () {
    var table = $('#example').dataTable({
        "Processing": true,
        "ServerSide": true,
        searching: false,
        "ordering": false,
        ajax: {
            "dataType": "json",
            url: "{{url(config('laraadmin.adminRoute').'/leave/Datatable')}}",
            type: 'get',
        },
        drawCallback: function (data) {
            $('.tooltips').tooltip();
        }
    });
});

</script>
@endpush