@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">Task</a> :
@endsection
@section("contentheader_description", $task->$view_col)
@section("section", "Tasks")
@section("section_url", url(config('laraadmin.adminRoute') . '/tasks'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Tasks Edit : ".$task->$view_col)

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
				{!! Form::model($task, ['route' => [config('laraadmin.adminRoute') . '.tasks.update', $task->id ], 'method'=>'PUT', 'id' => 'task-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					@la_input($module, 'start_date')
					@la_input($module, 'end_date')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">Cancel</a></button>
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
	$("#task-edit-form").validate({
		
	});
});
</script>
@endpush
