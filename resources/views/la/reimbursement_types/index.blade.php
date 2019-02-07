@extends("la.layouts.app")
 
@section("contentheader_title", "Reimbursement Types")
@section("contentheader_description", "Reimbursement Types listing")
@section("section", "Reimbursement Types")
@section("sub_section", "Listing")
@section("htmlheader_title", "Reimbursement Types Listing")

@section("headerElems")
@la_access("Reimbursement_Types", "create")
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Reimbursement Type</button>
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

@la_access("Reimbursement_Types", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Reimbursement Type</h4>
			</div>
			{!! Form::open(['action' => 'LA\Reimbursement_TypesController@store', 'id' => 'reimbursement_type-add-form']) !!}
			<div class="modal-body">
				<div class="box-body">
                    @la_form($module)
					
					{{--
					@la_input($module, 'name')
                                       @la_input($module, 'verification_level')
					@la_input($module, 'document_required')
					@la_input($module, 'limit')
					@la_input($module, 'limit_variance')
					@la_input($module, 'employee_grade')
					@la_input($module, 'created_by')
					@la_input($module, 'updated_by')
					@la_input($module, 'deleted_by')
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
                    url: "{{ url(config('laraadmin.adminRoute') . '/reimbursement_type_dt_ajax') }}",
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
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});
	$("#reimbursement_type-add-form").validate({
		
	});
});



$("input[name$='limit_variance']").on('keydown keyup change', function(){
  if ($(this).val() > 100 || $(this).val() < 0){
  swal("Enter No Between 0 to 100");
  $(this).val('0');
  }
      
});
$("input[name$='limit']").on('keydown keyup change', function(){
  if ( $(this).val() <= 0){
  swal("Enter Valid Amount");
    $(this).val('1');
  }
      
});
$("input[name$='verification_level']").on('keydown keyup change', function(){
  if ($(this).val() > 2 || $(this).val() <= 0){
  swal("Enter Valid Level");
    $(this).val('1');
  }
      
});
  
 

  
</script>
@endpush
