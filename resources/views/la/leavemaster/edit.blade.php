@extends("la.layouts.app")
@section("contentheader_title")
Edit Apply  Leave
@endsection
@section("main-content")


        <form method="post" action="{{action('LA\LeaveMasterController@update', $id)}}">
		
       <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input name="_method" type="hidden" value="PATCH">
		 <div class="row">
        
          <div class="form-group col-md-4">
            <label for="name">Employee Id:</label>
            <input type="text" class ="form-control" autocomplete="off" readonly="readonly" name="EmpId" value="{{$leaveMaster->EmpId}}">
          </div>
		  
		  
          <div class="form-group col-md-4">
		  <label for="StartDate" class="control-label">Start Date:</label>
                   <input type="text" class="form-control datepicker" 
                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required  readonly='true' value="{{$leaveMaster->FromDate}}" />
                  
         </div>
		  <div class="form-group col-md-4">
		  <label for="text" class="control-label">End Date:</label>
          
           <input type="text" class="form-control datepicker" id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' value="{{$leaveMaster->ToDate}}" />
         </div>
		 <div class="form-group col-md-4">
            <label for="name">Number Of Days</label>
            <input type="text" class="form-control" name="NoOfDays" autocomplete="off" readonly="readonly" id="NoOfDays" value="{{$leaveMaster->NoOfDays}}">
          </div>
		   <div class="form-group col-md-4">
              <label for="number">Leave Purpose</label>
		<!--	  <textarea type="text" name="LeaveReason" cols="45" rows="6" id="LeaveReason"  placeholder="Leave Purpose" required  value="{{$leaveMaster->LeaveReason}}"></textarea> -->
             <input type="text" class="form-control" name="LeaveReason" autocomplete="off"  placeholder="Leave Purpose" required value="{{$leaveMaster->LeaveReason}}"> 
            </div>
			<div class="form-group col-md-4">
                <label>Leave Type</label>
                     <select name="LeaveType" class="form-control" >
				
                                  <?php
                    if (!empty($leaveMaster->leave_type)) {
                        foreach ($leaveMaster->leave_type as $value) {
                            echo '<option value="' . $value->id . '" '. (($leaveMaster->LeaveType == $value->id) ? 'selected' : '' ).'>' . $value->name . '</option>';
                        }
                    }
                    ?>
				
<!--                  <option value="Casual" @if($leaveMaster->LeaveType=="Casual") selected @endif>Casual Leave</option>
                  <option value="Sick"@if($leaveMaster->LeaveType=="Sick") selected @endif>Sick Leave</option>
                  <option value="Medical"@if($leaveMaster->LeaveType=="Medical") selected @endif>Medical Leave</option>  
                  <option value="CompOff"@if($leaveMaster->LeaveType=="CompOff") selected @endif>Comp Off</option>-->
                </select>
            </div>
		<!--	<div class="form-group col-md-4">
                <label>Leave Duration Type</label>
                <select name="LeaveDurationType" class="form-control"> 
				 <option value="" @if($leaveMaster->LeaveDurationType=="") selected @endif>select</option>
                  <option value=".5"  @if($leaveMaster->LeaveDurationType==".5") selected @endif>Half Day</option>
                  <option value="1"  @if($leaveMaster->LeaveDurationType=="1") selected @endif>Full Day</option>
                 
                </select>
            </div>  -->
			          <div class="col-sm-12 text-right" style="margin-top:60px">
            <button type="submit" class="btn btn-success" style="margin-left:38px">Update</button>
          </div>
		  
        </div>
       
      </form>
  @endsection

@push('scripts')

<!--<script type="text/javascript">  
		 $(document).ready(function(){
			 
			
		// To calulate difference b/w two dates
			function CalculateDiff(isstart) 
			 {
			if($("#datepicker").val()!="" && $("#datepickerto").val()!=""){
			   var start= $("#datepicker").datepicker("getDate");
    var end= $("#datepickerto").datepicker("getDate");
   // days = ((end- start) / (1000 * 60 * 60 * 24))+1;
	// $("#NoOfDays").val(days);
	
		
if( start <= end)
	 
{
	 days = ((end - start) / (1000 * 60 * 60 * 24))+1;
			   $("#NoOfDays").val(days);
	 

	
	 
}
	
else
	
{
	if (!isstart)
      alert(" End date not less then start date");
     $("#datepickerto").val('');  
	$("#NoOfDays").val('');      
	
} 
 

   // alert(Math.round(days));
	 }
	
			 }
				
					
			$("#datepickerto").datepicker().on('changeDate',function(e){
				//$("#datepickerto").datepicker('setStartDate', e.date);
						CalculateDiff(false);
						 			});
			
			
				
			
		});

    
	
  </script>-->
<script type="text/javascript">
    $(document).ready(function () {


        // To calulate difference b/w two dates
        function CalculateDiff(isstart)

        {
            if ($("#datepicker").val() != "" && $("#datepickerto").val() != "") {
                var start = $("#datepicker").datepicker("getDate");

                var end = $("#datepickerto").datepicker("getDate");

                //   days = ((end - start) / (1000 * 60 * 60 * 24))+1;
                //  $("#NoOfDays").val(days);

                if (start <= end)

                {
                    days = ((end - start) / (1000 * 60 * 60 * 24)) + 1;
                    $("#NoOfDays").val(days);




                } else

                {
                    if (!isstart)
                        alert(" End date not less then start date");
                    $("#datepickerto").val('');
                    $("#NoOfDays").val('');

                }


                // alert(Math.round(days));

            }
        }

        $(".datepicker").datepicker().on('changeDate', function (e) {
            CalculateDiff(false);
        });
    });


</script>

@endpush