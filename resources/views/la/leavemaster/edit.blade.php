@extends("la.layouts.app")
@section("contentheader_title")
Edit Apply  Leave
@endsection
@section("main-content")


        <form method="post" action="{{action('LA\LeaveMasterController@update', $leaveMaster->id)}}">
		
       <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input name="_method" type="hidden" value="PATCH">
		 <div class="row">
        
          <div class="form-group col-md-4">
            <label for="name">Employee Id:</label>
            <input type="text" class ="form-control" autocomplete="off" readonly="readonly" name="EmpId" value="{{$leaveMaster->EmpId}}">
          </div>
		  
		  
          <div class="form-group col-md-4">
		  <label for="StartDate" class="control-label">Start Date:</label>
                   <input type="text" class="form-control " 
                   id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required  readonly='true' value="{{$leaveMaster->FromDate}}" />
                  
         </div>
		  <div class="form-group col-md-4">
		  <label for="text" class="control-label">End Date:</label>
          
           <input type="text" class="form-control" id="datepickerto" ng-model="datepickerto" name="ToDate"  readonly='true'   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker, datepickerto)' value="{{$leaveMaster->ToDate}}" />
         </div>
		 <div class="form-group col-md-4">
            <label for="name">Number Of Days</label>
            <input type="text" class="form-control" name="NoOfDays" autocomplete="off" readonly="readonly" id="NoOfDays" value="{{$leaveMaster->NoOfDays}}">
          </div>
		   <div class="form-group col-md-4">
              <label for="number">Leave Purpose</label>
		
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

                </select>
            </div>
			          <div class="col-sm-12 text-right" style="margin-top:60px">
            <button type="submit" class="btn btn-success" style="margin-left:38px">Update</button>
          </div>
		  
        </div>
       
      </form>
  @endsection

@push('scripts')

<script type="text/javascript">  
		 $(document).ready(function(){
			
			 
		 // To calulate difference b/w two dates
			function CalculateDiff(isstart) 
			 {
			   if($("#datepicker").val()!="" && $("#datepickerto").val()!=""){
			   var start= $("#datepicker").datepicker("getDate");
               var end= $("#datepickerto").datepicker("getDate");
               days = ((end - start) / (1000 * 60 * 60 * 24))+1;
			   $("#NoOfDays").val(days);


	            // alert(Math.round(days));
			   
			   }
              }
			 
			$("#datepicker").datepicker({
			autoclose: true,
                 	format: 'd M yyyy',	
					
			}).on('changeDate',function(e){
                             $("#datepickerto").val('');
                             $("#NoOfDays").val('');
				$("#datepickerto").datepicker('setStartDate', e.date);
						CalculateDiff(true);
						 			});
			
			$("#datepickerto").datepicker({
				

					autoclose: true,   
					format: 'd M yyyy'
				
			}).on('changeDate',function(){
						CalculateDiff(false);
						 			});
		});
	
		
  </script>

@endpush