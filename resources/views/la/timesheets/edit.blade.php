@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Timesheet</a> :
@endsection
@section("contentheader_description", $timesheet->$view_col)
@section("section", "Timesheets")
@section("section_url", url(config('laraadmin.adminRoute') . '/timesheets'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Timesheets Edit : ".$timesheet->$view_col)

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
				{!! Form::model($timesheet, ['route' => [config('laraadmin.adminRoute') . '.timesheets.update', $timesheet->id ], 'method'=>'PUT', 'id' => 'timesheet-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'submitor_id')
					@la_input($module, 'project_id')
					@la_input($module, 'task_id')
					@la_input($module, 'date')
					@la_input($module, 'hours')
					@la_input($module, 'minutes')
					@la_input($module, 'comments')
					@la_input($module, 'dependency')
					@la_input($module, 'dependency_for')
					@la_input($module, 'dependent_on')
					@la_input($module, 'lead_id')
					@la_input($module, 'manager_id')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Cancel</a></button>
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
	$("#timesheet-edit-form").validate({
		
	});
});
</script>
@endpush
