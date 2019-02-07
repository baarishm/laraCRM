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
Apply For Reimbursement
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
            <div class="col-md-8 col-md-offset-2">
                <form method="POST" action="{{url(config('laraadmin.adminRoute').'/reimbursement_forms/store')}}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <input type="hidden" id="verified_approval" name="verified_approval" value="1">
                     <input type="hidden" id="hard_copy_accepted" name="hard_copy_accepted" value="1">

                    <div class="row">
                         <div class="form-group col-md-4">
                            <label>Manager Name</label>
                            <input type="text" class="form-control" value="{{$manager}}" disabled/>
                        </div>
                        <div class="form-group col-md-3 hide">
                            <label for="Name">Employee Id:</label>
                            <input type="text" class="form-control" name="emp_id" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="emp_id" placeholder="EmpId" required readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="Date" class="control-label">Date*</label>
                            <input class="form-control" id="datepicker" name="date"  type="text" readonly="true"/>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Reimbursement Type</label>
                            <select name="type_id" id="type_id" class="form-control" >
                                <?php
                                if (!empty($reimbursement_types)) {
                                    foreach ($reimbursement_types as $value) {
                                        echo '<option data-doc-req="' . $value->document_required . '" value="' . $value->id . '" data-limit="'.$value->limit.'" data-limit_variance="'.$value->limit_variance.'" data-hard-copy="'.$value->hard_copy_accepted.'" data-verification-level="'.$value->verification_level.'">' . $value->name . ' </option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Cosharing" class="control-label">Co-Sharing Names</label>
                            <select name="cosharing[]" id="cosharing" class="js-example-basic-multiple" multiple="multiple"   >
                                <?php
                                if (!empty($employeename)) {
                                    foreach ($employeename as $value) {
                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                    }
                                }
                                ?> 
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="Cosharing_count"  class="control-label">Total</label>
                            <input type="text" value="{{ old('Cosharing_count')}}" class="form-control" 
                                   id="cosharingcount"  name="Cosharing_count" autocomplete="off"  min="1" max="10"  />
                        </div>
                         <div class="form-group col-md-4">
                            <label for="Amount" class="control-label">Amount (INR)*</label>
                            <input type="text" value="{{ old('amount')}}" class="form-control" 
                                   id="amount"  name="amount" autocomplete="off"  placeholder="Amount" />
                        </div>

                       <div class="form-group col-md-12">
                            <label for="Number">User Comment*</label>
                            <input type="text" value="{{ old('user_comment')}}" class="form-control" name="user_comment" autocomplete="off"   maxlength="180" id="user_comment" >   
                        </div>


                        <div class="form-group col-md-3" >

                            <label for="Number">Attach Document</label>

                            <table>
                                <tr>
                                    <td id="doc-att-yes">
                                        <label style="margin-right: 20px;"><input type="radio"  name="document_attached" value="1" class="check" checked required  > Yes</label>
                                    </td>
                                    <td id="doc-att-no">
                                        <label><input type="radio"  name="document_attached" value="0" checked class="uncheck"  > No</label>
                                    </td>
                                </tr>
                            </table>

                        </div>

                        <div class="form-group col-md-9 imageupload" style="display: none" id="document_attached1">

                            <label for="Number"> Select image :</label>
                            <input  type="file" id="name" class="form-control" name="name[]" placeholder="address" multiple onchange="validateImage()" value="{{ old('name')}}" >

                        </div>

                        <div class="col-md-12 text-right" >
                            <button type="submit" onclick="CheckApproval()" class="btn btn-success">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
                                   var limit=0;
                                   var limit_variance=0;
                            //        var level=0;
                                
                                $(".js-example-basic-multiple-limit").select2(
                                        {
                                            maximumSelectionLength: 25

                                        });
                                $(document).ready(function () {
                                    var emp_detail = "{{ Session::get('employee_details') }}";
                                    emp_detail = JSON.parse(emp_detail.replace(/&quot;/g, '\"'));
                                    //get dates from session
                                    var dates = "{{ Session::get('holiday_list') }}";
                                    dates = JSON.parse(dates.replace(/&quot;/g, '\"'));

                                    $('select').select2();

                                    //Show/hide comp off list
//                                    $('#type_id').on('change', function () {
//                                        ReimbursementType(this);
//                                    });



                                    $("input[name$='document_attached']").click(function () {
                                        var test = $(this).val();                                                                        
                                        $('.uncheck').click(function () {
                                             $("div.imageupload").hide();
                                            ($("#name").val(''));
                                        });
                                        if (test==1) {
                                      $("#document_attached1").show();
                                            $("#name").attr('required', true).show();
                                          
                                        }
                                        else{
                                             $("div.imageupload").hide();
                                              $("#name").attr('required',false).hide();
                                        }
                                    });
                                    $("#amount, #cosharingcount").keypress(function (e) {


                                        if (this.value.length == 0 && e.which == 48) {
                                            return false;
                                        }

                                        if (e.which == 46) {
                                            if ($(this).val().indexOf('.') != -1) {
                                                return false;
                                            }
                                        }

                                        if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                                            return false;
                                        }
                                    });




                                    var t = false

                                    $('#cosharingcount').focus(function () {
                                        var $this = $(this)

                                        t = setInterval(
                                                function () {
                                                    if (($this.val() < 1 || $this.val() > 25) && $this.val().length != 0) {
                                                        if ($this.val() < 1) {
                                                            $this.val(1)
                                                        }

                                                        if ($this.val() > 25) {
                                                            $this.val(25)
                                                        }

                                                    }
                                                }, 50)
                                    })

                                    $('#cosharingcount').blur(function () {
                                        if (t != false) {
                                            window.clearInterval(t)
                                            t = false;
                                        }
                                    })
                                    $("#cosharing").change(function () {
                                        var count = ($(".select2-selection__rendered").children().length - 1);
                                        $("#cosharingcount").val(count);

                                    });

                                    $("#type_id").change(function () {
                                        
                                         var req = $(this).children("option:selected").attr("data-doc-req");
                                          var  hard = $(this).children("option:selected").attr("data-hard-copy");
                                       //   level = $(this).children("option:selected").attr("data-verification-level");
                                         limit_variance=$(this).children("option:selected").attr("data-limit_variance");
                                        limit=$(this).children("option:selected").attr("data-limit");
                                     
                                     
                                        if (req == 'Yes') {
                                            $('#doc-att-yes').find('input').prop("checked", "checked").trigger('click');
                                            $('#doc-att-no').find('input').prop("checked", false);
                                            $('#doc-att-no').hide();

                                        } else {
                                            $('#doc-att-yes').find('input').prop("checked", false);
                                            $('#doc-att-no').find('input').prop("checked", "checked").trigger('click');
                                            $('#doc-att-no').show();
                                        }
                                         $("#hard_copy_accepted").val(hard); 
                                     
                                    })
                                    $("#type_id").trigger("change");
                        

                                });

                                    function CheckApproval(){
                                      
                                      var actualAmout=  parseFloat($("#amount").val());  
                                      var limitVarianceAmout=parseFloat(limit*limit_variance/100);
                                      var totalAmout=parseFloat(limit)+parseFloat(limitVarianceAmout);
                                      if(actualAmout>totalAmout)
                                          $("#verified_approval").val(2);                                 
                                    }

                                $(function () {
                                    $("#datepicker").datepicker({
                                        dateFormat: 'dd M yy',
                                        todayHighlight: 'true',
                                        changeMonth: true,
                                        changeYear: true,

                                        minDate: -30,
                                        maxDate: '+0day',
                                        numberOfMonths: 1

                                    });

                                });



                                function validateImage() {
                                    var img = $("#name").val();

                                    var exts = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                    // split file name at dot
                                    var get_ext = img.split('.');
                                    // reverse name to check extension
                                    get_ext = get_ext.reverse();

                                    if (img.length > 0) {
                                        if ($.inArray(get_ext[0].toLowerCase(), exts) > -1) {
                                            return true;
                                        } else {
                                            swal({
                                                title: "Error",
                                                text: "Upload only jpg, jpeg, png, gif, bmp images",
                                                type: "error"
                                            }).then(() => {
                                                $('#name').val('');
                                            });

                                            return false;
                                        }
                                    } else {
                                        swal("please upload an image");
                                        return false;
                                    }

                                    return false;
                                }



</script>

@endpush

