
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LeaveManagement  </title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">  
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>  
  </head>
  <body>
    <div class="container">
      <h2>Leave Management System</h2><br/>
      <form method="POST" action="{{url(config('laraadmin.adminRoute').'/leaves/store')}}">
       <input type="hidden" name="_token" value="{{ csrf_token() }}">
		 <div class="row">
        
          <div class="form-group col-md-4">
            <label for="Name">Employee Id:</label>
            <input type="text" class="form-control" name="EmpId" autocomplete="off" id="EmpId" placeholder="EmpId" required  >
          </div>
		
          <div class="form-group col-md-4">
            <label for="Name">Name:</label>
            <input type="text" class="form-control" name="EmpName" autocomplete="off" id="EmpName"  placeholder="EmpName" required>
		
          </div>
		 
          <div class="form-group col-md-4">
		  <label for="StartDate" class="control-label">Start Date:</label>
          <!--  <strong>From Date: </strong>  
            <input class="date form-control"  type="text" id="datepicker" name="date">   -->
			 <input type="text" class="form-control" 
           id="datepicker" ng-model="startDate" name="FromDate" autocomplete="off"  placeholder="FromDate" required />
         </div>
		
          <div class="form-group col-md-4">
          <!--  <strong>To Date: </strong>  -->
		  <label for="text" class="control-label">End Date:</label>
         <!--   <input class="date form-control"  type="text" id="datepickerto" name="Todate"> -->
         <input type="text" class="form-control" id="datepickerto" ng-model="datepickerto" name="ToDate"   placeholder="ToDate" required autocomplete="off" ng-change='checkErr(datepicker,datepickerto)' />	
         			
         </div>
		 
		  
          <div class="form-group col-md-4">
            <label for="Name">Number Of Days</label>
            <input type="text" class="form-control" name="NoOfDays"id="NoOfDays" autocomplete="off">
			<!--<div style="margin:1%;" > </div> -->
          </div>
		   <div class="form-group col-md-4">
              <label for="Number">Leave Purpose</label>
			  <!--<textarea name="LeaveReason" cols="45" rows="6" id="LeaveReason" class="bodytext" placeholder="LeaveReason" required></textarea> -->
         <input type="text" class="form-control" name="LeaveReason" autocomplete="off" placeholder="LeaveReason" required >   
            </div>
			   <div class="form-group col-md-4">
                <label>Leave Type</label>
                <select name="LeaveType" class="form-control">
                  <option value="Casual">Casual Leave</option>
                  <option value="Sick">Sick Leave</option>
                  <option value="Medical">Medical Leave</option>  
                  <option value="CompOff">Comp Off</option>  
                </select>
            </div>
			  <div class="form-group col-md-4">
                <label>Leave Duration Type</label>
                <select name="LeaveDurationType" class="form-control" >
                  <option value=".5">Half Day</option>
                  <option value="1">Full Day</option>
                </select>
            </div>
			<div class="form-group col-md-4" style="margin-top:60px">
            <button type="submit" class="btn btn-success">Submit</button>
          </div>
        </div>
		 @if(count($errors))
            <div class="form-group col-md-4">
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
      </form>
    </div>
  </body>
</html>


<script type="text/javascript">  
		 $(document).ready(function(){
		 // To calulate difference b/w two dates
			function CalculateDiff(isstart) 
			 {
			   if($("#datepicker").val()!="" && $("#datepickerto").val()!=""){
			   var start= $("#datepicker").datepicker("getDate");
               var end= $("#datepickerto").datepicker("getDate");
            //   days = ((end - start) / (1000 * 60 * 60 * 24))+1;
			 //  $("#NoOfDays").val(days);
	
if( start < end)
	 
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
			 
			$("#datepicker").datepicker({
					autoclose: true,
                 	format: 'yyyy-mm-dd',	
					
			}).on('changeDate',function(e){
				//$("#datepickerto").datepicker('setStartDate', e.date);
						CalculateDiff(true);
						 			});
			
			$("#datepickerto").datepicker({
				

					autoclose: true,   
					format: 'yyyy-mm-dd'
				
			}).on('changeDate',function(){
						CalculateDiff(false);
						 			});
		});
	
		
  </script>
 