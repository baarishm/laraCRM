@extends('la.layouts.app')

@section('htmlheader_title')
Reimbursement Form View
@endsection

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
<style>
    .profile2 .panel.infolist .form-group {
          border-bottom: none;
    }
    td, th {
          padding: 0px 10px 5px;
    }

    .time-footer{
          position: relative;
          width: 100%;
          margin-left: 0px;
    }
</style>


<div>
    <div class="col-md-6" style="padding:5px 15px;">
        <!--            <h4 class="name">Form Id: {{ $reimbursement_form -> $view_col}}</h4>-->
        <h4><strong>Reimbursement Details</strong></h4>
    </div>

    @if($teamMember )
    <div class="col-md-6"></div>
    @else

    <div class="col-md-6 text-right" style="padding:10px;">
          <?php
          if ($reimbursement_form->verified_level == 0) {
                ?> 
              @la_access("Reimbursement_Forms", "edit")
              <a href="{{ url(config('laraadmin.adminRoute').'/reimbursement_forms/'.$reimbursement_form -> id.'/edit')}}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
              @endla_access

              @la_access("Reimbursement_Forms", "delete")
              {{ Form::open(['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.destroy', $reimbursement_form->id], 'method' => 'delete', 'style'=>'display:inline']) }}
              <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>
              {{ Form::close() }}
              @endla_access
              <?php
        }
        ?>

    </div>
    @endif
</div>
<div class= "clearfix"></div>
<ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
    <li class=""><a href="{{ url(config('laraadmin.adminRoute').'/reimbursement_forms')}}" data-toggle="tooltip" data-placement="bottom" title="Back to Reimbursement Forms" style="padding: 18px;"><i class="fa fa-chevron-left"></i></a></li>
    <li class="active"><a role="tab" data-toggle="tab" class="active" href="#tab-general-info" data-target="#tab-info"><i class="fa fa-bars"></i> General Info</a></li>
    <li class=""><a role="tab" data-toggle="tab" href="#tab-timeline" data-target="#tab-timeline"><i class="fa fa-clock-o"></i> Timeline</a></li>
</ul>


<div class="tab-content">
    <div role="tabpanel" class="tab-pane active fade in" id="tab-info">
        <div class="tab-content">
            <div class="panel infolist">

                <div class="panel-body info-lables">

                    <div class="panel-default panel-heading">
                        <h4>General Info</h4>
                    </div>

                    <table style="width:100%;">
                        <tr>
                            <td>
                                <label for="amount" class="control-label">Applicant Name:</label> 
                                <?php
                                if (!empty($reimbursement_form)) {
                                      foreach ($employeename as $value) {
                                            if ($reimbursement_form->emp_id == $value->id)
                                                  echo $value->name;
                                      }
                                }
                                ?>
                            </td>
                            <td>
                                <label for="amount" class="control-label">Amount (INR):</label> 
                                {{$reimbursement_form -> amount or old('amount')}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Reimbursement Type: </label>
                                <?php
                                if (!empty($reimbursement_form)) {
                                      foreach ($reimb_types as $value) {
                                            if ($reimbursement_form->type_id == $value->id)
                                                  echo $value->name;
                                      }
                                }
                                ?>
                            </td>
                            <td> <label for="date" >Expenses Date:</label>{{$reimbursement_form -> Date}}</td>
                        </tr>

                        <tr>
                            <td >
                                <label for="cosharing" class="control-label">Co-Sharing Names:</label>
                                <?php if (!empty($reimbursement_form->cosharing)) { ?>
                                      <select name="cosharing[]" id="cosharing" class="js-example-basic-multiple" multiple="multiple" disabled="disable"  > 
                                            <?php
                                            foreach ($employeename as $value) {
                                                  echo '<option value="' . $value->id . '" ' . (in_array($value->id, $reimbursement_form->cosharing) ? 'selected' : '') . "> " . $value->name . '</option>';
                                            }
                                            ?>
                                      </select>
                                      <?php
                                } else {
                                      echo "None";
                                }
                                ?>
                            </td>
                            <td ><label for="Cosharing_count" class="control-label">Employee Count:</label> {{count($reimbursement_form -> cosharing) + 1}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label  for="number">User Comment:</label> 
                                {{$reimbursement_form -> user_comment or old('user_comment')}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label  for="number"> Image:</label>
                                <?php
                                if (!empty($images)) {
                                      foreach ($images as $image) {
                                            ?>
                                            <a href="<?php echo asset('uploads') . '\\' . $image->name ?>" target="_blank" type="btn" >
                                                  <?php
                                                  echo $image->name;
                                                  ?>
                                            </a>
                                            <?php
                                      }
                                } else {
                                      echo "No Document Attached";
                                }
                                ?> 
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="date">Approval Date:</label> 
                                <?php if ((!empty($updateby) && $updateby[0]->updated_at != Null)) {
                                      ?>
                                      <?php $Date = date('d M Y', strtotime($updateby[0]->updated_at)); ?>
                                      {{$Date}}
                                      <?php
                                } else {
                                      ?><h6>---</h6>
                                      <?php
                                }
                                ?>
                            </td>
                            <td>
                                <label for="date">Action taken by: </label>
                                <?php if ((!empty($updateby) && $updateby[0]->action_taken_by != Null)) {
                                      ?>
                                      <?php
                                      if (!empty($updateby[0]->action_taken_by)) {
                                            foreach ($employeename as $value) {
                                                  if ($updateby[0]->action_taken_by == $value->id)
                                                        echo $value->name;
                                            }
                                      }
                                      ?>
                                      <?php
                                }else {
                                      ?><h6>None</h6>
                                      <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </table>

                    <hr/>
                    <div class="clearfix"></div>
                    <div class="panel-default panel-heading">
                        <h4>Payment Info</h4>
                    </div>

                    <div class="clearfix"></div>


                    <?php if ($teamMember == 0 && $account == 1) { ?>
                          <div class="clearfix"></div>

                          <table>
                              <tr>
                                  <td>
                                      <label>Payment Date:</label>
                                      <?php if($reimbursement_form -> payment_date =='0000-00-00' || $reimbursement_form -> payment_date ==''){
                                          
                               ?>
                                    
                                       <input type="text"  class="form-control" id="payment_date" ng-model="payment_date" name="payment_date"
                                             autocomplete="off" placeholder="Payment Date" />
                                       
                                       <?php
                                      }
                                      else{
                                          ?>
                                        <input type="text"  class="form-control" id="payment_date" ng-model="payment_date" name="payment_date" value="<?php echo  date('d M Y', strtotime($reimbursement_form -> payment_date)); ?>"
                                             autocomplete="off" placeholder="Payment Date" />
                                       <?php
                                      }
                                      ?>
                  
                                  </td>
                                  <td>
                                      <label>Payment Mode:</label>
                                      <select name="payment_mode" class="form-control" id="mode" value="{{ ($reimbursement_form->payment_mode or old('payment_mode'))}}" required="=required">
                                          <option value="Cash">Cash</option>
                                          <option value="Cheque">Cheque</option>
                                          <option value="onlinetransfer">Online Transfer</option>
                                      </select>
                                  </td>
                                  <td>
                                      <label>Payment Amount:</label>
                                      <input type="text" value="{{$reimbursement_form -> paid_amount or old('paid_amount')}}" class="form-control" 
                                             id="amount"  name="paid_amount" autocomplete="off"  placeholder="Payment Amount" required="required" />
                                  </td>
                              </tr>

                          </table>
                          <div class="clearfix"></div>
                          <?php
                    } else if ($teamMember == 0 && $account == 0 || $teamMember == 1 && $account == 0) {
                          ?>


                          <table>
                              <tr>
                                  <td>
                                      <label>Payment Date:</label>

                                      <?php if ($reimbursement_form->payment_date == Null || $reimbursement_form->payment_date == 0000-00-00) {
                                            ?>
                                            <h6>--</h6>
                                            <?php
                                      } else {
                                            ?>
                                            <?php $PayDate = date('d M Y', strtotime($reimbursement_form->payment_date)); ?>
                                            {{$PayDate}}
                                            <?php
                                      }
                                      ?>
                                  </td>
                                  <td>
                                      <label>Payment Mode:</label>
                                      <?php
                                      if (!empty($reimbursement_form->payment_mode)) {
                                            echo $reimbursement_form->payment_mode;
                                      } else {
                                            ?>
                                            <h6>None</h6>
                                            <?php
                                      }
                                      ?>
                                  </td>
                                  <td>
                                      <label>Payment Amount:</label> 
                                      {{$reimbursement_form -> paid_amount}}
                                  </td>
                              </tr>

                          </table>

                          <?php
                    }
                    ?>



                    <div class="row status-bottom" text="right">
                        @if($teamMember || $account)
                        <?php
                        if ((!empty($reimbursement_status) && $reimbursement_status[0]->status == 2)) {
                              ?>
                              <div class="col-md-12 text-right" >
                                  <h5><span class="rejected">Rejected at level <?php echo $reimbursement_level->verified_level ?></span></h5>
                              </div>
                              <?php
                        } else {

                              if ($reimbursement_level->verified_level + 1 == $reimbursement_level->level) {
                                    ?>

                                    <div class="col-md-12 text-right" id="button">
                                        <button class="btn btn-success"  data-id ="{{ $reimbursement_form -> $view_col}}" onclick="myfunction(this);" id="Approved"  type="submit" data-value="1" >Approve</button>
                                        <button class="btn btn" data-value="2"  data-id ="{{ $reimbursement_form -> $view_col}}" onclick="myfunction(this);"  id="Rejected" style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;" >Reject</button>
                                    </div>

                                    <?php
                              } else if ($reimbursement_level->verified_level + 1 > $reimbursement_level->level) {
                                    ?>

                                    <div class="col-md-12 text-right" >
                                        <h5><span class="action-taken">Action already taken</span></h5>
                                    </div>

                                    <?php
                              } else if ($reimbursement_level->verified_level + 1 < $reimbursement_level->level) {
                                    ?>

                                    <div class="col-md-12 text-right" >
                                        <h5><span class="action-pending"><i class="fa fa-exclamation-circle"></i> Action pending at level <?php echo $reimbursement_level->verified_level + 1 ?></span></h5> 
                                    </div>

                                    <?php
                              }
                        }
                        ?>

                        @endif
                        @if(!$teamMember && !$account)

                        <?php
                        if ($reimbursement_form->verified_level == 0) {
                              ?>

                              <div class="col-md-12 text-right" >
                                  <h5> <span class="action-pending">  <i class="fa fa-exclamation-circle"></i>  Pending at level one </span> </h5> 
                              </div>
                              <?php
                        } else if ((!empty($reimbursement_status) && $reimbursement_status[0]->status == 2) && $reimbursement_form->verified_level !== 0) {
                              ?>

                              <div class="col-md-12 text-right" >
                                  <h5><span class="rejected">Reject at level <?php echo $reimbursement_form->verified_level ?></span></h5>
                              </div>
                              <?php
                        } else if ($reimbursement_form->verified_level !== 0 && $reimbursement_form->verified_level < $reimbursement_form->verified_approval) {
                              ?>

                              <div class="col-md-12 text-right" >
                                  <h5><span class="action-pending"><i class="fa fa-exclamation-circle"></i> Action pending at level  <?php echo $reimbursement_form->verified_level + 1 ?></span></h5>
                              </div>
                              <?php
                        } else if ($reimbursement_form->verified_level != 0 && $reimbursement_form->verified_level == $reimbursement_form->verified_approval) {
                              ?>

                              <div class="col-md-12 text-right" >
                                  <h5><span class="app-close">Application close </span></h5>
                              </div>
                              <?php
                        }
                        ?>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane fade in p20 bg-white info-lables" id="tab-timeline">
        <div class="panel-default panel-heading">
            <h4>Timeline</h4>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <ul class="timeline timeline-inverse">
                    <!-- timeline time label -->
                    <li class="time-label">
                        <span>
                            <h6>Applied date</h6>
                            <?php $ApplyDate = date('d M Y', strtotime($reimbursement_form['created_at'])); ?>
                            {{$ApplyDate}}

                        </span>
                    </li>
                    <!-- /.timeline-label -->
                    <!-- timeline item -->
                    <li>
                          <?php
                          if ($reimbursement_form->verified_level == 0) {
                                ?>
                              <i class="fa fa-clock-o fa-2 bg-grey"></i>

                              <div class="timeline-item">
                                  <span class="time"> </span>

                                  <h5>  Action pending </h5>

                                  <div class="timeline-footer">

                                  </div>
                              </div>

                              <?php
                        } else if ($reimbursement_form->verified_level > 0) {
                              ?>
                              <i class="fa fa-check fa-2 bg-blue"></i>

                              <div class="timeline-item">
                                  <span class="time"></span>

                                  <h5>In process</h5>


                                  <div class="timeline-footer">

                                  </div>
                              </div>
                              <?php
                        }
                        ?>


                        <?php if ($reimbursement_form['verified_approval'] == 3) { ?>  
                              <?php if (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1) { ?>
                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level One</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[0]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="approved"> Approved </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[0]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>

                                <?php
                          } else if (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 2 && $join_approve_form[0]->level == 1) {
                                ?>

                                <li>

                                    <i class="fa fa-times-circle bg-red"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level One</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[0]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="rejected"> Rejected </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[0]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>
                                <?php
                          } else {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                    <div class="timeline-item">
                                        <h5> <span class="text-left">Level One</span> </h5>
                                        <span class="time"> 

                                            <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                  ?>
                                                  <?php
                                                  if (!empty($join_approve_form[0]->action_taken_by)) {
                                                        foreach ($employeename as $value) {
                                                              if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                    echo $value->name;
                                                        }
                                                  }
                                                  ?>
                                                  <?php
                                            }else {
                                                  ?><h6>None</h6>
                                                  <?php
                                            }
                                            ?>

                                        </span>


                                        <div class="timeline-body">
                                            Action Pending
                                        </div>
                                    </div>
                                </li>
                                <?php
                          }
                          ?>

                          <?php if ((!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 1 && $join_approve_form[1]->level == 2)) { ?>
                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Two</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[1]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="approved"> Approved </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>

                                <?php
                          } else if ((!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 2 && $join_approve_form[1]->level == 2)) {
                                ?>

                                <li>
                                    <i class="fa fa-times-circle bg-red"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Two</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[1]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="rejected"> Rejected </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>
                                <?php
                          } else {
                                if ((!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 0 && $join_approve_form[1]->level == 2) && (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && ($join_approve_form[0]->status == 1 || $join_approve_form[0]->status == 0 ) && $join_approve_form[0]->level == 1)) {
                                      ?>
                                      <li>
                                          <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                          <div class="timeline-item">
                                              <h5> <span class="text-left">Level Two</span> </h5>
                                              <span class="time"> 

                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                        <?php
                                                  }
                                                  ?>

                                              </span>


                                              <div class="timeline-body">
                                                  Action Pending
                                              </div>
                                          </div>
                                      </li>
                                      <?php
                                } else {
                                      ?>
                                      <?php
                                }
                          }
                          ?>
                          <?php if ((!empty($join_approve_form[2]) && $join_approve_form[2]->action_taken_by != Null && $join_approve_form[2]->status == 1 && $join_approve_form[2]->level == 3)) { ?>
                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Three</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[2]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[2]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="approved"> Approved </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[2]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[2]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[2]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>

                                <?php
                          } else if ((!empty($join_approve_form[2]) && $join_approve_form[2]->action_taken_by != Null && $join_approve_form[2]->status == 2 && $join_approve_form[2]->level == 3)) {
                                ?>

                                <li>
                                    <i class="fa fa-times-circle bg-red"></i>


                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Three</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[2]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[2]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="rejected"> Rejected </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[2]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[2]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>
                                <?php
                          } else {
                                if ((!empty($join_approve_form[2]) && $join_approve_form[2]->action_taken_by != Null && $join_approve_form[2]->status == 0 && $join_approve_form[2]->level == 3) && (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && ($join_approve_form[0]->status == 1 || $join_approve_form[0]->status == 0 ) && $join_approve_form[0]->level == 1) && (!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && ($join_approve_form[1]->status == 1 || $join_approve_form[1]->status == 0 ) && $join_approve_form[1]->level == 2)) {
                                      ?>
                                      <li>
                                          <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                          <div class="timeline-item">
                                              <h5> <span class="text-left">Level Three</span> </h5>
                                              <span class="time">

                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[2]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[2]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[2]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                        <?php
                                                  }
                                                  ?>

                                              </span>


                                              <div class="timeline-body">
                                                  Action Pending
                                              </div>
                                          </div>
                                      </li>
                                      <?php
                                } 
                              
                          }

                          if ($reimbursement_form->verified_level == $reimbursement_form->verified_approval && (!empty($join_approve_form[2]) && $join_approve_form[2]->status == 2 && $join_approve_form[2]->level == 3) || (!empty($join_approve_form) && $join_approve_form[1]->status == 2 && $join_approve_form[1]->level == 2) || (!empty($join_approve_form) && $join_approve_form[0]->status == 2 && $join_approve_form[0]->level == 1)) {
                                ?>

                                <li>
                                    <i class="fa fa-check fa-2 bg-red"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application close
                                        </h3>

                                    </div>
                                </li>
                                <?php
                          } else if ($reimbursement_form->verified_level == $reimbursement_form->verified_approval || (!empty($join_approve_form[2]) && $join_approve_form[2]->status == 1 && $join_approve_form[2]->level == 3) && (!empty($join_approve_form) && $join_approve_form[1]->status == 1 && $join_approve_form[1]->level == 2) && (!empty($join_approve_form) && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1)) {
                                ?>

                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application close
                                        </h3>

                                    </div>
                                </li>
                                <?php
                          } else if (!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1) {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-blue"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application in progress
                                        </h3>
                                    </div>
                                </li>
                                <?php
                          } else {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>


                                        <div class="timeline-body">
                                            Action Pending
                                        </div>
                                    </div>
                                </li>

                                <?php
                          }
                    } else {
                          if (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1) {
                                ?>
                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level One</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[0]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="approved"> Approved </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[0]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>

                                <?php
                          } else if (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 2 && $join_approve_form[0]->level == 1) {
                                ?>

                                <li>
                                    <i class="fa fa-times-circle bg-red"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level One</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[0]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="rejected"> Rejected </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[0]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>
                                <?php
                          } else {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                    <div class="timeline-item">
                                        <h5> <span class="text-left">Level One</span> </h5>
                                        <span class="time">

                                            <?php if ((!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null)) {
                                                  ?>
                                                  <?php
                                                  if (!empty($join_approve_form[0]->action_taken_by)) {
                                                        foreach ($employeename as $value) {
                                                              if ($join_approve_form[0]->action_taken_by == $value->id)
                                                                    echo $value->name;
                                                        }
                                                  }
                                                  ?>
                                                  <?php
                                            }else {
                                                  ?><h6>None</h6>
                                                  <?php
                                            }
                                            ?>

                                        </span>
                                        <div class="timeline-body">
                                            Action Pending
                                        </div>
                                    </div>
                                </li>
                                <?php
                          }
                          ?>

                          <?php if ((!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 1 && $join_approve_form[1]->level == 2)) { ?>
                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Two</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[1]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="approved"> Approved </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>
                                </li>

                                <?php
                          } else if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 2 && $join_approve_form[1]->level == 2)) {
                                ?>

                                <li>
                                    <i class="fa fa-times-circle bg-red"></i>

                                    <div class="timeline-item">
                                        <h5>
                                            <span class="text-left">Level Two</span> 
                                            <span class="time float-right" >
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->updated_at != Null)) {
                                                        ?>
                                                        <?php $ApplyDate = date('d M Y', strtotime($join_approve_form[1]->updated_at)); ?>
                                                      <i class="fa fa-clock-o"></i> {{$ApplyDate}}
                                                      <?php
                                                } else {
                                                      ?>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>
                                        <h5>
                                            <span class="rejected"> Rejected </span>
                                            <span class="float-right"> 
                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                      <?php
                                                }
                                                ?>
                                            </span>
                                        </h5>

                                    </div>

                                </li>
                                <?php
                          } else {
                                if ((!empty($join_approve_form[1]) && $join_approve_form[1]->action_taken_by != Null && $join_approve_form[1]->status == 0 && $join_approve_form[1]->level == 2) && (!empty($join_approve_form[0]) && $join_approve_form[0]->action_taken_by != Null && ($join_approve_form[0]->status == 1 || $join_approve_form[0]->status == 0 ) && $join_approve_form[0]->level == 1)) {
                                      ?>
                                      <li>
                                          <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                          <div class="timeline-item">
                                              <h5> <span class="text-left">Level Two</span> </h5>
                                              <span class="time">

                                                  <?php if ((!empty($join_approve_form) && $join_approve_form[1]->action_taken_by != Null)) {
                                                        ?>
                                                        <?php
                                                        if (!empty($join_approve_form[1]->action_taken_by)) {
                                                              foreach ($employeename as $value) {
                                                                    if ($join_approve_form[1]->action_taken_by == $value->id)
                                                                          echo $value->name;
                                                              }
                                                        }
                                                        ?>
                                                        <?php
                                                  }else {
                                                        ?><h6>None</h6>
                                                        <?php
                                                  }
                                                  ?>

                                              </span>


                                              <div class="timeline-body">
                                                  Action Pending
                                              </div>
                                          </div>
                                      </li>
                                      <?php
                                } else {
                                      ?>
                                      <?php
                                }
                          }
                          ?>

                          <?php
                          if ($reimbursement_form->verified_level == $reimbursement_form->verified_approval && (!empty($join_approve_form) && $join_approve_form[1]->status == 1 && $join_approve_form[1]->level == 2 && (!empty($join_approve_form) && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1))) {
                                ?>

                                <li>
                                    <i class="fa fa-check fa-2 bg-green"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application close
                                        </h3>

                                    </div>
                                </li>
                                <?php
                          } else if ($reimbursement_form->verified_level == $reimbursement_form->verified_approval || (!empty($join_approve_form) && $join_approve_form[1]->status == 2 && $join_approve_form[1]->level == 2 || (!empty($join_approve_form) && $join_approve_form[0]->status == 2 && $join_approve_form[0]->level == 1))) {
                                ?>

                                <li>
                                    <i class="fa fa-check fa-2 bg-red"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application close
                                        </h3>

                                    </div>
                                </li>
                                <?php
                          } else if (!empty($join_approve_form) && $join_approve_form[0]->action_taken_by != Null && $join_approve_form[0]->status == 1 && $join_approve_form[0]->level == 1) {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-blue"></i>

                                    <div class="timeline-item">
                                        <span class="time"></span>

                                        <h3 class="timeline-header no-border">Application in progress
                                        </h3>
                                    </div>
                                </li>
                                <?php
                          } else {
                                ?>
                                <li>
                                    <i class="fa fa-clock-o fa-2 bg-grey"></i>

                                    <div class="timeline-item">
                                        <span class="time"> </span>


                                        <div class="timeline-body">
                                            Action Pending
                                        </div>
                                    </div>
                                </li>

                                <?php
                          }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>


</div>
<footer class="main-footer time-footer">
    <div class="pull-right hidden-xs" data-created-by = "Varsha Mittal">
        Powered by <a href="#">Ganit Softech</a>
    </div>
    <strong>Copyright &copy; 2018
</footer>
@endsection


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script>
                                          $(document).ready(function () {
                                                $('.js-example-basic-multiple').select2();

                                                $('#payment_date').datepicker({
                                                      todayHighlight: 'true',
                                                      format: 'd M yyyy',
                                                      daysOfWeekDisabled: [0, 6],
                                                      minDate: 0


                                                })
                                          });

                                          function myfunction(button)
                                          {
                                                var approved = $(button).attr('data-value');
                                                var date_id = $('#payment_date').val();
                                                var mode_id = $('#mode').val();
                                                var amount_id = $('#amount').val();
                                                var date = ('00-00-0000');
                                                var mode = ('');
                                                var amount = parseFloat(0);
                                                if(approved == 1){
                                                if (($("#payment_date").length > 0 && $("#payment_date").val() == '') || ($("#amount").length > 0 && $("#amount").val() == '')) {
                                                      swal("Please fill all fields.");
                                                      return false;
                                                }
                                            }
                                                swal({
                                                      title: "Enter Comment",
                                                      input: "textarea",
                                                      showCancelButton: true,
                                                      closeOnConfirm: false,
                                                      inputPlaceholder: "Comment on approval and rejection "
                                                }).then(function (inputValue) {
                                                      if (inputValue.dismiss === 'cancel') {
                                                            return false;
                                                      } else {
                                                            $('div.overlay').removeClass('hide');
                                                            $.ajax({
                                                                  url: "{{ url('/approvereimbursement') }}",
                                                                  type: 'GET',
                                                                  data: {
                                                                        'approved': approved,
                                                                        'id': $(button).attr('data-id'),
                                                                        'actionReason': inputValue.value,
                                                                        'datepicker': ((typeof date_id === "undefined") ? date : date_id),
                                                                        'mode': ((typeof mode_id === "undefined") ? mode : mode_id),
                                                                        'amount': ((typeof amount_id === "undefined") ? amount : amount_id)
                                                                  },

                                                                  success: function (data) {
                                                                        $(button).parents('div.buttons-div').html('Action Taken');
                                                                        swal('Application has been successfully ' + ((approved == 1) ? 'Approved' : 'Rejected') + '!').then(function () {
                                                                              location.reload();
                                                                        });

                                                                        $('div.overlay').addClass('hide');
                                                                  }
                                                            });
                                                      }
                                                });

                                          }




</script>

