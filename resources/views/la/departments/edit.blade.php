@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/departments') }}">Department</a> :
@endsection
@section("contentheader_description", $department->$view_col)
@section("section", "Departments")
@section("section_url", url(config('laraadmin.adminRoute') . '/departments'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Departments Edit : ".$department->$view_col)

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
				{!! Form::model($department, ['route' => [config('laraadmin.adminRoute') . '.departments.update', $department->id ], 'method'=>'PUT', 'id' => 'department-edit-form']) !!}
<!--					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					@la_input($module, 'color')
					@la_input($module, 'tags')
					--}}-->

 <div class="row">
                         <div class="form-group col-md-12">
                            <label>Name</label>
                            <input type="text" class="form-control"  value="{{$department -> name or old('name')}}" id="name" name="name" />
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Name">color</label>
                            <input type="text" class="form-control" name="color" autocomplete="off"  value="{{$department -> color or old('color')}}" id="color">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="tags" class="control-label">tags</label>
                            <input class="form-control" id="tags" name="tags"  type="text" readonly="true"  value="{{$department -> tags or old('tags')}}"/>
                        </div>




                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/departments') }}">Cancel</a></button>
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
	$("#department-edit-form").validate({
		
	});
});
</script>
@endpush
