@extends('la.layouts.app')

@section('htmlheader_title') Dashboard @endsection
@section('contentheader_title') Dashboard @endsection
@section('contentheader_description') @endsection

@section('main-content')
<?php
 $role = \Session::get('role');
 ?>
<!-- Main content -->
<section class="content">

    <!-- Small boxes (Stat box) -->
    <div class="row">
    <?php if($role=='engineer'){
    ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{$empdetail->available_leaves}}</h3>
                    <p>Available Leaves</p>
                </div>
                <div class="icon">
                    <i class="ion ion-calendar" style="margin-top: 10px"></i>
                </div>
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box <?php echo (($timesheet) ? 'bg-olive' : 'bg-red'); ?> ">
                <div class="inner">
                   @if($timesheet!= 0)
                    <h3> Submitted</h3>
                    @else
                    <h3> Pending</h3>
                    @endif
                   <p>Today's Timesheet</p>
                    <!--               
                    <p>Attendance</p>-->
                </div>
                <div class="icon">
                    <i class="ion ion-ios-list" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow project-toogle">
                <div class="inner">
                  <h3>{{count($Workingprojectname)}}</h3>
                     <p class="child"><a href="#" style="font-size: 15px; color:white">Projects</a> </p>
                    @foreach($Workingprojectname as $Workingprojectnamelist)
                    <div class="Subchild" style="display: none;">{{$Workingprojectnamelist->project_name}}</div> 
                    @endforeach
                    
                </div>
                <div class="icon">
                    <i class="ion ion-ios-albums" style="margin-top: 10px"></i>
                </div>
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{count($holidayname)}}</h3>
               <p class="child"><a href="#" style="font-size: 15px; color:white">Upcoming Holiday(s)</a> </p>
                    @foreach($holidayname as $holidaynamelist)
                    @php
                  
                $day=date('d M Y',  strtotime($holidaynamelist->day));
               @endphp
              
<!--                     <div class="Subchild" style="display: none;" >{{$day}}</div> -->
             
                    <div class="Subchild" style="display: none;" >{{$holidaynamelist->occasion }} ({{$day}})</div> 
              
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-timer" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
    <?php
    }
        else if($role=='manager'||$role=='lead'){
    ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{$empdetail->available_leaves}}</h3>
                    <p>Available Leaves</p>
                </div>
                <div class="icon">
                    <i class="ion ion-calendar" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box <?php echo (($timesheet) ? 'bg-olive' : 'bg-red'); ?> ">
                 <div class="inner">
                   @if($timesheet!= 0)
                    <h3> Submit</h3>
                    @else
                    <h3> Pending</h3>
                    @endif
                   <p>Today's Timesheet</p>
                    <!--               
                    <p>Attendance</p>-->
                </div>
                <div class="icon">
                    <i class="ion ion-ios-list" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ count($teammumber)}}</h3>
                     <p class="child"><a href="#" style="font-size: 15px; color:white">Team Member</a> </p>
                    @foreach($teammumber as $teamlist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;" >{{$teamlist->name}}</div> 
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{count($leaveMaster)}}</h3>
                    <p class="child"><a href="#" style="font-size: 15px; color:white">Absent Employee</a> </p>
                    @foreach($leaveMaster as $leaveMasterlist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;" >{{$leaveMasterlist->employees_name}}</div> 
                    @endforeach
                </div>
                <div class="icon">
                    
                    <i class="ion ion-ios-person"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
    <?php
    }
      
      else{
            
     ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{count($leaveMaster)}}</h3>
                    <p class="child"><a href="#" style="font-size: 15px; color:white">Absent Employee</a> </p>
                    @foreach($leaveMaster as $leaveMasterlist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;" >{{$leaveMasterlist->employees_name}}</div> 
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-person"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{count($employeelist)}}</h3>
                    <p class="child"><a href="#" style="font-size: 15px; color:white">Total Employee</a> </p>
                    @foreach($employeelist as $emplist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;" >{{$emplist->name}}</div> 
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-olive">
                <div class="inner">
                    <h3>{{count($ganitemp)}}</h3>
                    <p class="child"><a href="#" style="font-size: 15px; color:white">TimeSheet Submit</a> </p>
                    @foreach($ganitemp as $ganitemplist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;" >{{$ganitemplist->employees_name}}</div> 
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-list" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{count($projectname)}}</h3>
                     <p class="child"><a href="#" style="font-size: 15px; color:white">Projects</a> </p>
                    @foreach($projectname as $projectnamelist)
                    <!--                      <button>Toggle</button>  -->
                    <div class="Subchild" style="display: none;">{{$projectnamelist->name}}</div> 
                    @endforeach
                </div>
               <div class="icon">
                    <i class="ion ion-ios-albums" style="margin-top: 10px"></i>
                </div>
                <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
    <?php
    }
    ?>
    </div><!-- /.row -->
    

</section><!-- /.content -->
@endsection

@push('styles')
<!-- Morris chart -->
<link rel="stylesheet" href="{{ asset('la-assets/plugins/morris/morris.css') }}">
<!-- jvectormap -->
<link rel="stylesheet" href="{{ asset('la-assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
<!-- Date Picker -->
<link rel="stylesheet" href="{{ asset('la-assets/plugins/datepicker/datepicker3.css') }}">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('la-assets/plugins/daterangepicker/daterangepicker-bs3.css') }}">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="{{ asset('la-assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

@endpush


@push('scripts')



<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{ asset('la-assets/plugins/morris/morris.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('la-assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('la-assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('la-assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('la-assets/plugins/knob/jquery.knob.js') }}"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{ asset('la-assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('la-assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('la-assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('la-assets/plugins/fastclick/fastclick.js') }}"></script>
<!-- dashboard -->
<script src="{{ asset('la-assets/js/pages/dashboard.js') }}"></script>
<script>

$(".project-toogle").click(function ()
{
    $(".Subchild").toggle();
});

</script>
@endpush
