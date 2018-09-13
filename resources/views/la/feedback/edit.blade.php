@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/feedback') }}">Feedback</a> :
@endsection
@section("contentheader_description", $feedback->$view_col)
@section("section", "Feedback")
@section("section_url", url(config('laraadmin.adminRoute') . '/feedback'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Feedback Edit : ".$feedback->$view_col)

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
				{!! Form::model($feedback, ['route' => [config('laraadmin.adminRoute') . '.feedback.update', $feedback->id ], 'method'=>'PUT', 'id' => 'feedback-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'employee_id')
					@la_input($module, 'type')
					@la_input($module, 'suggestion')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/feedback') }}">Cancel</a></button>
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
	$("#feedback-edit-form").validate({
		
	});
});
</script>
@endpush
