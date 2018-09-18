@extends("la.layouts.app")

@section("contentheader_title", "Comp Off Managements")
@section("contentheader_description", "Comp Off Managements listing")
@section("section", "Comp Off Managements")
@section("sub_section", "Listing")
@section("htmlheader_title", "Comp Off Managements Listing")

@section("headerElems")
@la_access("Comp_Off_Managements", "create")
<!--	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Comp Off Management</button>-->
@endla_access
@endsection

@section("main-content")

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
        <table id="example1" class="table table-bordered">
            <thead>
                <tr class="success">
                    @foreach( $listing_cols as $col )
                    <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
                    @endforeach
                    @if($show_actions)
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@la_access("Comp_Off_Managements", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Comp Off Management</h4>
            </div>
            {!! Form::open(['action' => 'LA\Comp_Off_ManagementsController@store', 'id' => 'comp_off_management-add-form']) !!}
            <div class="modal-body">
                <div class="box-body">
                    @la_form($module)

                    {{--
					@la_input($module, 'employee_id')
					@la_input($module, 'start_date')
					@la_input($module, 'end_date')
					@la_input($module, 'description')
					@la_input($module, 'approved')
					@la_input($module, 'approved_by')
					@la_input($module, 'rejected_by')
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
            ajax: "{{ url(config('laraadmin.adminRoute') . '/comp_off_management_dt_ajax/'.$teamMember) }}",
            language: {
            lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search"
            },
            drawCallback : function (settings) {
            $('.actionCompOff').on('click', function () {approveCompOff($(this)); });
            },
            @if ($show_actions)
    columnDefs: [ { orderable: false, targets: [ - 1] }],
            @endif
    }
    );
    function approveCompOff(button) {
        $('div.overlay').removeClass('hide');
        var controlid = $(button).attr('id');
        var approved = 0;
        if (controlid == 'Approved')
        {
            approved = 1;
        }
        $.ajax({
            url: "{{ url('/approveCompOff') }}",
            type: 'GET',
            data: {approved: approved, id: $(button).attr('data-id'), start_date: $(button).attr('data-start-date'), end_date: $(button).attr('data-end-date')},
            success: function (data) {
                $('div.overlay').addClass('hide');
                swal({
                    title: 'Comp-off has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!'
                }).then(function (isConfirm) {
                    location.reload(true);
                });
            }
        });
    }
});
</script>
@endpush
