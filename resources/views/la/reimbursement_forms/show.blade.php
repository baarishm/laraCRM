@extends('la.layouts.app')

@section('htmlheader_title')
Reimbursement Form View
@endsection
@section('main-content')
<?php
// start the session
session_start();
// form token 
$csrf_token = uniqid();

// create form token session variable and store generated id in it.
$_SESSION['csrf_token'] = $csrf_token;
?>
<?php
$role = \Session::get('role');
?>

<div id="page-content" class="profile2">
    <div class="bg-primary clearfix">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="profile-icon text-primary"><i class="fa {{ $module->fa_icon }}"></i></div>
                </div>
                <div class="col-md-9">
                    <h4 class="name">{{ $reimbursement_form->$view_col }}</h4>
                    <div class="row stats">
                        <div class="col-md-4"><i class="fa fa-facebook"></i> 234</div>
                        <div class="col-md-4"><i class="fa fa-twitter"></i> 12</div>
                        <div class="col-md-4"><i class="fa fa-instagram"></i> 89</div>
                    </div>
                    <p class="desc">Test Description in one line</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dats1"><div class="label2">Admin</div></div>
            <div class="dats1"><i class="fa fa-envelope-o"></i> superadmin@gmail.com</div>
            <div class="dats1"><i class="fa fa-map-marker"></i> Pune, India</div>
        </div>
        <div class="col-md-4">
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #1</span>
                    <small class="pull-right">20%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">20% Complete</span>
                    </div>
                </div>
            </div>
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #2</span>
                    <small class="pull-right">90%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 90%" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">90% Complete</span>
                    </div>
                </div>
            </div>
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #3</span>
                    <small class="pull-right">60%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 60%" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">60% Complete</span>
                    </div>
                </div>
            </div>
        </div>

        @if($teamMember )

        <div class="col-md-1 actions">

        </div>
        @else

        <div class="col-md-1 actions">
            <?php
            if ($reimbursement_form->verified_level == 0) {
                ?> 
                @la_access("Reimbursement_Forms", "edit")
                <a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_forms/'.$reimbursement_form->id.'/edit') }}" class="btn btn-xs btn-edit btn-default"><i class="fa fa-pencil"></i></a><br>
                @endla_access

                @la_access("Reimbursement_Forms", "delete")
                {{ Form::open(['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.destroy', $reimbursement_form->id], 'method' => 'delete', 'style'=>'display:inline']) }}
                <button class="btn btn-default btn-delete btn-xs" type="submit"><i class="fa fa-times"></i></button>
                {{ Form::close() }}
                @endla_access
                <?php
            }
            ?>

        </div>
        @endif
    </div>
    <ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
        <li class=""><a href="{{ url(config('laraadmin.adminRoute') . '/reimbursement_forms') }}" data-toggle="tooltip" data-placement="right" title="Back to Reimbursement Forms"><i class="fa fa-chevron-left"></i></a></li>
        <li class="active"><a role="tab" data-toggle="tab" class="active" href="#tab-general-info" data-target="#tab-info"><i class="fa fa-bars"></i> General Info</a></li>
        <li class=""><a role="tab" data-toggle="tab" href="#tab-timeline" data-target="#tab-timeline"><i class="fa fa-clock-o"></i> Timeline</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active fade in" id="tab-info">
            <div class="tab-content">
                <div class="panel infolist">
                    <div class="panel-default panel-heading">
                        <h4>General Info</h4>
                    </div>
                    <div class="panel-body">
                        @la_display($module, 'emp_id')
                        @la_display($module, 'type_id')
                        @la_display($module, 'amount')
                        @la_display($module, 'user_comment')
                        @la_display($module, 'document_attached')
                        <!--@la_display($module, 'verified_level')-->
                        @la_display($module, 'paid_status ')
                        @la_display($module, 'verfication_status ')
                        @la_display($module, 'hard_copy_accepted')
                        @la_display($module, 'payment_date ')
                        @la_display($module, 'cosharing_count')
                        @la_display($module, 'created_by')
                        @la_display($module, 'update_by')
                        @la_display($module, 'deleted_by')
                        <!--                        <div class="form-group col-md-12">-->
                        <div class="form-group col-md-2">
                            <label for="cosharing" class="control-label">Co-Sharing Names:</label>
                        </div>
                        <div class="col-md-10">
                            <select name="cosharing[]" id="cosharing" class="js-example-basic-multiple" multiple="multiple" disabled="disable"  > 
                                <?php
                                if (!empty($reimbursement_form)) {
                                    foreach ($employeename as $value) {
                                        echo '<option value="' . $value->id . '" ' . (in_array($value->id, $reimbursement_form->cosharing) ? 'selected' : '') . "> " . $value->name . '</option>';
                                    }
                                }
                                ?>
                            </select> 
                        </div>
                        <!--                        </div>-->
                        <div class="col-md-12">

                            <label for="date"class="col-md-2">Date:</label>

                            <div class="col-md-10">
                                <?php echo $reimbursement_form->Date; ?>
                            </div>
                        </div>
                        <div class="form-group col-md-12" text="right">
                            @if($teamMember)
                            <?php
                            if ($role == 'lead') {

                                if ($reimbursement_form->verified_level == 0) {
                                    ?>

                                    <div class="col-md-12 text-right" >
                                        <button class="btn btn-success"  data-id ="{{ $reimbursement_form->$view_col }}" onclick="myfunction(this);" id="Approved"  type="submit">Approve</button>
                                        <button class="btn btn"  data-id ="{{ $reimbursement_form->$view_col }}" onclick="myfunction(this);"  id="Rejected" style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;">Reject</button>
                                    </div>

                                    <?php
                                } else {
                                    ?>

                                    <div class="col-md-12 text-right" >
                                        <h5> Action taken</h5>
                                    </div>

                                    <?php
                                }
                            } else if ($role == 'manager') {
//                                echo "<pre>hello"; print_r($reimbursement_level);die;
                                if (isset($reimbursement_level) && $reimbursement_level->status == 0 && $reimbursement_level->approved_by == Auth::user()->context_id && $reimbursement_level->level == 2) {
                                    ?>

                                    <div class="col-md-12 text-right buttons-div" >
                                        <button class="btn btn-success"  data-id ="{{ $reimbursement_form->id }}" onclick="mybutton(this);" id="Approved" data-value="1"  type="submit">Approve</button>
                                        <button class="btn btn"  data-id ="{{ $reimbursement_form->id }}" data-value="2"  onclick="mybutton(this);"  id="Rejected" style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;">Reject</button>
                                    </div>

                                    <?php
                                } else if (isset($reimbursement_level) && $reimbursement_level->status != '0' && $reimbursement_level->approved_by == Auth::user()->context_id && $reimbursement_level->level == 2) {
                                    ?> 
                                    <div class="col-md-12 text-right" >
                                        <h5> Action taken</h5>
                                    </div>


                                    <?php
                                } else  {
                                    ?> 
                                    <div class="col-md-12 text-right" >
                                        <h5> Approval pending level one</h5>
                                    </div>


                                    <?php
                                }
                            }
                            ?>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade in p20 bg-white" id="tab-timeline">
            <ul class="timeline timeline-inverse">
                <!-- timeline time label -->
                <li class="time-label">
                    <span class="bg-red">
                        10 Feb. 2014
                    </span>
                </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
                <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                        <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                        <div class="timeline-body">
                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                            weebly ning heekya handango imeem plugg dopplr jibjab, movity
                            jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                            quora plaxo ideeli hulu weebly balihoo...
                        </div>
                        <div class="timeline-footer">
                            <a class="btn btn-primary btn-xs">Read more</a>
                            <a class="btn btn-danger btn-xs">Delete</a>
                        </div>
                    </div>
                </li>
                <!-- END timeline item -->
                <!-- timeline item -->
                <li>
                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                        <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request
                        </h3>
                    </div>
                </li>
                <!-- END timeline item -->
                <!-- timeline item -->
                <li>
                    <i class="fa fa-comments bg-yellow"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                        <div class="timeline-body">
                            Take me to your leader!
                            Switzerland is small and neutral!
                            We are more like Germany, ambitious and misunderstood!
                        </div>
                        <div class="timeline-footer">
                            <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                        </div>
                    </div>
                </li>
                <!-- END timeline item -->
                <!-- timeline time label -->
                <li class="time-label">
                    <span class="bg-green">
                        3 Jan. 2014
                    </span>
                </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
                <li>
                    <i class="fa fa-camera bg-purple"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                        <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                        <div class="timeline-body">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                        </div>
                    </div>
                </li>
                <!-- END timeline item -->
                <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                </li>
            </ul>
            <!--<div class="text-center p30"><i class="fa fa-list-alt" style="font-size: 100px;"></i> <br> No posts to show</div>-->
        </div>

    </div>
</div>
</div>
</div>
@endsection
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script>

                                            $(document).ready(function () {
                                                $('.js-example-basic-multiple').select2();
                                            });


                                            function myfunction(button)
                                            {
                                                var controlid = $(button).attr('id');

                                                var approved = 0;
                                                if (controlid == 'Approved')
                                                {
                                                    var approved = 1;
                                                }

                                                $.ajax({
                                                    url: "{{ url('/approvereimbursement') }}",
                                                    type: 'GET',
                                                    data: {
                                                        'approved': approved, 'id': $(button).attr('data-id')
                                                    },

                                                    success: function (data) {
                                                        var vid = $(button).attr('data-id');
                                                        $(button).parents('td').siblings('td').children(".status").parents('td')
                                                                .html((approved) ? '<span class="text-success status">Approved</span>' : '<span class="text-danger status">Rejected</span>');
                                                        $(button).parents('td').html('Action Taken');
                                                        $('[data-id=' + vid + ']').remove();
                                                        swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
                                                        $('div.overlay').addClass('hide');

                                                    }
                                                });
                                            }

                                            function mybutton(button)
                                            {
                                                var approved = $(button).attr('data-value');
                                                $.ajax({
                                                    url: "{{ url('/approvedreimbursement') }}",
                                                    type: 'GET',
                                                    data: {
                                                        'approved': approved, 'form_id': $(button).attr('data-id')
                                                    },

                                                    success: function () {
                                                        $(button).parents('div.buttons-div').html('Action Taken');
                                                        swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
                                                        $('div.overlay').addClass('hide');
                                                    }
                                                });


                                            }
</script>

