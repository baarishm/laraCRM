<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;

class LeaveMasterController extends Controller
{
    public function edit($id)
    {
        $leaveMaster = LeaveMaster::find($id);
        return view('edit',compact('leaveMaster','id'));
    }
	public function index()
    {
        $leaveMaster=LeaveMaster::all();
        return view('index',compact('leaveMaster'));
    }	
    
  public function create()
    {
       return view('create');
	  
    }

	 public function show(Request $request, $id)
    {
        $leaveMaster= LeaveMaster::find($id);
        return view('ViewData',compact('leaveMaster','$id'));
	 
    }
  
   public function store(Request $request)
    {
		  $this->validate(request(), [
            'EmpId' => 'required',
           
            'FromDate' => 'required|date',
            'ToDate' => 'required|date',
			'LeaveReason' => 'required',
        ]);
		

        $leaveMaster= new LeaveMaster();
		 $leaveMaster->EmpId=$request->get('EmpId');
       
        $FromDate=date_create($request->get('FromDate'));
        $format = date_format($FromDate,"Y-m-d");
        $leaveMaster->FromDate =($format);
		
		$ToDate=date_create($request->get('ToDate'));
        $format = date_format($ToDate,"Y-m-d");
        $leaveMaster->ToDate =($format);
		$leaveMaster->NoOfDays=$request->get('NoOfDays');
		$leaveMaster->LeaveReason=$request->get('LeaveReason');
		$leaveMaster->LeaveType=$request->get('LeaveType');
	//	$leaveMaster->LeaveDurationType=$request->get('LeaveDurationType');
       
        $leaveMaster->save();
        
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been added');
    }
	 public function update(Request $request, $id)
    {
		  $this->validate(request(), [
              'EmpId' => 'required',
              
              'FromDate' => 'required',
             'ToDate' => 'required',
		     'LeaveReason' => 'required',
         ]);
        $leaveMaster= LeaveMaster::find($id);
		 $leaveMaster->EmpId=$request->get('EmpId');
       
		$leaveMaster->FromDate=$request->get('FromDate');
		$leaveMaster->ToDate=$request->get('ToDate');
		 $leaveMaster->NoOfDays=$request->get('NoOfDays');
		 $leaveMaster->LeaveReason=$request->get('LeaveReason');
		$leaveMaster->LeaveType=$request->get('LeaveType');
	//	$leaveMaster->LeaveDurationType=$request->get('LeaveDurationType');
        $leaveMaster->save();
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been Update');
    }
	 public function destroy($id)
    {
        $leaveMaster = LeaveMaster::find($id);
        $leaveMaster->delete();
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success','Information has been  deleted');
    }
	
}
?>