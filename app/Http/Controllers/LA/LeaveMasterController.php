<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;
use App\Models\Employee;
use Auth;
use DB;
use Mail;
use Dwij\Laraadmin\Models\LAConfigs;

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

        if ($role == 'superAdmin') {
            $view = 'Manager_index';
        } else {
            $view = 'index';
            $where .= ' and leavemaster.EmpId = ' . Auth::user()->context_id;
        }
        $empdetail = Employee::where('id', Auth::user()->context_id)
                ->first();

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name'), DB::raw('employees.total_leaves AS total_leaves'), DB::raw('employees.available_leaves AS available_leaves')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();


        return view('la.leavemaster.' . $view, ['leaveMaster' => $leaveMaster, 'role' => $role, 'empdetail' => $empdetail]);
    }

    public function teamMemberIndex(Request $request) {

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
        return view('la.leavemaster.create', ['leave_types' => $leave_types, 'before_days' => LAConfigs::getByKey('before_days_leave'), 'after_days' => LAConfigs::getByKey('after_days_leave'), 'number_of_leaves' => LAConfigs::getByKey('number_of_leaves')]);
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
        $start_date = $request->get('FromDate');
        $end_date = $request->get('ToDate');
        $FromDate = date_create($request->get('FromDate'));
        $FromDate = date_format($FromDate, "Y-m-d");
        $leaveMaster->FromDate = ($FromDate);
        $ToDate = date_create($request->get('ToDate'));
        $ToDate = date_format($ToDate, "Y-m-d");
        $leaveMaster->ToDate = ($ToDate);
        $leaveMaster->NoOfDays = $days = $request->get('NoOfDays');
        $leaveMaster->LeaveReason = $reason = $request->get('LeaveReason');
        $leaveMaster->LeaveType = $request->get('LeaveType');
//	$leaveMaster->Approved=$request->get('Approved');
        //check existance        
        $LeaveRecord = LeaveMaster::where('EmpId', $request->get('EmpId'))
                ->where('FromDate', $FromDate)
                ->where('ToDate', $ToDate)
                ->where('withdraw', '0')
                ->get();

        $LeaveRecordExists = $LeaveRecord->count();

        if ($LeaveRecordExists > 0) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'You have already applied leave for these dates.');
        }

        if ($leaveMaster->save()) {
//            $this->sendLeaveMail(false, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason]);
        }

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

        $start_date = $request->get('FromDate');
        $end_date = $request->get('ToDate');

        $FromDate = date_create($request->get('FromDate'));
        $format = date_format($FromDate, "Y-m-d");
        $leaveMaster->FromDate = ($format);
        $ToDate = date_create($request->get('ToDate'));
        $format = date_format($ToDate, "Y-m-d");
        $leaveMaster->ToDate = ($format);
        $leaveMaster->NoOfDays = $days = $request->get('NoOfDays');
        $leaveMaster->LeaveReason = $reason = $request->get('LeaveReason');
        $leaveMaster->LeaveType = $request->get('LeaveType');
//	$leaveMaster->LeaveDurationType=$request->get('LeaveDurationType');
        //check
        $LeaveRecord = LeaveMaster::where('EmpId', $request->get('EmpId'))
                ->where('FromDate', $FromDate)
                ->where('ToDate', $ToDate)
                ->where('withdraw', '0')
                ->get();

        $LeaveRecordExists = $LeaveRecord->count();

        if ($LeaveRecordExists > 0) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'You have already applied leave for these dates.');
        }
        if ($leaveMaster->save()) {
//            $this->sendLeaveMail(true, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason]);
        }
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
        if ($leavemaster->approved && $leavemaster->ApprovedBy != '') {
            $leavemaster = DB::table('employees')->where('id', $leavemaster->EmpId)->decrement('available_leaves', $_GET['days']);
        }
        else if (!$leavemaster->approved && $leavemaster->ApprovedBy != '' && $leavemaster->RejectedBy != '') {
            $leavemaster = DB::table('employees')->where('id', $leavemaster->EmpId)->increment('available_leaves', $_GET['days']);
        }
        
        return "true";
    }

    /**
     * Send mail to Lead and manager in case of leave apply and update
     * @param boolean $updated Record updated or inserted
     * @param array $data contains 
     * start_date
     * end_date
     * days
     * reason
     */
    private function sendLeaveMail($updated = false, $data) {
        $lead_manager = DB::table('employees')
                ->select([DB::raw('employee_lead.email as lead_email'), DB::raw('employee_manager.email as manager_email'), DB::raw('employees.name as name')])
                ->whereRaw('employees.id = ' . Auth::user()->context_id)
                ->leftJoin('employees as employee_lead', 'employee_lead.id', '=', 'employees.first_approver')
                ->leftJoin('employees as employee_manager', 'employee_manager.id', '=', 'employees.second_approver')
                ->first();

        $html = "Greetings of the day!<br><br>"
                . "<b>" . ucfirst($lead_manager->name) . "</b> has " . (($updated) ? 'updated' : 'applied') . " for leave from <b>" . $data['start_date'] . "</b> to <b>" . $data['end_date'] . "</b> for <b>" . $data['days'] . " days</b> with a reason stated as <b>" . $data['reason'] . "</b>."
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit Track Management";

        $recipients['to'] = ['ashok.chand@ganitsoft.com'];
        $recipients['cc'] = [$lead_manager->lead_email, $lead_manager->manager_email];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
            $m->to($recipients['to'])
                    ->cc($recipients['cc']) //need to add this recipent in mailgun
                    ->subject('Leave Application of ' . Auth::user()->name . '!');
        });
        return true;
    }

    public function Teamindex(Request $request) {
        $role = Employee::employeeRole();
        $where = 'employees.deleted_at IS NULL ';
        $view = 'Manager_index';
        if ($role == "manager" || $role == "lead") {
            //manager  
            $engineersUnder = Employee::getEngineersUnder(($role == "manager") ? "Manager" : "Lead");
            if ($engineersUnder != '')
                $where .= " and EmpId IN( " . $engineersUnder . " )";
            else
                $where .= " and EmpId IN( '' )";
        } else {
            //other users
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
            } else if ($request->date == 'N') {
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

    /**
     * Withdraw a Leave
     */
    public function withdraw(Request $request) {
        DB::table('leavemaster')->where('id', $request->id)->update(['withdraw' => 1]);
        return 'withdrawn';
    }

}

?>