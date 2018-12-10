@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_types') }}">Reimbursement Type</a> :
@endsection
@section("contentheader_description", $reimbursement_type->$view_col)
@section("section", "Reimbursement Types")
@section("section_url", url(config('laraadmin.adminRoute') . '/reimbursement_types'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Reimbursement Types Edit : ".$reimbursement_type->$view_col)

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

<div class="box">
	<div class="box-header">
		
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($reimbursement_type, ['route' => [config('laraadmin.adminRoute') . '.reimbursement_types.update', $reimbursement_type->id ], 'method'=>'PUT', 'id' => 'reimbursement_type-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					@la_input($module, 'verification_level')
					@la_input($module, 'document_required')
					@la_input($module, 'limit')
					@la_input($module, 'limit_variance')
					@la_input($module, 'employee grade ')
					@la_input($module, 'created_by')
					@la_input($module, 'updated_by')
					@la_input($module, 'deleted_by')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_types') }}">Cancel</a></button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
	$("#reimbursement_type-edit-form").validate({
		
	});
});
</script>
@endpush
