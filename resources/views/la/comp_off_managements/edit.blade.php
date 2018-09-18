@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/comp_off_managements') }}">Comp Off Management</a> :
@endsection
@section("contentheader_description", $comp_off_management->$view_col)
@section("section", "Comp Off Managements")
@section("section_url", url(config('laraadmin.adminRoute') . '/comp_off_managements'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Comp Off Managements Edit : ".$comp_off_management->$view_col)

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
				{!! Form::model($comp_off_management, ['route' => [config('laraadmin.adminRoute') . '.comp_off_managements.update', $comp_off_management->id ], 'method'=>'PUT', 'id' => 'comp_off_management-edit-form']) !!}
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
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/comp_off_managements') }}">Cancel</a></button>
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
	$("#comp_off_management-edit-form").validate({
		
	});
});
</script>
@endpush
