@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/roles') }}">Roles</a> :
@endsection
@section("section", "Roles")
@section("section_url", url(config('laraadmin.adminRoute') . '/roles'))


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
            <div class="col-md-8 col-md-offset-2 form-input">
                {!! Form::open(['action' => 'LA\RolesController@store', 'id' => 'role-add-form']) !!}
                @la_input($module, 'name', null, null, "form-control text-uppercase", ["placeholder" => "Role Name in CAPITAL LETTERS with '_' to JOIN e.g. 'SUPER_ADMIN'"])
                @la_input($module, 'display_name')
                @la_input($module, 'parent')
                @la_input($module, 'dept')
                @la_input($module, 'description')
            
                <br>
                <div class="form-group text-right" style="width: 88%;float:none;clear: both;">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!} <button class="btn btn-default cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}">Cancel</a></button>
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
