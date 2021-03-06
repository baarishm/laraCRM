@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/employees') }}">Edit Employee</a>
@endsection
@section("contentheader_description", $employee->$view_col)
@section("section", "Employees")
@section("section_url", url(config('laraadmin.adminRoute') . '/employees'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Employees Edit ")

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
        <div class="pull-right"><h3>
                <?php echo ($employee->deleted_at == '') ? '<span class="text-success display-3">Active</span>' : '<span class="text-danger">Inactive</span>'; ?>
            </h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::model($employee, ['route' => [config('laraadmin.adminRoute') . '.employees.update', $employee->id ], 'method'=>'PUT', 'id' => 'employee-edit-form']) !!}
                @la_form($module)

                {{--
					@la_input($module, 'name')
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
					@la_input($module, 'date_hire')
					@la_input($module, 'is_confirmed')
					--}}
                @if(Entrust::hasRole('SUPER_ADMIN'))
                <div class="form-group">
                    <label for="role">Role* :</label>
                    <select class="form-control" required="1" data-placeholder="Select Role" rel="select2" name="role">
                        <?php $roles = App\Role::all(); ?>
                        @foreach($roles as $role)
                        @if($role->id != 1 || Entrust::hasRole("SUPER_ADMIN"))
                        @if($user->hasRole($role->name))
                        <option value="{{ $role->id }}" selected>{{ $role->display_name }}</option>
                        @else
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endif
                        @endif
                        @endforeach
                    </select>
                </div>
                @endif
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/employees') }}">Cancel</a></button>
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
        var role = <?php echo ((Entrust::hasRole("SUPER_ADMIN") != '') ? 'true' : 'false'); ?>;
        if (role === false) {
            $('[name = "is_confirmed"]').siblings('.Switch').css({'pointer-events': 'none'});
            $('[name = "emp_code"]').attr('disabled', 'true');
            $('[name = "first_approver"]').attr('disabled', 'true');
            $('[name = "second_approver"]').attr('disabled', 'true');
        }
    });
</script>
@endpush
