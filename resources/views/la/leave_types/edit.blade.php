@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/leave_types') }}">Leave Type</a> :
@endsection
@section("contentheader_description", $leave_type->$view_col)
@section("section", "Leave Types")
@section("section_url", url(config('laraadmin.adminRoute') . '/leave_types'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Leave Types Edit : ".$leave_type->$view_col)

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
				{!! Form::model($leave_type, ['route' => [config('laraadmin.adminRoute') . '.leave_types.update', $leave_type->id ], 'method'=>'PUT', 'id' => 'leave_type-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/leave_types') }}">Cancel</a></button>
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
	$("#leave_type-edit-form").validate({
		
	});
});
</script>
@endpush
