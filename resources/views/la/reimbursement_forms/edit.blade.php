@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_forms') }}">Reimbursement Form</a> :
@endsection
@section("contentheader_description", $reimbursement_form->$view_col)
@section("section", "Reimbursement Forms")
@section("section_url", url(config('laraadmin.adminRoute') . '/reimbursement_forms'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Reimbursement Forms Edit : ".$reimbursement_form->$view_col)

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
				{!! Form::model($reimbursement_form, ['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.update', $reimbursement_form->id ], 'method'=>'PUT', 'id' => 'reimbursement_form-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'emp_id')
					@la_input($module, 'type_id')
					@la_input($module, 'amount')
					@la_input($module, 'user_comment')
					@la_input($module, 'verified_level')
					@la_input($module, 'paid_status')
					@la_input($module, 'document_attached')
					@la_input($module, 'verfication_status')
					@la_input($module, 'hard_copy_accepted')
					@la_input($module, 'payment_date')
					@la_input($module, 'cosharing')
					@la_input($module, 'cosharing_count')
					@la_input($module, 'created_by')
					@la_input($module, 'update_by')
					@la_input($module, 'deleted_by')
					@la_input($module, 'date')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_forms') }}">Cancel</a></button>
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
	$("#reimbursement_form-edit-form").validate({
		
	});
});
</script>
@endpush
