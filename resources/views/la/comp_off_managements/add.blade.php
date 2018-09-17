@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/comp_off_managements') }}">Apply for Comp Off</a>
@endsection
@section("section", "Comp Off Managements")
@section("section_url", url(config('laraadmin.adminRoute') . '/comp_off_managements'))


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
            <div class="col-md-10">
                {!! Form::open(['action' => 'LA\Comp_Off_ManagementsController@store', 'id' => 'comp_off_management-add-form']) !!}
                <div class="modal-body">
                    <div class="form-group col-md-3">
                        <label for="start_date">Start Date* :</label>
                        <div class="input-group date">
                            <input class="form-control comp-off" placeholder="Enter Start Date" required="1" name="start_date" id="start_date" type="text" value="" autocomplete="off">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="end_date">End Date* :</label>
                        <div class="input-group date">
                            <input class="form-control comp-off" placeholder="Enter End Date" required="1" name="end_date" id="end_date" type="text" value="" autocomplete="off">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="description">Description* :</label>
                        <input class="form-control" placeholder="Enter Description" data-rule-maxlength="255" required="1" name="description" id="description" type="text" value="">
                    </div>
                    <input type="hidden" name="employee_id" id="employee_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />
                    <div class="form-group col-md-2 mt25">
                        {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
                    </div>
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
        $('[name="start_date"]').parents('.date').data('DateTimePicker').minDate(moment(new Date('2016-08-29')));//.daysOfWeekDisabled([1,2,3,4,5]).initialDate('');
    });
</script>
@endpush
