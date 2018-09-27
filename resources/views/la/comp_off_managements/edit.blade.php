@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/comp_off_managements') }}">Edit Application</a>
@endsection
@section("section", "Comp Off Managements")
@section("section_url", url(config('laraadmin.adminRoute') . '/comp_off_managements'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Comp Off Managements Edit : ".$comp_off_management->$view_col)

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
                {!! Form::model($comp_off_management, ['route' => [config('laraadmin.adminRoute') . '.comp_off_managements.update', $comp_off_management->id ], 'method'=>'PUT', 'id' => 'comp_off_management-edit-form']) !!}
                <div class="modal-body">
                    <div class="form-group col-md-3">
                        <label for="start_date">Start Date* :</label>
                        <div class="input-group date">
                            <input class="form-control comp-off" placeholder="Enter Start Date" required="1" name="start_date" id="start_date" type="text" autocomplete="off" value="{{$comp_off_management->start_date}}">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 hide">
                        <label for="end_date">End Date* :</label>
                        <div class="input-group date" style="pointer-events: none;">
                            <input class="form-control comp-off" placeholder="Enter End Date" required="1" name="end_date" id="end_date" type="text" autocomplete="off"  value="{{$comp_off_management->end_date}}">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="description">Description* :</label>
                        <input class="form-control" placeholder="Enter Description" data-rule-maxlength="255" required="1" name="description" id="description" type="text" value="{{$comp_off_management->description}}">
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
        $('[name="start_date"]').parents('.date').data('DateTimePicker').minDate(moment(new Date()).subtract('30', 'days')).maxDate(moment(new Date()));//.daysOfWeekDisabled([1,2,3,4,5]).initialDate('');
        $('[name="start_date"]').parents('.date').on('dp.change', function () {
            $('[name="end_date"]').parents('.date').data('DateTimePicker')
                    .date(moment(new Date($('[name="start_date"]').val())));
        });
    });
</script>
@endpush
