@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/resource_allocations') }}">Modify Allocated Resource</a>
@endsection
@section("contentheader_description", '')
@section("section", "Resource Allocations")
@section("section_url", url(config('laraadmin.adminRoute') . '/resource_allocations'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Resource Allocations Edit : ".$resource_allocation->$view_col)

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
				{!! Form::model($resource_allocation, ['route' => [config('laraadmin.adminRoute') . '.resource_allocations.update', $resource_allocation->id ], 'method'=>'PUT', 'id' => 'resource_allocation-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'project_id')
					@la_input($module, 'employee_id')
					@la_input($module, 'start_date')
					@la_input($module, 'end_date')
					@la_input($module, 'allocation')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/resource_allocations') }}">Cancel</a></button>
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
	$("#resource_allocation-edit-form").validate({
		
	});
});
</script>
@endpush
