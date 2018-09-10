@extends("la.layouts.app")

@section("contentheader_title", "Resource Allocations")
@section("contentheader_description", "Resource Allocations listing")
@section("section", "Resource Allocations")
@section("sub_section", "Listing")
@section("htmlheader_title", "Resource Allocations Listing")

@section("headerElems")
@la_access("Resource_Allocations", "create")
<!--<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Resource Allocation</button>-->
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
        <div class="row ">
            <div class="col-md-2 pull-right">
                <input type="text" id="date_search" placeholder="Search by Date" class="search">
            </div>

            <div class="col-md-2 pull-right">
                <select id="project_search" name="project_search">
                    <option value="0" selected="selected" >Select Project Name</option>
                    <?php
                    if (!empty($projects)) {
                        foreach ($projects as $value) {
                            echo '<option value="' . $value->project_id . '">' . $value->project_name . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 pull-right">
                <select id="employee_search" name="employee_search">
                    <option value="" selected="selected" >Select Employee</option>
                    <?php
                    if (!empty($employees)) {
                        foreach ($employees as $value) {
                            echo '<option value="' . $value->employee_id . '">' . $value->employee_name . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
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

@la_access("Resource_Allocations", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Resource Allocation</h4>
            </div>
            {!! Form::open(['action' => 'LA\Resource_AllocationsController@store', 'id' => 'resource_allocation-add-form']) !!}
            <div class="modal-body">
                <div class="box-body">
                    @la_form($module)

                    {{--
					@la_input($module, 'project_id')
					@la_input($module, 'employee_id')
					@la_input($module, 'start_date')
					@la_input($module, 'end_date')
					@la_input($module, 'allocation')
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
    var table = $("#example1").DataTable({
    processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url:"{{ url(config('laraadmin.adminRoute') . '/resource_allocation_dt_ajax') }}",
                    type : 'get',
                    data:function(d){
                            d.project_search = $('#project_search').val();
                            d.date_search = $('#date_search').val();
                            d.employee_search = (($('#employee_search').length > 0) ? $('#employee_search').val() : '');
                    }
            },
            language: {
            lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search"
            },
            columnDefs: [ {"searchable": false, "targets": [3, 4]}],
            @if ($show_actions)
    columnDefs: [ { orderable: false, targets: [ - 1] }, {"searchable": false, "targets": [3, 4]}],
            @endif
    }
    );

    $("#project_search, #date_search, #employee_search").on('change dp.change', function () {
        table.draw();
    });
    
    $('#project_search, #employee_search').select2();
});
</script>
@endpush
