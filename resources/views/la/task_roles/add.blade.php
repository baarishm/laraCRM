@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/task_roles') }}">Task Role</a> :
@endsection
@section("section", "Task Roles")
@section("section_url", url(config('laraadmin.adminRoute') . '/task_roles'))


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
                {!! Form::open(['action' => 'LA\Task_RolesController@store', 'id' => 'task_roles-add-form']) !!}
                @la_form($module)

                {{--
					@la_input($module, 'role_id')
					@la_input($module, 'task_id')
					--}}
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/task_roles') }}">Cancel</a></button>
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
		$('select[name="role_id"]').prepend("<option value='0' selected='selected'>Common</option>");
    });
</script>
@endpush
