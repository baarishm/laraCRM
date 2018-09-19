@extends("la.layouts.app")
@section("contentheader_title")
Edit Apply  Leave
@endsection
@section("main-content")
@if(count($errors))
<div class="form-group">
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
<div class="box entry-form">
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">


                <form method="post" action="{{action('LA\LeaveMasterController@update', $leaveMaster - > id)}}">

                    <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="row">

                        <div class="form-group col-md-3 hide">
                            <label for="name">Employee Id:</label>
                            <input type="text" class ="form-control" autocomplete="off" readonly="readonly" name="EmpId" value="{{$leaveMaster - > EmpId}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Manager Name</label>
                            <input type="text" class="form-control" value="{{$manager}}" disabled/>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Leave Type</label>
                            <select name="LeaveType" class="form-control" >

                                <?php
                                if (!empty($leaveMaster->leave_type)) {
                                    foreach ($leaveMaster->leave_type as $value) {
                                        echo '<option value="' . $value->id . '" ' . (($leaveMaster->LeaveType == $value->id) ? 'selected' : '' ) . '>' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Comp Off Against*</label>
                            <select name="comp_off" id="comp_off" class="form-control" >
                                <?php
                                if (!empty($comp_off_list)) {
                                    foreach ($comp_off_list as $value) {
                                        $datetime1 = date_create($value->start_date);
                                        $datetime2 = date_create($value->end_date);
                                        $interval = date_diff($datetime1, $datetime2);
                                        $days = $interval->format('%a') + 1;
                                        echo '<option value="' . $value->id . '" data-days="' . $days . '"' . (($leaveMaster->comp_off_id == $value->id) ? 'selected' : '' ) . '>' . date('d M', strtotime($value->start_date)) . ' - ' . date('d M', strtotime($value->end_date)) . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="StartDate" class="control-label">Start Date:</label>
                            <input type="text" class="form-control " 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required  readonly='true' value="{{$leaveMaster - > FromDate or old('FromDate')}}" />

                        </div>
                        <div class="form-group col-md-3">
                            <label for="text" class="control-label">End Date:</label>

                            <input type="text" class="form-control" id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' value="{{$leaveMaster - > ToDate or old('ToDate')}}" />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Number Of Days</label>
                            <input type="text" class="form-control" name="NoOfDays" autocomplete="off" readonly="readonly" id="NoOfDays" value="{{$leaveMaster - > NoOfDays or old('NoOfDays')}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="number">Leave Purpose*</label>

                            <input type="text" class="form-control" name="LeaveReason" autocomplete="off"  placeholder="Leave Purpose" required maxlength="180" value="{{$leaveMaster - > LeaveReason or old('LeaveReason')}}"> 
                        </div>
                        <div class="form-group col-md-3" style="margin-top:25px">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script type="text/javascript">




    $(document).ready(function () {

        //get dates from session
        var dates = "{{ Session::get('holiday_list') }}";
        dates = JSON.parse(dates.replace(/&quot;/g, '\"'));

        // To calulate difference b/w two dates
        function CalculateDiff(first, last)
        {
            var aDay = 24 * 60 * 60 * 1000,
                    daysDiff = parseInt((last.getTime() - first.getTime()) / aDay, 10) + 1;

            if (daysDiff > 0) {
                for (var i = first.getTime(), lst = last.getTime(); i <= lst; i += aDay) {
                    var d = new Date(i);
                    var date = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
                    if (d.getDay() == 6 || d.getDay() == 0 // weekend
                            || dates[date]) {
                        daysDiff--;
                    }
                }
            }

            $("#NoOfDays").val(daysDiff);
            return daysDiff;
        }


        $('#datepicker').datepicker({
            todayHighlight: 'true',
            format: 'd M yyyy',
            daysOfWeekDisabled: [0, 6],
            startDate: '-{{ $before_days }}d',
            endDate: '+{{ $after_days }}d',
            beforeShowDay: function (date) {
                var date = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
                var Highlight = dates[date];
                var re = [];
                if (Highlight) {
                    re = {enabled: false, classes: "Highlighted tooltips", tooltip: dates[date]['occasion']};
                } else {
                    re = {enabled: true};
                }
                return re;
            }

        }).on('changeDate', function (e) {
            $("#datepickerto").val('');
            $("#NoOfDays").val('');
            $("#datepickerto").datepicker('setStartDate', e.date).datepicker("setDate", e.date);
            CalculateDiff(new Date($("#datepicker").datepicker("getDate")), new Date($("#datepickerto").datepicker("getDate")));
        });

        $("#datepickerto").datepicker({
            todayHighlight: 'true',
            autoclose: true,
            format: 'd M yyyy',
            daysOfWeekDisabled: [0, 6],
            startDate: '-{{ $before_days }}d',
            endDate: '+{{ $after_days }}d',
            beforeShowDay: function (date) {
                var date = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
                var Highlight = dates[date];
                if (Highlight) {
                    re = {enabled: false, classes: "Highlighted", tooltip: 'Holiday'};
                } else {
                    re = {enabled: true};
                }
                return re;
            }

        }).on('changeDate', function () {
            CalculateDiff(new Date($("#datepicker").datepicker("getDate")), new Date($("#datepickerto").datepicker("getDate")));
        });

        $('select').select2();

        //Show/hide comp off list
        $('#LeaveType').on('change', function () {
            leaveType(this);
        });

        leaveType($('#LeaveType'));

        function leaveType(leaveType) {
            if ($(leaveType).find('option:selected').length > 0) {
                if ($(leaveType).find('option:selected').html() === 'Comp Off') {
                    $('#comp_off').attr('required', true).parents('div.col-md-3').show();
                } else {
                    $('#comp_off').attr('required', false).parents('div.col-md-3').hide();
                }
            } else {
                $('#comp_off').attr('required', false).parents('div.col-md-3').hide();
            }
        }

        //on submit validate fields
        $('button[type="submit"]').on('click', function (e) {
            $('div.overlay').removeClass('hide');
            e.preventDefault();
            if (($('#LeaveType').find('option:selected').html() === 'Comp Off') && $('#comp_off option:selected').attr('data-days') < $('#NoOfDays').val()) {
                $('div.overlay').addClass('hide');
                swal('Smarty, You cannot avail leaves more then days for this comp-off!');
                return false;
            } else {
                if (validateFields($('[required]'))) {
                    $(this).parents('form').submit();
                } else {
                    $('div.overlay').addClass('hide');
                    swal('Please fill all required fields!');
                    return false;
                }
            }
        });
    }
    );



</script>
@endpush