@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/employees') }}">Employees</a> :
@endsection
@section("section", "Employees")
@section("section_url", url(config('laraadmin.adminRoute') . '/employees'))


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
                {!! Form::open(['action' => 'LA\EmployeesController@store', 'id' => 'employee-add-form']) !!}
                @la_form($module)

                {{--
					@la_input($module, 'name')
					@la_input($module, 'designation')
					@la_input($module, 'gender')
					@la_input($module, 'mobile')
					@la_input($module, 'mobile2')
					@la_input($module, 'email')
					@la_input($module, 'date_birth')
					@la_input($module, 'city')
					@la_input($module, 'address')
					@la_input($module, 'about')
					@la_input($module, 'first_approver')
					@la_input($module, 'second_approver')
					@la_input($module, 'dept')
					@la_input($module, 'project_id')
					@la_input($module, 'salary_cur')
					@la_input($module, 'date_hire')
					@la_input($module, 'date_left')
					--}}
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}">Cancel</a></button>
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
        $("#project-edit-form").validate({

        });
    });
</script>
@endpush
