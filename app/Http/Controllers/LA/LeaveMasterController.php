<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;
use Auth;
use DB;

class LeaveMasterController extends Controller {

    public function edit($id) {
        $leaveMaster = DB::table('leavemaster')
                ->select(['id', 'EmpId', DB::raw('DATE_FORMAT(FromDate,\'%d %b %Y\') as FromDate'), DB::raw('DATE_FORMAT(ToDate,\'%d %b %Y\') as ToDate'), 'NoOfDays', 'LeaveReason', 'LeaveType'])
                ->where('id', $id)
                ->get();


        $leave_types = DB::table('leave_types')
                ->whereNull('deleted_at')
                ->get();

        $data['leaveMaster'] = $leaveMaster[0];
        $data['leaveMaster']->leave_type = $leave_types;
        return view('la.leavemaster.edit', $data);
    }

    public function index(Request $request) {
        $role_id = DB::table('roles')->where('display_name', 'Super Admin')->first();
        $user_role_id = DB::table('role_user')->whereRaw('user_id = "' . Auth::user()->id . '"')->first();

        $where = 'employees.deleted_at IS NULL ';
        if ($role_id->id == $user_role_id->role_id) {
            //manager
            $view = 'Manager_index';
        } else {
            //other users
            $view = 'index';
            $where .= ' and leavemaster.EmpId = ' . Auth::user()->context_id;
        }

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();



        return view('la.leavemaster.' . $view, ['leaveMaster' => $leaveMaster]);
    }

    public function create() {

        $leave_types = DB::table('leave_types')
                ->whereNull('deleted_at')
                ->get();
        return view('la.leavemaster.create', ['leave_types' => $leave_types]);
    }

    public function show(Request $request, $id) {
        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->where('leavemaster.id', $id)
                ->whereNull('deleted_at')
                ->first();
        return view('la.leavemaster.ViewData', ['leaveMaster' => $leaveMaster]);
    }

    public function store(Request $request) {
        $this->validate(request(), [
            'EmpId' => 'required',
            'FromDate' => 'required|date',
            'ToDate' => 'required|date',
            'LeaveReason' => 'required',
        ]);


        $leaveMaster = new LeaveMaster();
        $leaveMaster->EmpId = $request->get('EmpId');

        $FromDate = date_create($request->get('FromDate'));
        $format = date_format($FromDate, "Y-m-d");
        $leaveMaster->FromDate = ($format);

        $ToDate = date_create($request->get('ToDate'));
        $format = date_format($ToDate, "Y-m-d");
        $leaveMaster->ToDate = ($format);
        $leaveMaster->NoOfDays = $request->get('NoOfDays');
        $leaveMaster->LeaveReason = $request->get('LeaveReason');
        $leaveMaster->LeaveType = $request->get('LeaveType');
//	$leaveMaster->Approved=$request->get('Approved');

        $leaveMaster->save();

        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been added');
    }

    public function update(Request $request, $id) {
        $this->validate(request(), [
            'EmpId' => 'required',
            'FromDate' => 'required',
            'ToDate' => 'required',
            'LeaveReason' => 'required',
        ]);
        $leaveMaster = LeaveMaster::find($id);
        $leaveMaster->EmpId = $request->get('EmpId');

        $FromDate = date_create($request->get('FromDate'));
        $format = date_format($FromDate, "Y-m-d");
        $leaveMaster->FromDate = ($format);

        $ToDate = date_create($request->get('ToDate'));
        $format = date_format($ToDate, "Y-m-d");
        $leaveMaster->ToDate = ($format);
        $leaveMaster->NoOfDays = $request->get('NoOfDays');
        $leaveMaster->LeaveReason = $request->get('LeaveReason');
        $leaveMaster->LeaveType = $request->get('LeaveType');
//	$leaveMaster->LeaveDurationType=$request->get('LeaveDurationType');
        $leaveMaster->save();
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been Update');
    }

    public function destroy($id) {
        $leaveMaster = LeaveMaster::find($id);
        $leaveMaster->delete();
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been  deleted');
    }

    public function ajaxApproveLeave() {

        $update_field = ['approved' => $_GET['approved']];
        if ($_GET['approved']) {
            $update_field['ApprovedBy'] = Auth::user()->context_id;
        } else {
            $update_field['RejectedBy'] = Auth::user()->context_id;
        }
        $leavemaster = DB::table('leavemaster')->where('id', $_GET['id'])->update($update_field);
        return "true";
    }

//    public function Managerview(Request $request) {
//        $role_id = DB::table('roles')->where('display_name', 'Super Admin')->first();
//        $user_role_id = DB::table('role_user')->whereRaw('user_id = "' . Auth::user()->id . '"')->first();
//
//        $where = 'employees.deleted_at IS NULL ';
//        if ($role_id->id == $user_role_id->role_id) {
//            //manager
//            $view = 'Manager_view';
//        } else {
//            //other users
//            $view = 'Lead_view';
//            $where .= ' and leavemaster.EmpId = ' . Auth::user()->context_id;
//        }
//
//        $leaveMaster = DB::table('leavemaster')
//                ->select([DB::raw('leavemaster.FromDate AS leave_FromDate,leavemaster.*'), DB::raw('employees.name AS Employees_name')])
//                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
//                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
//                ->whereRaw($where)
//                ->get();
//
//
//}
}

?>