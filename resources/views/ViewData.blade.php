<!-- ViewData.blade.php -->
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Laravel 5.6 CRUD Tutorial With Example </title>
	<link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">  
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>  
    <style>
/*        table {
    
     border-collapse: collapse;
    width: 50%;
     border-radius: 25px;
}
    table, th, td {
    border: 1px solid black;
     border-collapse:collapse;
         border-spacing: 20;
         text-align: center;
         
 
}
tr {
    height: 50px;
}*/
table {
    width: 100%;
    border-collapse:separate;
    border:solid black 1px;
    border-radius:6px;
    -moz-border-radius:6px;
}

td, th {
    border-left:solid black 1px;
    border-top:solid black 1px;
}

th {
    background-color: #e1e1e2;
    border-top: none;
}

td:first-child, th:first-child {
     border-left: none;
}
</style>
  </head>
  <body>
    <div class="container">
        <div class="col-sm-12"> 
      <h2>View Form</h2><br  />
       <div class="text-right" style="margin-bottom:20px;">  <a href="{{action('LA\LeaveMasterController@edit', $leaveMaster['id'])}}" class="btn btn-warning">Edit</a></div>
      <table class="table table-bordered" >
          <tr>
              <th>Employee Id</th>
               <th>Start Date</th>
                <th>End Date</th>
                 <th>Number Of Days</th>
                  <th>Leave Purpose</th>
                   <th>Leave Type</th>
              
              
          </tr>
          <tr>
              <td>{{$leaveMaster->EmpId}}</td>
              <td>{{$leaveMaster->FromDate}}</td>
                <td>{{$leaveMaster->ToDate}}</td>
                  <td>{{$leaveMaster->NoOfDays}}</td>
                   <td>{{$leaveMaster->LeaveReason}}</td>
                   <td>{{$leaveMaster->LeaveType}}</td>
              
              
          </tr>
          
          
      </table>
      
     
    
              
            
             
             
        
     
      
      
      
      
	    
		
		<!--	<div class="form-group col-md-4">
                <label>Leave Duration Type</label>
                <select name="LeaveDurationType" class="form-control"> 
				 <option value="" @if($leaveMaster->LeaveDurationType=="") selected @endif>select</option>
                  <option value=".5"  @if($leaveMaster->LeaveDurationType==".5") selected @endif>Half Day</option>
                  <option value="1"  @if($leaveMaster->LeaveDurationType=="1") selected @endif>Full Day</option>
                 
                </select>
            </div>  -->
       </div>
    
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