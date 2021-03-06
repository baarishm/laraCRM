@extends("la.layouts.app")

@section("contentheader_title", "Feedback")
@section("contentheader_description", "Feedback listing")
@section("section", "Feedback")
@section("sub_section", "Listing")
@section("htmlheader_title", "Feedback Listing")

@section("headerElems")
@la_access("Feedback", "create")
<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Feedback</button>
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

@la_access("Feedback", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Feedback</h4>
            </div>
            {!! Form::open(['action' => 'LA\FeedbackController@store', 'id' => 'feedback-add-form']) !!}
            <div class="modal-body">
                <div class="box-body">
                    <div class="form-group">
                        <label for="type">Suggestion Type* :</label>
                        <select class="form-control select2-hidden-accessible" required="1" data-placeholder="Enter Suggestion Type" rel="select2" name="type" id="type" tabindex="-1" aria-hidden="true" aria-required="true">
                            <option value="Technical" selected="selected">Technical</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suggestion">Suggestion* :</label>
                        <textarea class="form-control" placeholder="Enter Suggestion" required="1" cols="30" rows="3" name="suggestion" aria-required="true"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="employee_id" id="employee_id" value="{{ base64_encode(base64_encode(Auth::user()->context_id))}}" />
                    </div>
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
            ajax:  {
            url: "{{ url(config('laraadmin.adminRoute') . '/feedback_dt_ajax') }}",
                    type : 'get',
                    data:function(d){
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

    $('#type').select2();
    $("#feedback-add-form").validate({

    });
});
</script>
@endpush
