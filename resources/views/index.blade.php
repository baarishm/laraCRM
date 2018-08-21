
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Index Page</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
	 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">  
   
   
  </head>
  <body>
    <div class="container">
    <br />
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
	 
     @endif
	 <div class="row">
        
          <div class="form-group col-md-4">
            <label for="Name">Totel Leaves:10</label>
            
          </div>
		
          <div class="form-group col-md-4">
            <label for="Name">availed leave:8</label>
            
		
          </div>
		  <div class="form-group col-md-4">
            <label for="Name">available Leaves:2</label>
            
		
          </div>
		  </div>
	
    <table class="table table-striped">
    <thead>
      <tr>
       <th>EmpId</th>
       
        <th>From Date</th>
		<th>To Date</th>
		<th>Total Day</th>
		<th>Leave Type</th>
	<!--	<th>Leave Duration</th> -->
		<th>Leave Status</th>
		
		
        <th >Action</th>  
		<th ><a  href="leaves/create" class="btn btn-info">Apply Leave</a></th> 
      </tr>
    </thead>
    <tbody>
       @foreach($leaveMaster as $leaveMaster)
      @php
        $FromDate=date('Y-m-d', strtotime($leaveMaster['FromDate']));
		 $ToDate=date('Y-m-d', strtotime($leaveMaster['ToDate']));
		  
        @endphp
      <tr>
	    <td>{{$leaveMaster['EmpId']}}</td>
        
        <td>{{$FromDate}}</td>
		<td>{{$ToDate}}</td>
        <td>{{$leaveMaster['NoOfDays']}}</td>
        <td>{{$leaveMaster['LeaveType']}}</td>
	<!--	 <td>{{$leaveMaster['LeaveDurationType'] ==.5 ? "Half Day" : "Full Day"}}</td>   -->
		 <td>{{$leaveMaster['Approved']}}</td>
        
        
        <td><a href="{{action('LA\LeaveMasterController@show', $leaveMaster['id'])}}" class="btn btn-warning">View</a></td>
        <td>
          <form action="{{action('LA\LeaveMasterController@destroy', $leaveMaster['id'])}}" method="post">
       <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input name="_method" type="hidden" value="DELETE">
            <button class="btn btn-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </div>
  </body>
</html>
