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
use Mail;

class TimesheetsController extends Controller {

    public $show_action = true;
    public $view_col = '';
    public $listing_cols = ['id', 'submitor_id', 'project_id', 'task_id', 'date', 'hours', 'minutes', 'comments', 'dependency', 'dependency_for', 'dependent_on', 'lead_id', 'manager_id'];
    public $custom_cols = ['id', 'submitor_id', 'project_id', 'task_id', 'date', 'hours', 'minutes', 'comments', 'dependency', 'dependency_for', 'dependent_on', 'lead_id', 'manager_id'];

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
        $this->custom_cols = [/* 'submitor_id', */ 'Id', 'project_id', 'task_id', 'date', 'Time Spent'];
        $projects = DB::table('timesheets')
                ->select([DB::raw('distinct(timesheets.project_id)'), DB::raw('projects.name AS project_name')])
                ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                ->whereNull('projects.deleted_at')
                ->get();
        if (Module::hasAccess($module->id)) {
            return View('la.timesheets.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->custom_cols,
                'projects' => $projects,
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
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
            $forward = $this->leads_managers_tasks_notSubmitted();
            return view('la.timesheets.add', [
                'module' => $module,
                'leads' => $forward['leads'],
                'managers' => $forward['managers'],
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
            $insert_id = Timesheet::create($insert_data);

            $forward = $this->leads_managers_tasks_notSubmitted();
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
                    'record_name' => ucfirst("timesheet"),
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
                $module = Module::get('Timesheets');

                $module->row = $timesheet;
                $forward = $this->leads_managers_tasks_notSubmitted();
//                echo "<pre>"; print_r('here'); die;
                return view('la.timesheets.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'leads' => $forward['leads'],
                            'managers' => $forward['managers'],
                            'tasks' => $forward['tasks'],
                        ])->with('timesheet', $timesheet);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("timesheet"),
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
    public function destroy($id) {
        if (Module::hasAccess("Timesheets", "delete")) {
            Timesheet::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.timesheets.index');
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
            $project = ' and projects.id =' . $request->project_search;
        }
        $date = '';
        if ($request->date_search != '') {
            $date = ' and timesheets.date like "%' . $request->date_search . '%"';
        }
        $this->custom_cols = ['timesheets.id', 'project_id', 'task_id', 'date', DB::raw("SUM((hours*60) + minutes)/60 as hours")];
        $values = DB::table('timesheets')
                ->select($this->custom_cols)
                ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->whereNull('timesheets.deleted_at')
                ->whereRaw('submitor_id = ' . Auth::user()->id . $project . $date)
                ->groupBy(['date', 'project_id', 'task_id','hours', 'minutes']);
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $col_arr = [/* 'submitor_id', */ 'id', 'project_id', 'task_id', 'date', 'hours'];
        $fields_popup = ModuleFields::getModuleFields('Timesheets');
        foreach ($fields_popup as $column => $val) {
            if (!in_array($column, $col_arr)) {
                unset($fields_popup[$column]);
            }
        }
//echo "<pre>"; print_r($fields_popup); die;
        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->custom_cols); $j++) {
                $col = $col_arr[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/timesheets/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Timesheets", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/timesheets/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Timesheets", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.timesheets.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

    public function sendEmailToLeadsAndManagers(Request $request) {
        session(['task_removed' => '']);
        $records = DB::table('timesheets')
                ->select([DB::raw('projects.name as project_name'), DB::raw('tasks.name as task_name'), DB::raw('employee_lead.email as lead_email'), DB::raw('employee_manager.email as manager_email'), DB::raw('timesheets.id as entry_id'), 'hours', 'minutes', 'date'])
                ->whereIn('timesheets.id', $_GET['entry_ids'])
                ->leftJoin('projects', 'projects.id', '=', 'timesheets.project_id')
                ->leftJoin('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->leftJoin('employees as employee_lead', 'employee_lead.id', '=', 'timesheets.lead_id')
                ->leftJoin('employees as employee_manager', 'employee_manager.id', '=', 'timesheets.manager_id')
                ->get();
        $leads = [];
        foreach ($records as $record) {
            $leads[$record->lead_email][$record->manager_email][] = $record;
        }

        //lead loop
        foreach ($leads as $lead_email => $managers) {
            //manager loop
            foreach ($managers as $manager_email => $tasks) {
                $html = 'Hello,'
                        . '<br>'
                        . 'Timesheet of ' . Auth::user()->name . ' is as under: <br><br>'
                        . '<table border="1" style="width: 100%">'
                        . '<thead>'
                        . '<tr>'
                        . '<th>Name</th>'
                        . '<th>Project</th>'
                        . '<th>Task</th>'
                        . '<th>Time Spent</th>'
                        . '<th>Date</th>'
                        . '</tr>'
                        . '</thead>';
                //task loop
                $entry_id_in_email = [];
                foreach ($tasks as $task) {
                    $html .= "<tr>"
                            . "<td>" . Auth::user()->name . "</td>"
                            . "<td>" . $task->project_name . "</td>"
                            . "<td>" . $task->task_name . "</td>"
                            . "<td>" . ($task->hours + ($task->minutes / 60)) . "</td>"
                            . "<td>" . date("d M Y", strtotime($task->date)) . "</td>"
                            . "</tr>";
                    $entry_id_in_email[] = $task->entry_id;
                }
                $html .= "</table>";
                $recipients['to'] = $lead_email;
                $recipients['cc'] = $manager_email;
                if (
                        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
                            $m->from(Auth::user()->email, 'Ganit Timesheet From Portal');

                            $m->to($recipients['to'])
                                    ->cc($recipients['cc']) //need to add this recipent in mailgun
                                    ->subject('Timesheet of ' . Auth::user()->name . '!');
                        })) {
                    DB::table('timesheets')->whereIn('id', $entry_id_in_email)->update(['mail_sent' => '1']);
                }
            }
            echo "Mail sent successfully!";
        }
    }

    /**
     * Used to get list of 
     * Leads
     * Managers
     * Tasks
     * Entries not yet submitted
     */
    private function leads_managers_tasks_notSubmitted() {
        $leads = DB::table('leads')
                ->select([DB::raw('users.name as lead_name'), DB::raw('leads.id AS lead_id'), DB::raw('users.email AS lead_email')])
                ->leftJoin('users', 'users.id', '=', 'leads.employee_id')
                ->get();

        $managers = DB::table('managers')
                ->select([DB::raw('users.name as manager_name'), DB::raw('managers.id AS manager_id'), DB::raw('users.email AS manager_email')])
                ->leftJoin('users', 'users.id', '=', 'managers.employee_id')
                ->get();

        $role_id = DB::table('role_user')->whereRaw('user_id = "' . Auth::user()->id . '"')->first();
        $tasks = DB::table('task_roles')
                ->select(['name', 'task_id'])
                ->leftJoin('tasks', 'tasks.id', '=', 'task_roles.task_id')
                ->whereRaw('role_id = ' . $role_id->role_id . ' or role_id = 0')
                ->whereNull('tasks.deleted_at')
                ->get();
        $task_deleted = (session('task_removed') != '') ? " and timesheets.id NOT IN (" . trim(session('task_removed'), ',') . ")" : '';
        $notSubmitted = DB::table('timesheets')
                ->select([DB::raw('projects.name as project_name'), DB::raw('tasks.name as task_name'), 'hours', 'minutes', 'timesheets.id'])
                ->leftJoin('tasks', 'timesheets.task_id', '=', 'tasks.id')
                ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                ->whereRaw('submitor_id = ' . Auth::user()->id . " and mail_sent = 0"
                        . $task_deleted)
                ->get();

        return [
            'leads' => $leads,
            'managers' => $managers,
            'tasks' => $tasks,
            'notSubmitted' => $notSubmitted,
        ];
    }

}
