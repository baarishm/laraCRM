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
        <div class="row" style="padding-bottom:5px">
            <div class="col-md-3">
                <div class="input-group date">
                    <input class="form-control date_search" placeholder="Enter From Date" required="" name="start_date" id="start_date" type="text" value="" autocomplete="off">
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group date">
                    <input class="form-control date_search" placeholder="Enter To Date" required="" name="end_date" id="end_date" type="text" value="" autocomplete="off">
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>


            <div class="col-md-3">
                <select id="employee_search" name="employee_search">
                    <option value="" selected="selected" >Select Employee</option>
                    <?php
                    if (!empty($teamname)) {
                        foreach ($teamname as $value) {
                            echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="status_search" name="status_search">
                    <option value="" selected="selected" >Select Status</option>
                    <option value="1" >Approved</option>
                    <option value="0" >Reject</option>
                    <option value="2"  >Pending</option>

                </select>
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
        data: {
            'name': $('#employees_name.employee_search').val(), 'Approved': $('#status_search.status_search').val(),
            'start_date': $('#start_date.date_search').val(), 'end_date': $('#end_date.date_search').val(), _token: "{{ csrf_token() }}"
        },
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
            $('div.overlay').removeClass('hide');
            $.ajax({
                url: "{{ url('/approveLeave') }}",
                type: 'GET',
                data: {
                    'approved': approved, 'id': $(button).attr('data-id'), 'days': $(button).attr('data-days'), 'actionReason': inputValue.value
                },
                success: function (data) {
                    if (data !== 'true') {
                        swal(data);
                    } else {
                        var vid = $(button).attr('data-id');
                        $(button).parents('td').siblings('td').children(".status").parents('td')
                                .html((approved) ? '<span class="text-success status">Approved</span>' : '<span class="text-danger status">Rejected</span>');
                        $(button).parents('td').html('Action Taken');
                        $('[data-id=' + vid + ']').remove();
                        swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
                    }
                    $('div.overlay').addClass('hide');
                }
            });
        }
    });

}

$(document).ready(function () {
    $('#start_date, #end_date, #employee_search, #status_search').val('');

    var table = $('#searchdate').DataTable({
        Processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        ajax: {
            dataType: "json",
            url: "{{url(config('laraadmin.adminRoute').'/leave/Datatable')}}",
            type: 'POST',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.employee_search = (($('#employee_search').length > 0) ? $('#employee_search').val() : '');
                d.status_search = (($('#status_search').length > 0) ? $('#status_search').val() : '');

                d.teamMember = "{{$teamMember}}";
                d._token = "{{ csrf_token()}}";
                filterDatatableData(d);
            },
            dataFilter: function (data) {
                var json = jQuery.parseJSON(data);
                json.recordsTotal = json.total;
                json.recordsFiltered = json.total;
                json.data = json.data;

                return JSON.stringify(json); // return JSON string
            }
        },
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
        drawCallback: function (data) {
            $('.tooltips').tooltip();
        }
    });
    $("#employee_search, #status_search").on('change dp.change', function () {

        table.draw();
    });

    $('.date').on('dp.change', function (e) {
        table.draw();
    });
//     $('#status_search').on('change', function (e) {
//         
//      table.draw();

<<<<<<< HEAD
=======


>>>>>>> optimize_code
});
$('#employee_search, #status_search').select2();


</script>

@endpush

