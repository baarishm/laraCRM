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
                <div class="form-group">
                    <label for="employee_id">Project Name* :</label>
                    <select class="form-control" name="project_id" id="project_id" required>
                        @foreach($projects as $project)
                        <option value="{{$project->id}}">{{ucwords($project->name)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="employee_id">Employee Name* :</label>
                    <select class="form-control" name="employee_id" id="employee_id" required>
                        @foreach($employees as $employee)
                        <option value="{{$employee->id}}">{{ucwords($employee->name)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">From* :</label>
                    <div class="input-group date">
                        <input class="form-control" placeholder="Enter From" required="1" name="start_date" type="text" autocomplete="off">
                        <span class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date">To* :</label>
                    <div class="input-group date">
                        <input class="form-control" placeholder="Enter To" required="1" name="end_date" type="text" autocomplete="off">
                        <span class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="allocation">Allocation (in %)* :</label>
                    <input class="form-control" placeholder="Enter Allocation (in %)" required="1" name="allocation" type="number" value="0" min="5" max="100">
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
        $('[name="allocation"]').attr('min', '5').attr('max', '100');
        $('.date').data("DateTimePicker").minDate(moment('2016-08-29'));
        $('select').select2();
    });
</script>
@endpush
