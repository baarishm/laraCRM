@extends("la.layouts.app")

@section("contentheader_title")
Edit Apply  Reimbursement
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


                <form method="post" action="{{action('LA\Reimbursement_FormsController@update', $reimbursement_form -> id )}}" enctype="multipart/form-data" id="editForm"  >
                    <input type="hidden" style="display:none" id="isImages" name="isImages" value="0">
                    <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <input type="hidden" id="verified_approval" name="verified_approval" value="1">
                     <input type="hidden" id="hard_copy_accepted" name="hard_copy_accepted" value="1">
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="row">
                         <div class="form-group col-md-4">
                            <label>Manager Name</label>
                            <input type="text" class="form-control" value="{{$manager}}" disabled/>
                        </div>

                        <!--                        <div class="form-group col-md-3 hide">
                                                    <label for="name">Employee Id:</label>
                                                    <input type="text" class ="form-control" autocomplete="off" readonly="readonly" name="EmpId" value="{{$reimbursement_form -> EmpId}}">
                                                </div>-->
                        <div class="form-group col-md-4">

                            <label for="date" class="control-label">Date*</label>
                            <input class="form-control" id="datepicker" name="date"  type="text" readonly="true"  value="{{$reimbursement_form -> date or old('date')}}"/>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Reimbursement Type</label>
                            <select name="type_id" id="type_id" class="form-control" >
                                <?php
                                if (!empty($reimbursement_form)) {
                                    foreach ($reimb_types as $value) {

                                        echo '<option value="' . $value->id . '" ' . (($reimbursement_form->type_id == $value->id) ? 'selected' : '' ) . ' data-limit="'.$value->limit.'" data-limit_variance="'.$value->limit_variance.'" data-hard-copy="'.$value->hard_copy_accepted.'" data-verification-level="'.$value->verification_level.'" >' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Cosharing" class="control-label">Co-Sharing Names</label>
                            <select name="cosharing[]" id="cosharing" class="js-example-basic-multiple" multiple="multiple"  >
                                <?php
                                if (!empty($reimbursement_form)) {
                                    foreach ($employeename as $value) {

                                        echo '<option value="' . $value->id . '" ' . (in_array($value->id, $reimbursement_form->cosharing) ? 'selected' : '') . " > " . $value->name . '</option>';
                                    }
                                }
                                ?>
                            </select>

                        </div>
                        <div class="form-group col-md-2">
                            <label for="Cosharing_count" class="control-label">Total</label>
                            <?php
                                if
 ($reimbursement_form -> cosharing_count !=0 ) {
                                    ?>
                            <input type="text" value="{{count($reimbursement_form->cosharing)}}" class="form-control" 
                                   id="cosharing_count"  name="cosharing_count" autocomplete="off"   />
             <?php           }
                          else {
                              ?>
                                   <input type="text" value="0" class="form-control" 
                                   id="cosharing_count"  name="cosharing_count" autocomplete="off"   />
                     <?php               }
                ?>        
                       
                        </div>
                         <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount (INR)*</label>
                            <input  type="text" value="{{$reimbursement_form -> amount or old('amount')}}" class="form-control" 
                                    id="amount"  name="amount" autocomplete="off"   />
                        </div>
                        <div class="form-group col-md-12">
                            <label for="number">User Comment*</label>

                            <input type="text" class="form-control" name="user_comment" autocomplete="off" id="user_comment" maxlength="180" value="{{$reimbursement_form -> user_comment or old('user_comment')}}"> 
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
 



                        <div class="form-group col-md-12 " id="images-div" >

                            <?php
                            if (count($images) > 0) {
                                ?> 

                                <label for="Number" class="selectimg"> <?php echo "Selected image:" ?></label>
                                <?php
                            }
                            ?>

                            <table style="width: 100%">
                                <?php foreach ($images as $image) { ?>

                                    <tr>
                                        <td> 
                                            <?php
                                            echo $image->name;
                                            ?>
                                        </td>

                                        <td style="text-align: left;">
                                            <div class="form-group col-md-12 text-right">
                                                <a href="<?php echo storage_path('uploads') . '\\' .$image->name ?>" target="_blank"  ><input   value="view" type="button" ></a>



                                                <button data-id="{{$image->id}}" class="btn btn-danger btn-xs removeimage" type="submit"  >Delete</ button>
                                            </div>
                                        </td>
                                    </tr> 
                                    <?php
                                }
                                ?>  
                            </table>



                        </div>


                        <div class="form-group col-md-12 text-right" >
                            <button type="submit" onclick="CheckApproval() class="btn btn-success" id="myButton">Update</button>
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
                                $(document).ready(function () {


                                    var uform = $('#editForm');
                                    var inputs = uform[0].getElementsByClassName('btn btn-danger btn-xs removeimage');
                                    if (inputs.length > 0) {
                                        $("#isImages").val("1");
                                    }


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

                                    $('.removeimage').click(function (e) {
                                        var image = inputs.length - 1;
                                        if (image == 0) {
                                            $("#isImages").val("0");
                                            $("#images-div").remove();



                                        }
                                        e.preventDefault();
                                        var button = $(this);
                                        var id = button.attr('data-id');

                                        // now make the ajax request
                                        $.ajax({
                                            url: "{{ url(config('laraadmin.adminRoute') . '/reimbursement_images_delete_ajax') }}",
                                            data: {id: id},
                                            type: 'GET',
                                            success: function () {
                                                $('div.overlay').addClass('hide');
                                                button.parents('tr').remove();
                                                if ($('#removeimage').length == 0) {
                                                    $('.images-div').remove();
                                                }



                                            }
                                        });
                                    });

                                    $("input[name$='document_attached']").click(function () {
                                        var test = $(this).val();

                                        $("div.imageupload").hide();
                                        $('.uncheck').click(function () {
                                            ($("#name").val(''));

                                        })

                                         if (test==1) {
                                      
                                            $("#document_attached" + test).attr('required', 'required').show();
                                          
                                        }
                                    });


//                                 

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

                                    $("#cosharing").change(function () {
                                        var count = ($(".select2-selection__rendered").children().length - 1);
                                        $("#cosharing_count").val(count);

                                    });
                                    
                                      $("#type_id").change(function () {

                                        var req = $(this).children("option:selected").attr("data-doc-req");
                                         var  hard = $(this).children("option:selected").attr("data-hard-copy");
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
                                        debugger;
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
