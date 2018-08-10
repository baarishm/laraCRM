@extends("la.layouts.app")
<style>
    .group{
        background-color: #eee;
    }
    .group td{
        text-align:center;
    }
</style>
@section("contentheader_title", "Timesheets")
@section("contentheader_description", "Timesheets listing")
@section("section", "Timesheets")
@section("sub_section", "Listing")
@section("htmlheader_title", "Timesheets Listing")

@section("headerElems")

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

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
    var groupColumn = 2;
    $("#example1").DataTable({
    processing: true,
            serverSide: true,
            ajax: "{{ url(config('laraadmin.adminRoute') . '/timesheet_dt_ajax') }}",
            language: {
            lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search"
            },
            "order": [[ groupColumn, 'desc' ]],
            "drawCallback": function (settings) {
            var api = this.api();
                    var rows = api.rows({page:'current'}).nodes();
                    var last = null;
                    api.column(groupColumn, {page:'current'}).data().each(function (group, i) {
            if (last !== group) {
            $(rows).eq(i).before(
                    '<tr class="group"><td colspan="5">' + group + '</td></tr>'
                    );
                    last = group;
            }
            });
            },
            "columnDefs": [
            { "visible": false, "targets": groupColumn }
            ],
            @if ($show_actions)
    columnDefs: [ { orderable: false, targets: [ - 1] }],
            @endif
    }
    );
    $('input[type="search"][aria-controls="example1"]').hide();
    $("#timesheet-add-form").validate({

    });
});
</script>
@endpush
