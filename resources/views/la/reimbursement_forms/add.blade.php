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

                    <div class="row">
                        <div class="form-group col-md-3 hide">
                            <label for="Name">Employee Id:</label>
                            <input type="text" class="form-control" name="emp_id" autocomplete="off" value="<?php echo Auth::user()->context_id; ?>" id="emp_id" placeholder="EmpId" required readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Reimbursement Type</label>
                            <select name="type_id" id="type_id" class="form-control" >
                                <?php
                                if (!empty($reimbursement_types)) {
                                    foreach ($reimbursement_types as $value) {
                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Amount" class="control-label">Amount</label>
                            <input type="text" value="{{ old('Amount')}}" class="form-control" 
                                   id="amount"  name="Amount" autocomplete="off"  placeholder="Amount" />
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Cosharing" class="control-label">Cosharing Name</label>
                            <input type="text" value="{{ old('Cosharing')}}" class="form-control" 
                                   id="cosharing"  name="Cosharing" autocomplete="off"  placeholder="Cosharing" />
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Cosharing_count"  class="control-label">Cosharing Count</label>

                            <input type="text" value="{{ old('Cosharing_count')}}" class="form-control" 
                                   id="cosharingcount"  name="Cosharing_count" autocomplete="off"  placeholder="Cosharing Count" />
                        </div>

                        <div class="form-group col-md-6">

                            <label for="Date" class="control-label">Date*</label>
                            <input class="form-control" id="datepicker" name="date"  type="text" readonly="true"/>
                        </div>


                        <div class="form-group col-md-6">
                            <label for="Number">User Comment*</label>
                            <input type="text" value="{{ old('UserComment')}}" class="form-control" name="user_comment" autocomplete="off" required  maxlength="180" id="user_comment" >   
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Number">Attach Document</label>
                            <table>
                                <tr>
                                    <td>
                                        <input type="radio" name="document_attached" value="1" class="check"> Yes
                                    </td>
                                    <td>
                                        <input type="radio" name="document_attached" value="0" checked class="uncheck"> No
                                    </td>
                                </tr>
                            </table>

                        </div>

                        <div class="form-group col-md-6 imageupload" style="display: none" id="document_attached1">

                            <label for="Number"> Select image :</label>
                            <input  type="file" id="name" class="form-control" name="name[]" placeholder="address" multiple onchange="validateImage()" value="{{ old('name')}}" >

                        </div>

                        <div class="col-md-12 text-right" style="margin-top: 25px;">
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
                                $(document).ready(function () {
                                    var emp_detail = "{{ Session::get('employee_details') }}";
                                    emp_detail = JSON.parse(emp_detail.replace(/&quot;/g, '\"'));
                                    //get dates from session
                                    var dates = "{{ Session::get('holiday_list') }}";
                                    dates = JSON.parse(dates.replace(/&quot;/g, '\"'));





                                    $('select').select2();

                                    //Show/hide comp off list
                                    $('#type_id').on('change', function () {
                                        ReimbursementType(this);
                                    });

                                    $('#cosharing').keypress(function (e) {
                                        var regex = new RegExp("^[a-zA-Z]+$");
                                        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                                        if (regex.test(str)) {
                                            return true;
                                        } else
                                        {
                                            e.preventDefault();
                                            //  swal('Please Enter Alphabate');
                                            return false;
                                        }
                                    });


                                    $("input[name$='document_attached']").click(function () {
                                        var test = $(this).val();

                                        $("div.imageupload").hide();
                                        $('.uncheck').click(function () {
                                            ($("#name").val(''));
                                        })

                                        $("#document_attached" + test).show();
                                    });
                                    $("#amount, #cosharingcount").keypress(function (e) {
                                        if (e.which == 46) {
                                            if ($(this).val().indexOf('.') != -1) {
                                                return false;
                                            }
                                        }

                                        if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                                            return false;
                                        }
                                    });



                                });




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

