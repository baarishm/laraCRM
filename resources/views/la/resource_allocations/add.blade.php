@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/resource_allocations') }}">Allocate Resource</a>
@endsection
@section("section", "Resource Allocations")
@section("section_url", url(config('laraadmin.adminRoute') . '/resource_allocations'))


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
                {!! Form::open(['action' => 'LA\Resource_AllocationsController@store', 'id' => 'resource_allocation-add-form']) !!}
                <div class="modal-body">
                    <div class="box-body">
                        @la_form($module)

                        {{--
					@la_input($module, 'project_id')
					@la_input($module, 'employee_id')
					@la_input($module, 'start_date')
					@la_input($module, 'end_date')
					@la_input($module, 'allocation')
					--}}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}">Cancel</a></button>
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
        console.log({{ Session::get('holiday_list') }});
        $('[name="allocation"]').attr('min', '5').attr('max', '100');
        $('.date').data("DateTimePicker").minDate(moment('2016-08-29'));
    });
</script>
@endpush
