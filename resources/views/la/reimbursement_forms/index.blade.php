@extends("la.layouts.app")

@section("contentheader_title", "Reimbursement Forms")
@section("contentheader_description", "Reimbursement Forms listing")
@section("section", "Reimbursement Forms")
@section("sub_section", "Listing")
@section("htmlheader_title", "Reimbursement Forms Listing")

@section("headerElems")
@la_access("Reimbursement_Forms", "create")
<!--	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Reimbursement Form</button>-->
@endla_access
@endsection

@section("main-content")
<?php
$role = \Session::get('role');
?>

@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="box box-success">
    <!--<div class="box-header"></div>-->
    <div class="box-body">
        <div class="row ">


            <!--            <div class="col-md-2 pull-right">
                            <input type="text" id="date_search" placeholder="Search by Date" class="search">
                        </div>-->
            @if($teamMember)
            <!--            <div class="col-md-2 pull-right">
                            <select id="employee_search" name="employee_search">
                                <option value="" selected="selected" >Select Employee</option>
            <?php
            if (!empty($employees)) {
                foreach ($employees as $value) {
                    echo '<option value="' . $value->emp_id . '">' . $value->employee_name . '</option>';
                }
            }
            ?>
                            </select>
                        </div>-->
            @endif

        </div>

        <table id="example1" class="table table-bordered">
            <thead>
                <tr class="success">
                    @foreach( $listing_cols as $col )
                    <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
                    @endforeach
                    <?php if ($role == 'engineer' || $role == 'manager' || $role == 'lead') {
                        ?>
                        @if($show_actions)

                        <th>Actions</th>
                        @endif
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@la_access("Reimbursement_Forms", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Reimbursement Form</h4>
            </div>
            {!! Form::open(['action' => 'LA\Reimbursement_FormsController@store', 'id' => 'reimbursement_form-add-form']) !!}
            <div class="modal-body">


                <div class="box-body">
                    @la_form($module)

                    {{--
					@la_input($module, 'emp_id')
					@la_input($module, 'type_id')
					@la_input($module, 'amount')
					@la_input($module, 'user_comment')
					@la_input($module, 'document_attached')
//                                        @la_input($module, 'verified_level')
					@la_input($module, 'paid_status ')
					@la_input($module, 'verfication_status ')
					@la_input($module, 'hard_copy_accepted')
					@la_input($module, 'payment_date ')
					@la_input($module, 'cosharing')
					@la_input($module, 'cosharing_count')
                                      
					@la_input($module, 'created_by')
					@la_input($module, 'update_by')
					@la_input($module, 'deleted_by')
					@la_input($module, 'date')
					--}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endla_access

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
    $("#example1").DataTable({
    processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            dataType: "json",
                    url: "{{ url(config('laraadmin.adminRoute') . '/reimbursement_form_dt_ajax') }}",
                    type : 'get',
                    data:function(d){
                    d.employee_search = (($('#employee_search').length > 0) ? $('#employee_search').val() : '');
                            d.teamMember = "{{$teamMember}}";
                            filterDatatableData(d);
                    }
            },
            language: {
            lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search"
            },
            @if ($show_actions)
    columnDefs: [ { orderable: false, targets: [ - 1] }],
            @endif
    }
    );
    $("#employee_search").on('change dp.change', function () {
        table.draw();
    });
    $("#reimbursement_form-add-form").validate({

    });
});
function myfunction(button)
{

    var controlid = $(button).attr('id');

    var approved = 0;
    if (controlid == 'Approved')
    {
        var approved = 1;
    }

    $.ajax({
        url: "{{ url('/approvereimbursement') }}",
        type: 'GET',
        data: {
            'approved': approved, 'id': $(button).attr('data-id')
        },

        success: function (data) {
            var vid = $(button).attr('data-id');
            $(button).parents('td').siblings('td').children(".status").parents('td')
                    .html((approved) ? '<span class="text-success status">Approved</span>' : '<span class="text-danger status">Rejected</span>');
            $(button).parents('td').html('Action Taken');
            $('[data-id=' + vid + ']').remove();
            swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
            $('div.overlay').addClass('hide');
            
        }
    });
 

}

</script>
@endpush
