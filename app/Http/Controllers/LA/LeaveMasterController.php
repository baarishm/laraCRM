<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;
use App\Models\Employee;
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

        $role = Employee::employeeRole();
        $where = 'employees.deleted_at IS NULL ';

        if ($role == 'engineer') {
            //other users
            $view = 'index';
            $where .= ' and leavemaster.EmpId = ' . Auth::user()->context_id;
        } else {
            if ($role == "manager" || $role == "lead") {
                $engineersUnder = Employee::getEngineersUnder(ucfirst($role));
                if ($engineersUnder != '') {
                    $where .= ' and leavemaster.EmpId IN (' . $engineersUnder . ')';
                } else {
                    $where .= ' and leavemaster.EmpId = ""';
                }
            }
            $view = 'Manager_index';
        }

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();

        return view('la.leavemaster.' . $view, ['leaveMaster' => $leaveMaster, 'role' => $role]);
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

    public function Teamindex(Request $request) {
        $role = Employee::employeeRole();
        $where = 'employees.deleted_at IS NULL ';
        if ($role == "manager" || $role == "lead") {
            //manager  
            $engineersUnder = Employee::getEngineersUnder(($role == "manager") ? "Manager" : "Lead");
            if ($engineersUnder != '')
                $where .= " and EmpId IN( " . $engineersUnder . " )";
            else
                $where .= " and EmpId IN( '' )";
            $view = 'Manager_index';
        } else {
            //other users
            $view = 'Manager_index';
            $where .= 'and leavemaster.EmpId = ' . Auth::user()->context_id;
        }

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();

        return view('la.leavemaster.' . $view, ['leaveMaster' => $leaveMaster, 'role' => $role]);
    }

    public function ajaxDateSearch(Request $request) {

        $role = Employee::employeeRole();
        $where = 'employees.deleted_at IS NULL ';
        if ($request->date != null && $request->date != "") {
            if ($request->date == 'P') {
                $filter_date = date('Y-m-d', strtotime(' -1 day'));
            } else if ($request->date == 'T') {
                $filter_date = date('Y-m-d');
            }else if ($request->date == 'N'){
                $filter_date = date('Y-m-d', strtotime(' +1 day'));
            }
            $where .= 'and leavemaster.FromDate <= "' . $filter_date . '" and leavemaster.ToDate >= "' . $filter_date . '"';
        }

        if ($role == "manager" || $role == "lead") {
            $engineersUnder = Employee::getEngineersUnder(ucfirst($role));
            if ($engineersUnder != '') {
                $where .= ' and leavemaster.EmpId IN (' . $engineersUnder . ')';
            } else {
                $where .= ' and leavemaster.EmpId = ""';
            }
        }

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();

        $html = "";

        foreach ($leaveMaster as $leaveMasterRow) {
            $html .= '<tr id="ps">

                    <td>' . $leaveMasterRow->Employees_name . '</td>

                    <td>' . $leaveMasterRow->FromDate . '</td>
                    <td>' . $leaveMasterRow->ToDate . '</td>
                    <td>' . $leaveMasterRow->NoOfDays . '</td>
                    <td>' . (($leaveMasterRow->leave_name != '') ? $leaveMasterRow->leave_name : "Not Specified" ) . '</td> 

                 <td>';
            $html .= '<span  id="btn2" data-toggle="popover" title="' . $leaveMasterRow->LeaveReason . '" data-content="Default popover">Leave Reason ..</span>';
            $html .= '</td>
                   
                    <td class="text-center">';
            if ($leaveMasterRow->Approved == '1') {
                $html .= '<span class="text-success">Approved</span>';
            } else if ($leaveMasterRow->Approved == '0') {
                $html .= '<span class="text-danger">Rejected</span>';
            } else {
                $html .= '<button type="button" class="btn btn-success" name="Approved" id="Approved" data-id =' . $leaveMasterRow->id . ' onclick="myfunction(this);">Approve</button>';
                $html .= '<button type="button" class="btn btn" name="Rejected" id="Rejected" data-id =' . $leaveMasterRow->id . 'onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> ';
            }


            $html .= ' </td>

                </tr>';
        }

        return json_encode(['html' => $html, 'day' => $request->date]);
    }

}

?>