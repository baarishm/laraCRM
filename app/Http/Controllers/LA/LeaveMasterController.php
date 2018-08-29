<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;
use Auth;
use DB;
use Mail;

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

        if ($leaveMaster->save()) {
            $this->sendLeaveMail(false, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason]);
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

        if ($leaveMaster->save()) {
            $this->sendLeaveMail(true, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason]);
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

        $recipients['to'] = $lead_manager->lead_email;
        $recipients['cc'] = $lead_manager->manager_email;

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
            $m->from('varsha.mittal@ganitsoftech.com', 'Leave Application From Portal');

            $m->to($recipients['to'])
                    ->cc($recipients['cc']) //need to add this recipent in mailgun
                    ->subject('Leave Application of ' . Auth::user()->name . '!');
        });
        return true;
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
//                ->select([DB::raw('leavemaster.FromDate AS leave_FromDate, leavemaster.*'), DB::raw('employees.name AS Employees_name')])
//                ->leftJoin('leave_types', 'leavemaster.LeaveType', ' = ', 'leave_types.id')
//                ->leftJoin('employees', 'employees.id', ' = ', 'leavemaster.EmpId')
//                ->whereRaw($where)
//                ->get();
//
//
//}
}

?>