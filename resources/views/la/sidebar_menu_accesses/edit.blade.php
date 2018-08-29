@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/sidebar_menu_accesses') }}">Sidebar Menu Access</a> :
@endsection
@section("contentheader_description", $sidebar_menu_access->$view_col)
@section("section", "Sidebar Menu Accesses")
@section("section_url", url(config('laraadmin.adminRoute') . '/sidebar_menu_accesses'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Sidebar Menu Accesses Edit : ".$sidebar_menu_access->$view_col)

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
				{!! Form::model($sidebar_menu_access, ['route' => [config('laraadmin.adminRoute') . '.sidebar_menu_accesses.update', $sidebar_menu_access->id ], 'method'=>'PUT', 'id' => 'sidebar_menu_access-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'role_id')
					@la_input($module, 'menu_id')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/sidebar_menu_accesses') }}">Cancel</a></button>
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
	$("#sidebar_menu_access-edit-form").validate({
		
	});
});
</script>
@endpush
