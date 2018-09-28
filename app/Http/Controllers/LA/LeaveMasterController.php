<?php

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveMaster;
use App\Models\Leave_Type;
use App\Models\Employee;
use App\Models\Comp_Off_Management;
use App\Models\Notification;
use Auth;
use DB;
use Mail;
use Dwij\Laraadmin\Models\LAConfigs;

class LeaveMasterController extends Controller {

    public function edit($id) {
        $leaveMaster = DB::table('leavemaster')
                ->select(['id', 'EmpId', DB::raw('DATE_FORMAT(FromDate,\'%d %b %Y\') as FromDate'), DB::raw('DATE_FORMAT(ToDate,\'%d %b %Y\') as ToDate'), 'NoOfDays', 'LeaveReason', 'LeaveType', 'approved', 'comp_off_id'])
                ->where('id', $id)
                ->first();

        $leave_types = DB::table('leave_types')
                ->whereNull('deleted_at')
                ->get();

        $comp_off = Comp_Off_Management::select(['start_date', 'end_date', 'id'])->where('employee_id', Auth::user()->context_id)->where('availed', '0')->where('approved', '1')->whereNull('deleted_at')->get();

        if (date('Y-m-d', strtotime($leaveMaster->FromDate)) >= date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days'))) {

            $manager = Employee::getManagerDetails(Auth::user()->context_id);

            $data = [
                'before_days' => LAConfigs::getByKey('before_days_leave'),
                'after_days' => LAConfigs::getByKey('after_days_leave'),
                'number_of_leaves' => LAConfigs::getByKey('number_of_leaves'),
                'comp_off_list' => $comp_off,
                'manager' => ucwords($manager->name),
            ];

            $data['leaveMaster'] = $leaveMaster;
            $data['leaveMaster']->leave_type = $leave_types;
            if ($data['leaveMaster']->approved != '') {
                return redirect()->back();
            }

            return view('la.leavemaster.edit', $data);
        } else {
            return redirect()->back()->withErrors(['Trying to be smart!!!']);
        }
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
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name'), DB::raw('employees.emp_code AS emp_code'), DB::raw('employees.total_leaves AS total_leaves'), DB::raw('employees.available_leaves AS available_leaves'), DB::raw('comp_off_managements.deleted_at AS comp_off_deleted')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('comp_off_managements', 'comp_off_managements.id', '=', 'leavemaster.comp_off_id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();

        return view('la.leavemaster.' . $view, [
            'before_days' => LAConfigs::getByKey('before_days_leave'),
            'after_days' => LAConfigs::getByKey('after_days_leave'),
            'number_of_leaves' => LAConfigs::getByKey('number_of_leaves'),
            'leaveMaster' => $leaveMaster,
            'role' => $role,
            'empdetail' => $empdetail
                ]
        );
    }

    public function teamMemberIndex(Request $request) {

        $role = Employee::employeeRole();
        if ($role != 'engineer') {
            $where = 'employees.deleted_at IS NULL ';

            if ($role == 'engineer') {
                //other users
                $view = 'index';
                $where .= ' and leavemaster.EmpId = ' . Auth::user()->context_id;
            } else {
                if ($role == "manager" || $role == "lead") {
                    $engineersUnder = Employee::getEngineersUnder(ucwords($role));
                    if ($engineersUnder != '') {
                        $where .= ' and leavemaster.EmpId IN (' . $engineersUnder . ')';
                    } else {
                        $where .= ' and leavemaster.EmpId = ""';
                    }
                }
                $view = 'Manager_index';
            }

            $leaveMaster = DB::table('leavemaster')
                    ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name'), DB::raw('employees.emp_code AS emp_code'), DB::raw('comp_off_managements.deleted_at AS comp_off_deleted')])
                    ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                    ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                    ->leftJoin('comp_off_managements', 'comp_off_managements.id', '=', 'leavemaster.comp_off_id')
                    ->whereRaw($where)
                    ->get();

            return view('la.leavemaster.' . $view, ['leaveMaster' => $leaveMaster, 'role' => $role]);
        } else {
            return redirect()->back();
        }
    }

    public function create() {

        $leave_types = DB::table('leave_types')
                ->whereNull('deleted_at')
                ->get();

        $manager = Employee::getManagerDetails(Auth::user()->context_id);

        $comp_off = Comp_Off_Management::select(['start_date', 'end_date', 'id'])->where('employee_id', Auth::user()->context_id)->where('availed', '0')->where('approved', '1')->whereNull('deleted_at')->get();

        return view('la.leavemaster.create', [
            'leave_types' => $leave_types,
            'before_days' => LAConfigs::getByKey('before_days_leave'),
            'after_days' => LAConfigs::getByKey('after_days_leave'),
            'number_of_leaves' => LAConfigs::getByKey('number_of_leaves'),
            'comp_off_list' => $comp_off,
            'manager' => ucwords($manager->name),
        ]);
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
        $leaveType = Leave_Type::find($leaveMaster->LeaveType);
        $comp_off_date = '';
        if ($leaveType->name == 'Comp Off' && $request->get('comp_off_id') != '') {
            $leaveMaster->comp_off_id = $request->get('comp_off_id');
            $comp_off_date = Comp_Off_Management::find($request->get('comp_off_id'))->start_date;
        }
        //check existance        
        $LeaveRecord = LeaveMaster::where('EmpId', $request->get('EmpId'))
                ->where('FromDate', $FromDate)
                ->where('ToDate', $ToDate)
                ->where('withdraw', '0')
                ->get();

        $LeaveRecordExists = $LeaveRecord->count();

        if ($LeaveRecordExists > 0) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'You have already applied leave for these dates.');
        } else if (($FromDate < date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($FromDate > date('Y-m-d', strtotime('+' . LAConfigs::getByKey('after_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($ToDate > date('Y-m-d', strtotime('+' . LAConfigs::getByKey('after_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($ToDate < date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days', strtotime(date('Y-m-d')))))) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'Smarty! Your dates are out of applicable range.');
        }

        if ($leaveMaster->save()) {
            //send mail
            $this->sendLeaveMail(false, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason, 'leaveType' => $leaveType->name, 'comp_off_date' => $comp_off_date]);

            //send notification
            $emp_detail = Employee::find(Auth::user()->context_id);
            $notification_data = [
                'display_data' => json_encode(
                        [
                            'message' => ucwords(Auth::user()->name) . ' has applied for leave',
                            'type' => 'leave_by_junior'
                        ]
                ),
                'display_to' => $emp_detail->first_approver
            ];

            Notification::create($notification_data);
            $notification_data['display_to'] = $emp_detail->second_approver;
            Notification::create($notification_data);
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
        $leaveType = Leave_Type::find($leaveMaster->LeaveType);
        $comp_off_date = '';
        if ($leaveType->name == 'Comp Off' && $request->get('comp_off_id') != '') {
            $leaveMaster->comp_off_id = $request->get('comp_off_id');
            $comp_off_date = Comp_Off_Management::find($request->get('comp_off_id'))->start_date;
        }
        //check
        $row = LeaveMaster::where('EmpId', $request->get('EmpId'))
                ->where('FromDate', $FromDate)
                ->where('ToDate', $ToDate)
                ->where('withdraw', '0')
                ->pluck('id');

        $Exists = $row->count();

        if ($Exists > 0 && !in_array($id, $row->toArray())) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'You have already applied leave for these dates.');
        } else if (($FromDate < date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($FromDate > date('Y-m-d', strtotime('+' . LAConfigs::getByKey('after_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($ToDate > date('Y-m-d', strtotime('+' . LAConfigs::getByKey('after_days_leave') . ' days', strtotime(date('Y-m-d'))))) || ($ToDate < date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days', strtotime(date('Y-m-d')))))) {
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('error', 'Smarty! Your dates are out of applicable range.');
        }
        if ($leaveMaster->save()) {
            //send mail
            $this->sendLeaveMail(true, ['start_date' => $start_date, 'end_date' => $end_date, 'days' => $days, 'reason' => $reason, 'leaveType' => $leaveType->name, 'comp_off_date' => $comp_off_date]);

            //send notification
            $emp_detail = Employee::find(Auth::user()->context_id);
            $notification_data = [
                'display_data' => json_encode(
                        [
                            'message' => ucwords(Auth::user()->name) . ' has applied updated leave details.',
                            'type' => 'leave_by_junior'
                        ]
                ),
                'display_to' => $emp_detail->first_approver
            ];

            Notification::create($notification_data);
            $notification_data['display_to'] = $emp_detail->second_approver;
            Notification::create($notification_data);
        }
        return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been Update');
    }

    public function destroy($id) {
        $leaveMaster = LeaveMaster::find($id);

        if (date('Y-m-d', strtotime($leaveMaster->FromDate)) >= date('Y-m-d', strtotime('-' . LAConfigs::getByKey('before_days_leave') . ' days'))) {
            $leaveMaster->delete();
            return redirect(config('laraadmin.adminRoute') . '/leaves')->with('success', 'Information has been  deleted');
        } else {
            return redirect()->back()->withErrors(['Trying to be smart!!!']);
        }
    }

    public function ajaxApproveLeave() {

        $update_field = ['Approved' => $_GET['approved'], 'actionReason' => $_GET['actionReason']];
        if ($_GET['approved']) {
            $update_field['ApprovedBy'] = Auth::user()->context_id;
        } else {
            $update_field['RejectedBy'] = Auth::user()->context_id;
        }

        LeaveMaster::where('id', $_GET['id'])->update($update_field);
        $leavemaster = LeaveMaster::find($_GET['id']);
        $leaveType = Leave_Type::find($leavemaster->LeaveType);
        $employee = Employee::find($leavemaster->EmpId);
        if ($leavemaster->Approved && $leavemaster->ApprovedBy != '') {
            if ($leaveType->name == 'Comp Off') {//compoff
                $comp_off = $employee->comp_off - $_GET['days'];
                $available_leaves = $employee->available_leaves;
                $availed_leaves = $employee->availed_leaves;
                Comp_Off_Management::find($leavemaster->comp_off_id)->update(['availed' => '1']);
            } else {//other
                $comp_off = $employee->comp_off;
                $available_leaves = $employee->available_leaves - $_GET['days'];
                $availed_leaves = $employee->availed_leaves + $_GET['days'];
            }
        } else if (!$leavemaster->Approved && $leavemaster->ApprovedBy != '' && $leavemaster->RejectedBy != '') {
            if ($leaveType->name == 'Comp Off') {//compoff
                $comp_off = $employee->comp_off + $_GET['days'];
                $available_leaves = $employee->available_leaves;
                $availed_leaves = $employee->availed_leaves;
                Comp_Off_Management::find($leavemaster->comp_off_id)->update(['availed' => '0']);
            } else {//other
                $comp_off = $employee->comp_off;
                $available_leaves = $employee->available_leaves + $_GET['days'];
                $availed_leaves = $employee->availed_leaves - $_GET['days'];
            }
        }

        DB::update("update employees set comp_off = $comp_off, available_leaves = $available_leaves, availed_leaves = $availed_leaves where id = ?", [$leavemaster->EmpId]);


        $employee_update = Employee::find($leavemaster->EmpId);

        $mail_data = [
            'approved' => $_GET['approved'],
            'action_by' => ucwords(Auth::user()->name),
            'comment' => $_GET['actionReason'],
            'action_date' => date('d M Y'),
            'mail_to' => $employee_update->email,
            'mail_to_name' => ucwords($employee_update->name),
            'leave_from' => date('d M Y', strtotime($leavemaster->FromDate)),
            'leave_to' => date('d M Y', strtotime($leavemaster->ToDate))
        ];

        //send mail
        $this->sendApprovalMail($mail_data);

        //send notification
        $emp_detail = Employee::find(Auth::user()->context_id);
        $notification_data = [
            'display_data' => json_encode(
                    [
                        'message' => ucwords(Auth::user()->name) . ' has ' . ($_GET['approved'] ? 'Approved' : 'Rejected') . ' your leaves from ' . $mail_data['leave_from'] . ' to ' . $mail_data['leave_from'] . '.',
                        'type' => 'leave_action_by_senior'
                    ]
            ),
            'display_to' => $leavemaster->EmpId
        ];

        Notification::create($notification_data);
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
     * leaveType
     * comp_off_date
     */
    private function sendLeaveMail($updated = false, $data) {
        $lead_manager = DB::table('employees')
                ->select([DB::raw('employee_lead.email as lead_email'), DB::raw('employee_manager.email as manager_email'), DB::raw('employees.name as name')])
                ->whereRaw('employees.id = ' . Auth::user()->context_id)
                ->leftJoin('employees as employee_lead', 'employee_lead.id', '=', 'employees.first_approver')
                ->leftJoin('employees as employee_manager', 'employee_manager.id', '=', 'employees.second_approver')
                ->first();

        $html = "Greetings of the day!<br><br>"
                . "<b>" . ucwords($lead_manager->name) . "</b> has " . (($updated) ? 'updated' : 'applied') . " for leave from <b>" . $data['start_date'] . "</b> to <b>" . $data['end_date'] . "</b> for <b>" . $data['days'] . " days</b> with a reason stated as <b>" . $data['reason'] . "</b>";

        if ($data['leaveType'] == 'Comp Off') {
            $html .= " against Comp off date " . date('d M Y', strtotime($data['comp_off_date']));
        }

        $html .= "."
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";

        $recipients['to'] = [$lead_manager->lead_email, $lead_manager->manager_email];
        $recipients['cc'] = ['ashok.chand@ganitsoft.com'];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
            $m->to($recipients['to'])
                    ->cc($recipients['cc']) //need to add this recipent in mailgun
                    ->subject('Leave Application of ' . Auth::user()->name . '!');
        });
        return true;
    }

    /**
     * Send mail to Employee in case of approval or rejection
     * @param array $data contains 
     * approved 0/1
     * action_by
     * comment
     * action_date
     * mail_to
     * mail_to_name
     * leave_from
     * leave_to
     */
    private function sendApprovalMail($data) {
        $html = "Greetings of the day " . $data['mail_to_name'] . "!<br><br>"
                . "Your leaves are <b>" . (($data['approved']) ? 'Accepted' : 'Rejected') . "</b> by " . $data['action_by'] . " for leave from <b>" . $data['leave_from'] . "</b> to <b>" . $data['leave_to'] . "</b> with a reason stated as <b>" . (($data['comment'] != '') ? $data['comment'] : 'No reason given') . "</b> on " . $data['action_date'] . "."
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";

        $recipients['to'] = [$data['mail_to']];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
            $m->to($recipients['to'])
                    ->subject('Approval of your Leave Application');
        });
        return true;
    }

    public function ajaxDateSearch(Request $request) {

        $role = Employee::employeeRole();
        $where = 'employees.deleted_at IS NULL ';
        if ((($request->start_date != null && $request->start_date != "") && ($request->end_date == '' || $request->end_date == null)) || (($request->end_date != null && $request->end_date != "") && ($request->start_date == null && $request->start_date == ""))) {
            $date = ($request->end_date != '' && $request->end_date == null) ? $request->end_date : $request->start_date;
            $where .= ' and (leavemaster.FromDate <= "' . date('Y-m-d', strtotime($date)) . '" and leavemaster.ToDate >= "' . date('Y-m-d', strtotime($date)) . '")';
        } else if (($request->end_date != null && $request->end_date != "") && ($request->start_date != null && $request->start_date != "")) {
            $where .= ' and (leavemaster.FromDate >= "' . date('Y-m-d', strtotime($request->start_date)) . '" and leavemaster.ToDate <= "' . date('Y-m-d', strtotime($request->end_date)) . '")';
        }

        if ($role == "manager" || $role == "lead") {
            $engineersUnder = Employee::getEngineersUnder(ucwords($role));
            if ($engineersUnder != '') {
                $where .= ' and leavemaster.EmpId IN (' . $engineersUnder . ')';
            } else {
                $where .= ' and leavemaster.EmpId = ""';
            }
        }

        $leaveMaster = DB::table('leavemaster')
                ->select([DB::raw('leave_types.name AS leave_name,leavemaster.*'), DB::raw('employees.name AS Employees_name'), DB::raw('employees.emp_code AS emp_code')])
                ->leftJoin('leave_types', 'leavemaster.LeaveType', '=', 'leave_types.id')
                ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                ->whereRaw($where)
                ->get();

        $html = '<tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>No Of Days</th>
                        <th>Leave Type</th>
                        <th>Purpose</th>
                        <th>Leave Status</th>
                        <th style="width:155px; text-align:center;">Action</th>
                </tr>
                ';
        if (!empty($leaveMaster)) {

            foreach ($leaveMaster as $leaveMasterRow) {
                $html .= '<tr>
                     <td>' . $leaveMasterRow->emp_code . '</td>
                    <td>' . $leaveMasterRow->Employees_name . '</td>

                    <td>' . date('d M Y', strtotime($leaveMasterRow->FromDate)) . '</td>
                    <td>' . date('d M Y', strtotime($leaveMasterRow->ToDate)) . '</td>
                    <td>' . $leaveMasterRow->NoOfDays . '</td>
                    <td>' . (($leaveMasterRow->leave_name != '') ? $leaveMasterRow->leave_name : "Not Specified" ) . '</td> 

                 <td>';
                $html .= '<span  id="btn2" data-toggle="popover" title="' . $leaveMasterRow->LeaveReason . '" data-content="Default popover">Leave Reason ..</span>';

                $html .= '</td>'
                        . '<td class="text-center" clas="status">';
                if ($leaveMasterRow->Approved == '1') {
                    $html .= '<span class="text-success">Approved</span>';
                } else if ($leaveMasterRow->Approved == '0') {
                    $html .= '<span class="text-danger">Rejected</span>';
                } else {
                    $html .= 'Pending';
                }

                $html .= '</td>
                   
                    <td class="text-center">';
                if ($role == 'lead') {
                    if ($leaveMasterRow->Approved == '1' || $leaveMasterRow->Approved == '0') {
                        $html .= 'Action Taken';
                    } else {
                        $html .= '<button type="button" class="btn btn-success" name="Approved" id="Approved" data-id =' . $leaveMasterRow->id . ' onclick="myfunction(this);">Approve</button>';
                        $html .= '<button type="button" class="btn btn" name="Rejected" id="Rejected" data-id =' . $leaveMasterRow->id . 'onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> ';
                    }
                } else if ($role == 'manager') {
                    if (($leaveMasterRow->Approved == '1' || $leaveMasterRow->Approved == '0') && $leaveMasterRow->ApprovedBy != '' && $leaveMasterRow->RejectedBy != '') {
                        $html .= 'Action Taken';
                    } else if ($leaveMasterRow->Approved == '1' && $leaveMasterRow->RejectedBy == '') {
                        $html .= '<button type="button" class="btn btn" name="Rejected" id="Rejected" data-id =' . $leaveMasterRow->id . 'onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> ';
                    } else if ($leaveMasterRow->Approved == '0' && $leaveMasterRow->ApprovedBy == '') {
                        $html .= '<button type="button" class="btn btn-success" name="Approved" id="Approved" data-id =' . $leaveMasterRow->id . ' onclick="myfunction(this);">Approve</button>';
                    } else {
                        $html .= '<button type="button" class="btn btn-success" name="Approved" id="Approved" data-id =' . $leaveMasterRow->id . ' onclick="myfunction(this);">Approve</button>';
                        $html .= '<button type="button" class="btn btn" name="Rejected" id="Rejected" data-id =' . $leaveMasterRow->id . 'onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> ';
                    }
                }


                $html .= ' </td>
                </tr>';
            }
        } else {
            $html .= "<tr><td colspan=8 style='text-align : center;'><b>No Record Found!</b></td></tr>";
        }

        return json_encode(['html' => $html, 'day' => $request->date]);
    }

    /**
     * Withdraw a Leave
     */
    public function ajaxWithdraw(Request $request) {
        $leaveRecord = LeaveMaster::find($request->id);
        if ($leaveRecord->withdraw != 1) {
            LeaveMaster::where('id', $request->id)->update(['withdraw' => 1]);
            if ($leaveRecord->approved == 1) {
                $employee = Employee::find($leavemaster->EmpId);
                $leaveType = Leave_Type::find($leaveRecord->LeaveType);
                $comp_off = $employee->comp_off;
                $available_leaves = $employee->available_leaves;
                $availed_leaves = $employee->availed_leaves;
                if ($leaveType['name'] == 'Comp Off') {//compoff
                    $comp_off_record = Comp_Off_Management::find($leaveRecord->comp_off_id);
                    if ($comp_off_record->deleted_at == '' || $comp_off_record->deleted_at == null) {
                        $comp_off = $employee->comp_off + $leaveRecord->NoOfDays;
                    }
                } else {//other
                    $available_leaves = $employee->available_leaves + $leaveRecord->NoOfDays;
                    $availed_leaves = $employee->availed_leaves - $leaveRecord->NoOfDays;
                }
                DB::update("update employees set comp_off = $comp_off, available_leaves = $available_leaves, availed_leaves = $availed_leaves where id = ?", [$leaveRecord->EmpId]);
            }

            //send notification
            $emp_detail = Employee::find(Auth::user()->context_id);
            $notification_data = [
                'display_data' => json_encode(
                        [
                            'message' => ucwords(Auth::user()->name) . ' has withdrawn the leaves',
                            'type' => 'leave_by_junior',
                            'leave_id' => $leaveRecord->id
                        ]
                ),
                'display_to' => $emp_detail->first_approver
            ];

            Notification::create($notification_data);
            $notification_data['display_to'] = $emp_detail->second_approver;
            Notification::create($notification_data);

            return 'Leave withdrawn successfully!';
        } else {
            return 'Being Smart, ahaan! Already Withdrawn!';
        }
    }

}

?>