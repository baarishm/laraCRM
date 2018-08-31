@extends("la.layouts.app")
<style>
    .group{
        background-color: #eee;
    }
    .group td{
        text-align:center;
    }
    .search{
        float : right;
        margin:5px;
        border:none;
        border-bottom: 1px solid #9a9999;
    }
    .search:focus {
        outline: none; 
        border-bottom: 1px solid #48b7d2;
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
        </div>
       <!-- <input type="text" id="project_search" placeholder="Search by Project Name" class="search">  -->
        <table id="example1" class="table table-bordered">
            <thead>
                <tr class="success">
                    @foreach( $listing_cols as $col )
                    <th style="width:10%">{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
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

    var groupColumn = 3;
    var table = $("#example1").DataTable({
    processing: true,
            serverSide: true,
            searchable: false,
            ajax: {
            url:"{{ url(config('laraadmin.adminRoute') . '/timesheet_dt_ajax') }}",
                    type : 'get',
                    data:function(d){
                            d.project_search = $('#project_search').val();
                            d.date_search = $('#date_search').val();
                            d.teamMember = "{{$teamMember}}";
                    }
            },
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
                    var date_to_show = new Date(group);
            $(rows).eq(i).before(
                    '<tr class="group"><td colspan="6">' + date_to_show.toShortFormat() + '</td></tr>'
                    );
                    last = group;
            }
            });
            },
            "columnDefs": [
            { "visible": false, "targets": groupColumn },
            { "width": "7%", "targets": 0 },
            { "width": "7%", "targets": 1 },
            { "width": "7%", "targets": 2 },
            { "width": "5%", "targets": 3 },
            { "width": "4%", "targets": 4 }
            ],
            @if ($show_actions)
                columnDefs: [ 
                { orderable: false, targets: [ - 1] }, 
                { "visible": false, "targets": groupColumn },
                { "width": "7%", "targets": 0 },
                { "width": "25%", "targets": 1 },
                { "width": "25%", "targets": 2 },
                { "width": "25%", "targets": 3 },
                { "width": "4%", "targets": 4 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 }
                ],
            @endif
    }
    );
    $("#project_search, #date_search").on('change dp.change', function () {
        table.draw();
    });

    $('#project_search').select2();
    $('input[type="search"][aria-controls="example1"]').hide();
});
</script>
@endpush
