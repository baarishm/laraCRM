<?php

/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projects_Sprint;
use Mail;

class TimesheetsController extends Controller {

    public $show_action = false;
    public $view_col = '';
    public $listing_cols = ['id', 'submitor_id', 'project_id', 'projects_sprint_id', 'task_id', 'date', 'hours', 'minutes', 'comments', 'dependency', 'dependency_for', 'dependent_on', 'lead_id', 'manager_id'];
    public $custom_cols = ['id', 'submitor_id', 'project_id', 'projects_sprint_id', 'task_id', 'date', 'hours', 'minutes', 'comments', 'dependency', 'dependency_for', 'dependent_on', 'lead_id', 'manager_id'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Timesheets', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Timesheets', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Timesheets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        session(['task_removed' => '']);
        $module = Module::get('Timesheets');

        $this->custom_cols = ['Id', 'project_id', 'projects_sprint_id', 'task_id', 'date', 'Time (in hrs)', 'Description', 'Status'];

        $projects = DB::table('timesheets')
                ->select([DB::raw('distinct(timesheets.project_id)'), DB::raw('projects.name AS project_name')])
                ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                ->whereNull('projects.deleted_at')
                ->get();

        $this->show_action = true;
        if (Module::hasAccess($module->id)) {
            return View('la.timesheets.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->custom_cols,
                'projects' => $projects,
                'module' => $module,
                'teamMember' => false
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display a listing of the Timesheets.
     *
     * @return \Illuminate\Http\Response
     */
    public function teamMemberSheet() {
        $role = Employee::employeeRole();
        if ($role != 'engineer') {
            session(['task_removed' => '']);
            $module = Module::get('Timesheets');

            $role = Employee::employeeRole();

            $this->custom_cols = ['submitor_id', 'project_id', 'projects_sprint_id', 'task_id', 'date', 'Time (in hrs)', 'Description', 'Status'];

            $projects = DB::table('timesheets')
                    ->select([DB::raw('distinct(timesheets.project_id)'), DB::raw('projects.name AS project_name')])
                    ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                    ->whereNull('projects.deleted_at')
                    ->get();


            $where = '';
            if ($role == 'manager') {
                $people_under_manager = Employee::getEngineersUnder('Manager');
                if ($people_under_manager != '')
                    $where = 'submitor_id IN (' . $people_under_manager . ')';
            } else if ($role == 'lead') {
                $people_under_lead = Employee::getEngineersUnder('Lead');
                if ($people_under_lead != '')
                    $where = 'submitor_id IN (' . $people_under_lead . ')';
            }

            $employees = DB::table('timesheets')
                    ->select([DB::raw('distinct(timesheets.submitor_id)'), DB::raw('employees.name AS employee_name')])
                    ->leftJoin('employees', 'timesheets.submitor_id', '=', 'employees.id')
                    ->whereNull('employees.deleted_at');

            if ($where != '') {
                $employees = $employees->whereRaw($where);
            }
            $employees = $employees->get();

            if (Module::hasAccess($module->id)) {
                return View('la.timesheets.index', [
                    'show_actions' => $this->show_action,
                    'listing_cols' => $this->custom_cols,
                    'projects' => $projects,
                    'employees' => $employees,
                    'module' => $module,
                    'teamMember' => true
                ]);
            } else {
                return redirect(config('laraadmin.adminRoute') . "/");
            }
        } else {
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new timesheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Timesheets');
        if (Module::hasAccess("Timesheets", "create")) {
            $forward = Timesheet::leads_managers_tasks_notSubmitted();
            $projects = Project::whereNull('projects.deleted_at')
                    ->select([DB::raw('projects.id as id'), DB::raw('projects.name as name')])
                    ->whereNull('resource_allocations.deleted_at')
                    ->leftJoin('resource_allocations', 'resource_allocations.project_id', '=', 'projects.id')
                    ->where('resource_allocations.start_date', '<=', date('Y-m-d'))
                    ->where('resource_allocations.end_date', '>=', date('Y-m-d'))
                    ->where('resource_allocations.employee_id', Auth::user()->context_id)
                    ->get();
            return view('la.timesheets.add', [
                'module' => $module,
                'leads' => $forward['leads'],
                'managers' => $forward['managers'],
                'tasks' => $forward['tasks'],
                'projects' => $projects,
                'projects_sprints' => Projects_Sprint::where('project_id', ((!empty($projects)) ? $projects[0]->project_id : 0))->where('end_date', '>=', date('Y-m-d'))->where('start_date', '<=', date('Y-m-d'))->get(),
                'records' => $forward['notSubmitted'],
                'task_removed' => '',
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display Records to send mail for
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMail(request $request) {
        $module = Module::get('Timesheets');
        if (Module::hasAccess("Timesheets", "create")) {
            $forward = Timesheet::leads_managers_tasks_notSubmitted();
            return view('la.timesheets.sendMail', [
                'module' => $module,
                'tasks' => $forward['tasks'],
                'records' => $forward['notSubmitted'],
                'task_removed' => '',
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created timesheet in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        session(['task_removed' => $request->task_removed]);
        $module = Module::get('Timesheets');
        if (Module::hasAccess("Timesheets", "create")) {

            $rules = Module::validateRules("Timesheets", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $lead_manager_id = DB::table('projects')->select(['lead_id', 'manager_id'])->where('id', $request->project_id)->first();
            $insert_data = $request->all();
            unset($insert_data['task_removed']);
            $insert_data['lead_id'] = $lead_manager_id->lead_id;
            $insert_data['manager_id'] = $lead_manager_id->manager_id;
            $insert_data['submitor_id'] = base64_decode(base64_decode($request->submitor_id));
            $insert_data['date'] = date('Y-m-d', strtotime($request->date));
            $insert_row = Timesheet::create($insert_data);

            if (Timesheet::where('date', $insert_data['date'])->where('submitor_id', $insert_data['submitor_id'])->count() == 1) {
                //send notification
                $emp_detail = Employee::find(Auth::user()->context_id);
                $notification_data = [
                    'display_data' => json_encode(
                            [
                                'message' => ucwords(Auth::user()->name) . ' has added timesheet of date ' . $request->date,
                                'type' => 'timesheet_by_junior'
                            ]
                    ),
                    'display_to' => $emp_detail->first_approver
                ];

                Notification::create($notification_data);
                $notification_data['display_to'] = $emp_detail->second_approver;
                Notification::create($notification_data);
            }

            if ($request->ajax()) {
                return $insert_row->id;
            }

            $forward = Timesheet::leads_managers_tasks_notSubmitted();
            return view('la.timesheets.add', [
                'module' => $module,
                'leads' => $forward['leads'],
                'managers' => $forward['managers'],
                'tasks' => $forward['tasks'],
                'records' => $forward['notSubmitted'],
                'task_removed' => session('task_removed'),
                'token' => ($request->timesheet_token != '') ? $request->timesheet_token : $request->_token
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Timesheets", "view")) {

            $timesheet = Timesheet::find($id);
            if (isset($timesheet->id)) {
                $module = Module::get('Timesheets');
                $module->row = $timesheet;

                return view('la.timesheets.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('timesheet', $timesheet);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("timesheet"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Timesheets", "edit")) {
            $timesheet = Timesheet::find($id);
            if (isset($timesheet->id)) {
                if ($timesheet->date >= date('Y-m-d', strtotime('-1 week'))) {
                    $module = Module::get('Timesheets');
                    $module->row = $timesheet;
                    $forward = Timesheet::leads_managers_tasks_notSubmitted();
                    $projects = Project::whereNull('projects.deleted_at')
                            ->select([DB::raw('projects.id as id'), DB::raw('projects.name as name')])
                            ->whereNull('resource_allocations.deleted_at')
                            ->leftJoin('resource_allocations', 'resource_allocations.project_id', '=', 'projects.id')
                            ->where('resource_allocations.start_date', '<=', $timesheet->date)
                            ->where('resource_allocations.end_date', '>=', $timesheet->date)
                            ->where('resource_allocations.employee_id', Auth::user()->context_id)
                            ->get();

                    return view('la.timesheets.edit', [
                                'module' => $module,
                                'view_col' => $this->view_col,
                                'leads' => $forward['leads'],
                                'managers' => $forward['managers'],
                                'tasks' => $forward['tasks'],
                                'projects' => $projects,
                                'projects_sprints' => Projects_Sprint::where('project_id', $timesheet->project_id)->where('end_date', '>=', $timesheet->date)->where('start_date', '<=', $timesheet->date)->get(),
                            ])->with('timesheet', $timesheet);
                } else {
                    return redirect()->back()->withErrors(['Trying to be smart!!!']);
                }
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("timesheet"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified timesheet in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Timesheets", "edit")) {

            $rules = Module::validateRules("Timesheets", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $lead_manager_id = DB::table('projects')->select(['lead_id', 'manager_id'])->where('id', $request->project_id)->first();
            $update_data = $request->all();
            $update_data['lead_id'] = $lead_manager_id->lead_id;
            $update_data['manager_id'] = $lead_manager_id->manager_id;
            $update_data['submitor_id'] = base64_decode(base64_decode($request->submitor_id));
            $update_data['date'] = date('Y-m-d', strtotime($request->date));
            $update_id = Timesheet::find($id)->update($update_data);
            if ($request->ajax()) {
                return $id;
            }
            return redirect()->route(config('laraadmin.adminRoute') . '.timesheets.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified timesheet from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {
        if (Module::hasAccess("Timesheets", "delete")) {
            $timesheet = Timesheet::find($id);
            if (isset($timesheet->id)) {
                if ($timesheet->date >= date('Y-m-d', strtotime('-1 week'))) {
                    Timesheet::find($id)->delete();
                    //if ajax
                    if ($request->ajax()) {
                        return "Deleted!";
                    }
                    // Redirecting to index() method
                    return redirect()->route(config('laraadmin.adminRoute') . '.timesheets.index');
                } else {
                    return redirect()->back()->withErrors(['Trying to be smart!!!']);
                }
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax(Request $request) {
        $project = '';
        if ($request->project_search != 0) {
            $project = ' projects.id =' . $request->project_search;
        }
        $date = '';
        if ($request->date_search != '') {
            $date = ' timesheets.date like "%' . date('Y-m-d', strtotime($request->date_search)) . '%"';
        }
        $employee = '';
        if ($request->employee_search != '') {
            $date = ' timesheets.submitor_id = "' . $request->employee_search . '"';
        }
        $week = ' timesheets.date >= "' . date('Y-m-d', strtotime('last Monday')) . '" and timesheets.date <= "' . date('Y-m-d', strtotime('last Saturday')) . '"';
        if ($request->week_search != '') {
            $week = ' timesheets.date >= "' . date('Y-m-d', strtotime("Monday", strtotime('this week ' . $request->week_search . ' week'))) . '" and timesheets.date <= "' . date('Y-m-d', strtotime("Saturday", strtotime('this week ' . $request->week_search . ' week'))) . '"';
        }

        $this->custom_cols = [($request->teamMember) ? 'timesheets.submitor_id' : 'timesheets.id', 'project_id', 'projects_sprint_id', 'task_id', 'date', DB::raw("((hours*60) + minutes)/60 as hours"), 'timesheets.comments', DB::raw("(case when (mail_sent = 1) THEN 'Mail Sent' ELSE 'Submitted' end) as mail_sent")];

        $where = 'submitor_id = ' . Auth::user()->context_id;
        if ($request->teamMember) {
            $where = '';
            $role = Employee::employeeRole();
            if ($role == 'superAdmin') {
                //no condition to be applied
            } else if ($role == 'manager') {
                $people_under_manager = Employee::getEngineersUnder('Manager');
                if ($people_under_manager != '')
                    $where = 'submitor_id IN (' . $people_under_manager . ')';
            } else if ($role == 'lead') {
                $people_under_lead = Employee::getEngineersUnder('Lead');
                if ($people_under_lead != '')
                    $where = 'submitor_id IN (' . $people_under_lead . ')';
            } else if ($role == 'engineer') {
                $this->show_action = true;
                $where = 'submitor_id = ' . Auth::user()->context_id;
            }
        }

        $value = DB::table('timesheets')
                ->select($this->custom_cols)
                ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->whereNull('timesheets.deleted_at');
        if ($where != "") {
            $value->whereRaw($where);
        }
        if ($project != "") {
            $value->whereRaw($project);
        }
        if ($date != "") {
            $value->whereRaw($date);
        }
        if ($employee != "") {
            $value->whereRaw($employee);
        }
        if ($week != "") {
            $value->whereRaw($week);
        }
        $value->orderBy('timesheets.date', 'desc');
        $values = $value->orderBy('timesheets.id', 'desc');

        $out = Datatables::of($values)->make();
        $data = $out->getData();
        $col_arr = [($request->teamMember) ? 'submitor_id' : 'id', 'project_id', 'projects_sprint_id', 'task_id', 'date', 'hours', 'comments', 'mail_sent'];
        $fields_popup = ModuleFields::getModuleFields('Timesheets');
        foreach ($fields_popup as $column => $val) {
            if (!in_array($column, $col_arr)) {
                unset($fields_popup[$column]);
            }
        }

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->custom_cols); $j++) {
                $col = $col_arr[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/timesheets/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                if ($col == 'comments') {
                    $data->data[$i][$j] = '<span class="tooltips" title="'.$data->data[$i][$j].'">' . ((strlen($data->data[$i][$j])>20) ? substr($data->data[$i][$j], 0, 20).'...' : $data->data[$i][$j]) . '</span>';
                }
            }

            if ($this->show_action || !$request->teamMember) {
                $output = '';
                if ($data->data[$i][count($this->custom_cols) - 1] != 'Mail Sent' && ($data->data[$i][4] >= date('Y-m-d', strtotime('-1 week')))) {
                    if (Module::hasAccess("Timesheets", "edit")) {
                        $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/timesheets/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                    }

                    if (Module::hasAccess("Timesheets", "delete")) {
                        $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.timesheets.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
                        $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                        $output .= Form::close();
                    }
                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

    /**
     * Export timesheet function
     */
    public function downloadTimesheet() {
        return view('la.timesheets.downloadTimesheet');
    }

    /* Ajax Functions */

    public function sendEmailToLeadsAndManagers(Request $request) {
        if ($_POST['date'] == '') {
            $_POST['date'] = date('Y-m-d');
        }
        if ($_POST['type'] == 'week') {
            $start = date('Y-m-d', strtotime("last monday", strtotime($_POST['date'])));
            $end = date('Y-m-d', strtotime("next saturday", strtotime($_POST['date'])));
        } else {
            $start = $end = $_POST['date'];
        }
        session(['task_removed' => '']);
        $re = DB::table('timesheets')
                ->select([DB::raw('projects.name as project_name'), DB::raw('tasks.name as task_name'), DB::raw('employee_lead.email as lead_email'), DB::raw('employee_manager.email as manager_email'), DB::raw('GROUP_CONCAT(timesheets.id) as entry_id'), DB::raw('SUM(((hours*60)+minutes)/60) as time'), 'date'])
                ->whereRaw('date >= "' . $start . '" and date <= "' . $end . '" and submitor_id = ' . Auth::user()->context_id . " and timesheets.deleted_at IS NULL")
                ->leftJoin('projects', 'projects.id', '=', 'timesheets.project_id')
                ->leftJoin('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->leftJoin('employees as employee_lead', 'employee_lead.id', '=', 'timesheets.lead_id')
                ->leftJoin('employees as employee_manager', 'employee_manager.id', '=', 'timesheets.manager_id')
                ->groupBy(['timesheets.project_id', 'timesheets.task_id']);
        if ($_POST['task_removed'] != '') {
            $re = $re->whereRaw('timesheets.id NOT IN (' . trim($_POST['task_removed'], ',') . ')');
        }
        $records = $re->get();
        $leads = [];
        foreach ($records as $record) {
            $leads[$record->lead_email][$record->manager_email][] = $record;
        }
        //lead loop
//        foreach ($leads as $lead_email => $managers) {
        //manager loop
//            foreach ($managers as $manager_email => $tasks) {
        $html = 'Hello,'
                . '<br>'
                . 'Timesheet of ' . Auth::user()->name . ' is as under: <br><br>'
                . '<table border="1" style="width: 100%">'
                . '<thead>'
                . '<tr>'
                . '<th>Date</th>'
                . '<th>Name</th>'
                . '<th>Project</th>'
                . '<th>Task</th>'
                . '<th>Time Spent (in hrs)</th>'
                . '</tr>'
                . '</thead>';
        //task loop
//        $entry_id_in_email = [];
        $entry_id_in_email = '';
//            foreach ($tasks as $task) {
        foreach ($records as $id => $task) {
            $html .= "<tr>"
                    . "<td>" . date("d M Y", strtotime($task->date)) . "</td>"
                    . "<td>" . Auth::user()->name . "</td>"
                    . "<td>" . $task->project_name . "</td>"
                    . "<td>" . $task->task_name . "</td>"
                    . "<td>" . $task->time . "</td>"
                    . "</tr>";
            $entry_id_in_email .= $task->entry_id . ",";
        }
        $html .= "</table>";
        $recipients['to'] = 'ashok.chand@ganitsoft.com';
        $recipients['cc'] = ['varsha.mittal@ganitsoftech.com'];
//            $recipients['to'] = $lead_email;
//            $recipients['cc'] = $manager_email;
        if (
                Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
                    $m->to($recipients['to'])
                            ->cc($recipients['cc']) //need to add this recipent in mailgun
                            ->subject('Timesheet of ' . Auth::user()->name);
                })) {
            DB::table('timesheets')->whereIn('id', (explode(',', trim($entry_id_in_email, ','))))->update(['mail_sent' => '1']);
        }
//        }
//        }
        echo "Mail sent successfully!";
    }

    public function ajaxHoursWorked() {
        $timesheet = new Timesheet();
        $hours = $timesheet->hoursWorked($_POST['type'], $_POST['date'], isset($_POST['task_removed']) ? $_POST['task_removed'] : '');
        return ($hours != '') ? $hours : '0';
    }

    public function ajaxDatesMailPending() {
        $timesheet = new Timesheet();
        $dates = $timesheet->datesMailPending($_POST['task_removed']);
        $date_array = [];
        if (!empty($dates)) {
            foreach ($dates as $date) {
                $date_array[$date->date] = date('d M Y', strtotime($date->date));
            }
        }
        return json_encode(!empty($date_array) ? $date_array : '');
    }

    /** Excel Export of timesheet
     * @param request $request Inputs from ajax
     * @return file a file downloaded
     * @author Varsha Mittal <varsha.mittal@ganitsoftec.com>
     */
    public function ajaxExportTimeSheetToAuthority(Request $request) {
        //code to export excel
        $sheet_data = Employee::
                        select([DB::raw('employees.emp_code as Emp_Code'), DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as Date'), DB::raw('employees.name as Employee'), DB::raw('projects.name as Project'), DB::raw('projects_sprints.name as Sprint_Name'), DB::raw('tasks.name as Task'), DB::raw('comments as Description'), DB::raw('SUM(((hours*60)+minutes)/60) as Effort_Hours')])
                        ->where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                        ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                        ->whereNull('timesheets.deleted_at')
                        ->leftJoin('timesheets', 'timesheets.submitor_id', '=', 'employees.id')
                        ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                        ->leftJoin('tasks', 'timesheets.task_id', '=', 'tasks.id')
                        ->leftJoin('projects_sprints', 'timesheets.projects_sprint_id', '=', 'projects_sprints.id')
                        ->groupBy('employees.id', 'date', 'timesheets.project_id', 'timesheets.task_id')
                        ->orderBy(DB::raw("STR_TO_DATE(date,'%Y-%m-%d')"), 'desc')
                        ->orderBy('employees.name', 'asc')
                        ->get()->toArray();
        $existingEmployees = [];
        $bade_log = config('custom.bade_log');

        foreach ($sheet_data as $row) {
            if (!in_array($row['Emp_Code'], $existingEmployees)) {
                $existingEmployees[] = $row['Emp_Code'];
            }
        }

        $employees_No_timesheet = Employee::select([DB::raw('employees.emp_code as Emp_Code'), DB::raw('employees.name as Employee'), 'email'])->whereNull('deleted_at')->whereNotIn('emp_code', $existingEmployees)->get()->toArray();

        foreach ($employees_No_timesheet as $defected_employee) {
            if (!in_array($defected_employee['email'], $bade_log)) {
                $sheet_data[] = [
                    'Emp_Code' => $defected_employee['Emp_Code'],
                    'Date' => date('d M Y', strtotime('-1 days')),
                    'Employee' => $defected_employee['Employee'],
                    'Project' => '-',
                    'Sprint_Name' => '-',
                    'Task' => '-',
                    'Description' => '-',
                    'Effort_Hours' => '-'
                ];
            }
        }

        $file = \Excel::create('Timesheet_' . date('d M Y'), function($excel) use ($sheet_data) {
                    $excel->sheet('Timesheets', function($sheet) use ($sheet_data) {
                        $sheet->fromArray($sheet_data);
                    });
                });

        $file = $file->string('xlsx');
        $response = array(
            'name' => 'Timesheet_' . date('d M Y'), //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($file) //mime type of used format
        );
        return response()->json($response);
    }

    /* Ended Ajax Functions */
}
