@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/managers') }}">Manager</a> :
@endsection
@section("contentheader_description", $manager->$view_col)
@section("section", "Managers")
@section("section_url", url(config('laraadmin.adminRoute') . '/managers'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Managers Edit : ".$manager->$view_col)

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
				{!! Form::model($manager, ['route' => [config('laraadmin.adminRoute') . '.managers.update', $manager->id ], 'method'=>'PUT', 'id' => 'manager-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'employee_id')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/managers') }}">Cancel</a></button>
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
	$("#manager-edit-form").validate({
		
	});
});
</script>
@endpush
