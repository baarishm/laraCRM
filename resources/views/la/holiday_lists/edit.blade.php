@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/holiday_lists') }}">Holiday List</a> :
@endsection
@section("contentheader_description", $holiday_list->$view_col)
@section("section", "Holiday Lists")
@section("section_url", url(config('laraadmin.adminRoute') . '/holiday_lists'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Holiday Lists Edit : ".$holiday_list->$view_col)

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
				{!! Form::model($holiday_list, ['route' => [config('laraadmin.adminRoute') . '.holiday_lists.update', $holiday_list->id ], 'method'=>'PUT', 'id' => 'holiday_list-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'day')
					@la_input($module, 'occasion')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/holiday_lists') }}">Cancel</a></button>
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
	$("#holiday_list-edit-form").validate({
		
	});
});
</script>
@endpush
