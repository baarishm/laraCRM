@extends("la.layouts.app")
@section("contentheader_title")
Team Leave Dashboard
@endsection
@section("main-content")


<br />
@if (\Session::has('success'))
<div class="alert alert-success">
    <p>{{ \Session::get('success') }}</p>
</div><br />

@endif

<div class="box box-success">
    <div class="box-body" style="background: #FFF">
        @if(!empty($leaveMaster))
        <div class="row">
            <div class="col-md-3 col-md-offset-2">
                <div class="input-group date">
                    <input class="form-control date_search" placeholder="Enter From Date" required="" name="start_date" id="start_date" type="text" value="" autocomplete="off">
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <div class="input-group date">
                    <input class="form-control date_search" placeholder="Enter To Date" required="" name="end_date" id="end_date" type="text" value="" autocomplete="off">
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <input type="text" readonly="true" id="holder" class="pull-right" style="border:none;">
        <table class="table table-striped table-bordered"  id="searchdate">


            <tr>
            <thead>
            <th>Emp Code</th>
            <th>Name</th>
            <th>Applied Date</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>No Of Days</th>
            <th>Leave Type</th>
            <th>Purpose</th>
            <th>Leave Status</th>
            @if($role != 'superAdmin')
            <th style="width:155px; text-align:center;">Action</th>
            @endif
            </thead>
            </tr>

<!--            <tbody>

                @foreach($leaveMaster as $leaveMasterRow)
                @php
                $FromDate=date('d M Y', strtotime($leaveMasterRow->FromDate));
                $ToDate=date('d M Y', strtotime($leaveMasterRow->ToDate));
                $Approved=$leaveMasterRow->Approved;
                @endphp

                <tr>
                    <td>{{$leaveMasterRow->emp_code}}</td>
                    <td>{{$leaveMasterRow->Employees_name}}</td>
                    <td>{{date('d M Y',strtotime($leaveMasterRow->created_at))}}</td>
                    <td>{{$FromDate}}</td>
                    <td>{{$ToDate}}</td>
                    <td>{{$leaveMasterRow->NoOfDays}}</td>
                    <td>{{(($leaveMasterRow->leave_name != '')? $leaveMasterRow->leave_name : "Not Specified" ) }}</td> 

                    <td><span class="tooltips" title="{{$leaveMasterRow->LeaveReason}}" >{{((strlen($leaveMasterRow->LeaveReason)>20) ? substr($leaveMasterRow->LeaveReason, 0, 20).'...' : $leaveMasterRow->LeaveReason)}}</span>

                    </td>-->
<!--                    <td class="status">
                        @if($Approved=='1')
                        <span class="text-success">Approved</span>
                        @elseif($Approved=='0')
                        <span class="text-danger">Rejected</span>
                        @else
                        Pending
                        @endif
                    </td>-->
<!--                    <td class="text-center"> 
                        @if($role == 'lead')
                        @if($Approved=='1' || $Approved=='0')
                        Action Taken
                        @elseif($leaveMasterRow->comp_off_id == '' || (($leaveMasterRow->comp_off_deleted == '' || $leaveMasterRow->comp_off_deleted == null)))-->

            <!--                        <div class="">
                                        <button type="button" class="btn btn-success" name="Approved" id="Approved" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" >Approve</button>
                                        <button type="button" class="btn btn" name="Rejected" id="Rejected" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> 
                                    </div>
            
                                    @else
                                    <span class="text-danger">Comp Off Lapsed</span>
                                    @endif
                                    @elseif($role == 'manager')
                                    @if($Approved=='1' && $leaveMasterRow->RejectedBy == '')
            
                                    <div class="">
                                        <button type="button" class="btn btn" name="Rejected" id="Rejected" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> 
                                    </div>
            
                                    @elseif($Approved=='0' && $leaveMasterRow->ApprovedBy == '')
            
                                    <div class="">
                                        <button type="button" class="btn btn-success" name="Approved" id="Approved" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" >Approve</button>
                                    </div>
            
                                    @elseif(($Approved=='1' || $Approved=='0') && $leaveMasterRow->RejectedBy != '' && $leaveMasterRow->ApprovedBy != '')
            
                                    <span class="text-success">Action Taken</span>
            
                                    @elseif(($Approved=='1' || $Approved=='0') && $leaveMasterRow->RejectedBy != '' && $leaveMasterRow->ApprovedBy != '')
            
                                    <span class="text-success">Action Taken</span>
            
                                    @elseif($leaveMasterRow->comp_off_id == '' || (($leaveMasterRow->comp_off_deleted == '' || $leaveMasterRow->comp_off_deleted == null)))
            
                                    <div class="">
                                        <button type="button" class="btn btn-success" name="Approved" id="Approved" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" >Approve</button>
                                        <button type="button" class="btn btn" name="Rejected" id="Rejected" data-id = <?php echo $leaveMasterRow->id; ?> data-days = <?php echo $leaveMasterRow->NoOfDays; ?> onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> 
                                    </div>
            
                                    @else
                                    <span class="text-danger">Comp Off Lapsed</span>
                                    @endif
                                    @endif
            
                                </td>
            
                            </tr>
                            @endforeach
                        </tbody>-->
        </table>
        @else
        <div>No Record found!</div>
        @endif
    </div>
</div>



@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script type="text/javascript">
function dateSorting() {
    $("#searchdate").html('');
    $.ajax({
        url: "{{ url(config('laraadmin.adminRoute') . '/datesearch') }}",
        type: 'POST',
        data: {'start_date': $('#start_date.date_search').val(), 'end_date': $('#end_date.date_search').val(), _token: "{{ csrf_token() }}"},
        success: function (data) {
            data = $.parseJSON(data);
            $("#searchdate").html(data.html);
        }
    });
}


function myfunction(button)
{

    var controlid = $(button).attr('id');

    var approved = 0;
    if (controlid == 'Approved')
    {
        approved = 1;
    }
    swal({
        title: "Enter Comment",
        input: "textarea",
        showCancelButton: true,
        closeOnConfirm: false,
        inputPlaceholder: "Comment on approval"
    }).then(function (inputValue) {
        if (inputValue.dismiss === 'cancel') {
            return false;
        } else {
            $.ajax({
                url: "{{ url('/approveLeave') }}",
                type: 'GET',
                data: {'approved': approved, 'id': $(button).attr('data-id'), 'days': $(button).attr('data-days'), 'actionReason': inputValue.value},
                success: function (data) {
                    console.log(data);
                    swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
                }
            });
            var vid = $(button).attr('data-id');
            $(button).parents('td').siblings(".status").html((approved) ? '<span class="text-success">Approved</span>' : '<span class="text-danger">Rejected</span>');
            $(button).parents('td').html('Action Taken');
            $('[data-id=' + vid + ']').remove();
        }
    });

}

$(document).ready(function () {

    var table = $('#searchdate').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            dataType: "json",
            url: "{{url(config('laraadmin.adminRoute').'/leave/Datatable')}}",
            type: 'get',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.teamMember = "{{$teamMember}}";
                d.week_search = $('div.week-div').attr('data-value');
//                        filterDatatableData(d);
//                        $('.tooltips').tooltips();
            }
        },
        drawCallback: function () {
            console.log('here');
        }
    });

    $('.date').on('dp.change', function (e) {
        table.draw();
    });
});


</script>

@endpush

