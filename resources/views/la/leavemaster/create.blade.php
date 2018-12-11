@extends("la.layouts.app")
@section("contentheader_title")

<?php
// start the session
session_start();
// form token 
$csrf_token = uniqid();

// create form token session variable and store generated id in it.
$_SESSION['csrf_token'] = $csrf_token;
?>
Apply For Leave
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
                <form method="POST" action="{{url(config('laraadmin.adminRoute').'/leaves/store')}}" >
                    <input type="hidden" name="_token" value="{{ csrf_token()}}">

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Manager Name</label>
                            <input type="text" class="form-control" value="{{$manager}}" disabled/>
                        </div>
                        <div class="form-group col-md-3 hide">
                            <label for="Name">Employee Id:</label>
                            <input type="text" class="form-control" name="EmpId" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="EmpId" placeholder="EmpId" required readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Leave Type</label>
                            <select name="LeaveType" id="LeaveType" class="form-control" >
                                <?php
                                if (!empty($leave_types)) {
                                    foreach ($leave_types as $value) {
                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Comp Off Against*</label>
                            <select name="comp_off_id" id="comp_off" class="form-control" >
                                <?php
                                if (!empty($comp_off_list)) {
                                    foreach ($comp_off_list as $value) {
                                        $datetime1 = date_create($value->start_date);
                                        $datetime2 = date_create($value->end_date);
                                        $interval = date_diff($datetime1, $datetime2);
                                        $days = $interval->format('%a') + 1;
                                        echo '<option value="' . $value->id . '" data-days="' . $days . '" data-start = "' . $value->start_date . '">' . date('d M', strtotime($value->start_date)) . ' - ' . date('d M', strtotime($value->end_date)) . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-3">

                            <label for="StartDate" class="control-label">Start Date*</label>

                            <input type="text" value="{{ old('FromDate')}}" class="form-control" 
                                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="From" readonly='true' />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="EndDate" class="control-label">End Date*</label>
                            <input type="text" value="{{ old('ToDate')}}" class="form-control " id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="To" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' />	
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Name">Number Of Days</label>
                            <input type="text" value="{{ old('NoOfDays')}}" class="form-control" readonly="readonly" name="NoOfDays" id="NoOfDays" autocomplete="off" >

                        </div>
                        <div class="form-group col-md-3">
                            <label for="Number">Leave Purpose*</label>
                            <input type="text" value="{{ old('LeaveReason')}}" class="form-control" name="LeaveReason" autocomplete="off" required placeholder="Reason" maxlength="180"  >   
                        </div>
                        <div class="form-group col-md-3" style="display: none" id="halfday">
                            <label for="half_day">Half Day</label>
                            <input class="duration form-control" type="checkbox" name="half_day" id="half_day" /> 
                        </div>
                        
                        <div class="col-md-3" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-success">Submit</button>
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
        var emp_detail = "{{ Session::get('employee_details') }}";
        emp_detail = JSON.parse(emp_detail.replace(/&quot;/g, '\"'));
        //get dates from session
        var dates = "{{ Session::get('holiday_list') }}";
        dates = JSON.parse(dates.replace(/&quot;/g, '\"'));
        
          $("#half_day").change(function(){     
             var daysDiff=$("#NoOfDays").val();
            if(daysDiff == 1 && $("#half_day"). prop("checked") == true){             
                daysDiff = .5;
               $("#NoOfDays").val(daysDiff);
             return daysDiff;
            }else{
                 $("#NoOfDays").val(1);
                return daysDiff;
            }            
        });

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

            if (daysDiff == 1) {
                 $("#halfday").show();
                
                               
            }else{                
                 $('#half_day').prop('checked', false);
                 $("#halfday").hide();
            }
            if(daysDiff==0){
               daysDiff = 1; 
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
            $('#half_day').prop('checked', false);
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
                var leave_type_id = $(leaveType).find('option:selected').val();
                //in case of comp off
                if (leave_type_id === '7') {
                    $('#comp_off').attr('required', true).parents('div.col-md-3').show();
                    datesAgainstCompoff(true, $('#comp_off'));
                } else if (leave_type_id == 8) { //in case of birthday
                    var fullDate = new Date(emp_detail.date_birth);
                    fullDate.setFullYear(new Date().getFullYear());
                    if (fullDate < new Date()) {
                        fullDate.setFullYear(new Date().getFullYear() + 1);
                    } 
                    var birthday = fullDate.toShortFormat();
                    $('#datepicker, #datepickerto').datepicker('setStartDate', birthday).datepicker('setEndDate', birthday).datepicker('setDate', birthday);
                    $('#comp_off').attr('required', false).parents('div.col-md-3').hide();
                } else {
                    $('#comp_off').attr('required', false).parents('div.col-md-3').hide();
                    datesAgainstCompoff(false, ''); //to set start and end dates for selection
                }
            } else {
                datesAgainstCompoff(false, '');
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

        $('#comp_off').on('change', function () {
            datesAgainstCompoff(this);
        });

        function datesAgainstCompoff(isCompOff, compOffSelected) {
            if (isCompOff) {
                var start_date = new Date($(compOffSelected).find('option:selected').attr('data-start'));
                var end_date = new Date($(compOffSelected).find('option:selected').attr('data-start'));
                end_date.setDate(end_date.getDate() + 30);
            } else {
                var start_date = '-{{ $before_days }}d';
                var end_date = '+{{ $after_days }}d';
            }
            $('#datepicker, #datepickerto').datepicker('setStartDate', start_date).datepicker('setEndDate', end_date).datepicker('setDate', '');
        }
        
        
    });



</script>



@endpush
