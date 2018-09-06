@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/task_roles') }}">Task Role</a> :
@endsection
@section("contentheader_description", $task_role->$view_col)
@section("section", "Task Roles")
@section("section_url", url(config('laraadmin.adminRoute') . '/task_roles'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Task Roles Edit : ".$task_role->$view_col)

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
				{!! Form::model($task_role, ['route' => [config('laraadmin.adminRoute') . '.task_roles.update', $task_role->id ], 'method'=>'PUT', 'id' => 'task_role-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'role_id')
					@la_input($module, 'task_id')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/task_roles') }}">Cancel</a></button>
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
	$('select[name="role_id"]').prepend("<option value='0'>Common</option>");
        if($('select[name="role_id"] option[selected]').length == 0){
            $('select[name="role_id"] option:first').attr('selected', true);
        }
});
</script>
@endpush
