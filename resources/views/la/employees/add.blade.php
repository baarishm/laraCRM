@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/employees') }}">Employees</a> :
@endsection
@section("section", "Employees")
@section("section_url", url(config('laraadmin.adminRoute') . '/employees'))


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

<!--<div class="box">
    <div class="box-header">

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['action' => 'LA\EmployeesController@store', 'id' => 'employee-add-form']) !!}
                @la_form($module)

                {{--
                                        @la_input($module, 'name')
                                        @la_input($module, 'gender')
                                        @la_input($module, 'mobile')
                                        @la_input($module, 'mobile2')
                                        @la_input($module, 'email')
                                        @la_input($module, 'date_birth')
                                        @la_input($module, 'city')
                                        @la_input($module, 'address')
                                        @la_input($module, 'about')
                                        @la_input($module, 'first_approver')
                                        @la_input($module, 'second_approver')
                                        @la_input($module, 'dept')
                                        @la_input($module, 'salary_cur')
                                        @la_input($module, 'date_hire')
                                        @la_input($module, 'is_confirmed')
                                       
                                        --}}
                <div class="form-group">
                    <label for="role">Role* :</label>
                    <select class="form-control" required="1" data-placeholder="Select Role" rel="select2" name="role">
<?php $roles = App\Role::all(); ?>
                        @foreach($roles as $role)
                        @if($role->id != 1 || Entrust::hasRole("SUPER_ADMIN"))
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                  
                <br>
                <div class="form-group">
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right cancel-button"><a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}">Cancel</a></button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>-->

<div class="box entry-form">
    <div class="box-body">
        <!--        <div class="row">-->
        <div class="col-md-10 col-md-offset-1">
            <!--                {!! Form::open(['action' => 'LA\EmployeesController@store', 'id' => 'employee-add-form']) !!}-->
            <form method="POST" action="{{url(config('laraadmin.adminRoute').'/employees')}}" >
                <input type="hidden" name="_token" value="{{ csrf_token()}}">


                <div class="row">
                    <div class="form-group col-md-4">
                        <span><h5>Name*</h5></span>
                        <input type="text" class="form-control" id="name"  autocomplete="off" />
                    </div>
                    <div class="form-group col-md-4 label-radio">
                        <span><h5>Gender</h5></span>
                        <label><input type="radio" name="gender" value="male" id="male" checked> Male</label>
                        <label><input type="radio" name="gender" id="female" value="female"> Female</label>
                    </div>
                     <div class="form-group col-md-4">
                        <span><h5>DOB*</h5></span>
                        <input type="text"  class="form-control" 
                               id="datepicker"  autocomplete="off"   readonly='true' />
                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>Mobile Number*</h5></span>
                        <input type="text" class="form-control" id="mobile" autocomplete="off" />
                    </div>

                    <div class="form-group col-md-4">
                        <span><h5>Alternate mobile number*</h5></span>
                        <!--                            <label>Alternate mobile number</label>-->
                        <input type="text" class="form-control" id="mobile2" autocomplete="off" />
                    </div>

                  
                    <div class="form-group col-md-4">
                        <span><h5>Email Id*</h5></span>
                        <!--                            <label>Email Id</label>-->
                        <input type="text" class="form-control" id="email" autocomplete="off" />
                    </div>
                     <div class="form-group col-md-4">
                        <span><h5>Address*</h5></span>
                        <!--                        <div class="form-group col-md-3">
                                                    <label for="Number">Address :</label>-->
                        <input type="text"  class="form-control" name="address" autocomplete="off"  maxlength="180"  >   
                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>City*</h5></span>
                        <!--                        <div class="form-group col-md-3">
                                                    <label for="Name">City</label>-->
                        <input type="text"  class="form-control"  name="city" id="city" autocomplete="off" >

                    </div>
                   
                   
                    <div class="form-group col-md-4">
                        <span><h5>About</h5></span>
                        <!--                         <div class="form-group col-md-3">
                                                    <label for="Number">About</label>-->
                        <input type="text"  class="form-control" name="about" autocomplete="off"  maxlength="180"  >   
                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>First Approver* </h5></span>
                        <!--                       <div class="col-md-3">
                                                   <label for="Number">First Approver </label>-->
                        <select id="First_approver" >
                            <option value="" selected="selected" >Select First Approver</option>
                            <?php
                            if (!empty($teamname)) {
                                foreach ($teamname as $value) {
                                    echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>Second Approver* </h5></span>
                        <!--                     <div class="col-md-3">
                                                   <label for="Number">Second Approver </label>-->
                        <select id="second_approver" >
                            <option value="" selected="selected" >Select Second Approver</option>
                            <?php
                            if (!empty($teamname)) {
                                foreach ($teamname as $value) {
                                    echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!--                         <div class="form-group col-md-3">
                                                <label for="Number">First Approver </label>
                                                <input type="text" class="form-control" name="address" autocomplete="off" maxlength="180"  >   
                                            </div>-->
                    <!--                         <div class="form-group col-md-3">
                                                <label for="Number">second Approver</label>
                                                <input type="text" class="form-control" name="address" autocomplete="off" maxlength="180"  >   
                                            </div>-->
                    <div class="form-group col-md-4">
                        <span><h5>Department </h5></span>
                        <!--                        <div class="form-group col-md-3">
                                                    <label for="Number">Department</label>-->
                        <input type="text"  class="form-control" name="Department" autocomplete="off"  maxlength="180" id="dept"  >   
                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>DOJ* </h5></span>
                        <!--                        <div class="form-group col-md-3">
                                                    <label for="Number">Doj</label>-->
                        <input type="text" class="form-control" name="address" autocomplete="off"  maxlength="180" id="datejoin"  >   
                    </div>
                    <div class="form-group col-md-4 label-radio">
                        <span><h5>Conform/Notconform</h5></span>
                        <label> <input type="radio" name="is_confirmed" value="Conform" id="Conform" checked> Conform</label>
                        <label> <input type="radio" name="is_confirmed" id="notConform" value="notConform"> Not Conform</label>

                    </div>
                    <div class="form-group col-md-4">
                        <span><h5>Image* </h5></span>
                        <!--                     <div class="form-group col-md-3">
                                                    <label for="Number">Image</label>-->
                        <input type="File" class="form-control" name="image" autocomplete="off"  maxlength="180" id="image"  >   
                    </div>
                    <div class="form-group col-md-4">
                        <label for="role">Role* :</label>
                        <select class="form-control" required="1" data-placeholder="Select Role" rel="select2" name="role">
                            <?php $roles = App\Role::all(); ?>
                            @foreach($roles as $role)
                            @if($role->id != 1 || Entrust::hasRole("SUPER_ADMIN"))
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-8 text-right" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
            <!--            </div>-->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $("#employee-add-form").validate({

        });
        var date = new Date().toShortFormat();
        $('[name="date_hire"]').val(date);
    });


    $(function ()
    {
        $("#datepicker, #datejoin").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0",
            format: 'd M yyyy'});
    });
    $('#First_approver,#second_approver').select2();
    
</script>
@endpush
