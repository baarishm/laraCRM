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

    public $show_action = false;
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
        $module = Module::get('Timesheets');
        $this->custom_cols = ['submitor_id', 'project_id', 'date', 'Time Spent'];
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
            $leads = DB::table('leads')
                    ->select([DB::raw('users.name as lead_name'), DB::raw('leads.id AS lead_id'), DB::raw('users.email AS lead_email')])
                    ->leftJoin('users', 'users.context_id', '=', 'leads.employee_id')
                    ->whereNull('leads.deleted_at')
                    ->get();
            $managers = DB::table('managers')
                    ->select([DB::raw('users.name as manager_name'), DB::raw('managers.id AS manager_id'), DB::raw('users.email AS manager_email')])
                    ->leftJoin('users', 'users.context_id', '=', 'managers.employee_id')
                    ->whereNull('managers.deleted_at')
                    ->get();
            $role_id = DB::table('role_user')->whereRaw('user_id = "' . Auth::user()->id . '"')->first();
            $tasks = DB::table('task_roles')
                    ->select(['name', 'task_id'])
                    ->leftJoin('tasks', 'tasks.id', '=', 'task_roles.task_id')
                    ->whereRaw('role_id = ' . $role_id->role_id . ' or role_id = 0')
                    ->whereNull('tasks.deleted_at')
                    ->get();
            return view('la.timesheets.add', [
                'module' => $module,
                'leads' => $leads,
                'managers' => $managers,
                'tasks' => $tasks,
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
        if ($request->timesheet_token == '') {
            $request->session()->put($request->_token, []);
            $request->timesheet_token = $request->_token;
        }
        $request->session()->push($request->timesheet_token, $request->all());
        $module = Module::get('Timesheets');
        if (Module::hasAccess("Timesheets", "create")) {

            $rules = Module::validateRules("Timesheets", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $request->submitor_id = base64_decode(base64_decode($request->submitor_id));
            $insert_id = Module::insert("Timesheets", $request);

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
//            echo "<pre>".$request->timesheet_token."<br>"; print_r($_SESSION[$request->timesheet_token]);die;
            return view('la.timesheets.add', [
                'module' => $module,
                'leads' => $leads,
                'managers' => $managers,
                'tasks' => $tasks,
                'records' => $request->session()->get($request->timesheet_token),
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

                return view('la.timesheets.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
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

            $insert_id = Module::updateRow("Timesheets", $request, $id);

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
        $this->custom_cols = ['submitor_id', 'project_id', 'date', DB::raw("SUM((hours*60) + minutes)/60 as hours")];
        $values = DB::table('timesheets')
                ->select($this->custom_cols)
                ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                ->whereNull('timesheets.deleted_at')
                ->whereRaw('submitor_id = ' . Auth::user()->id . $project . $date)
                ->groupBy(['date', 'project_id']);
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $col_arr = ['submitor_id', 'project_id', 'date', 'hours'];
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
        $records = $request->session()->pull($_GET['token']);
        $leads = [];
        foreach ($records as $record) {
            $leads[$record['lead_email']][$record['manager_email']][] = $record;
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
                foreach ($tasks as $task) {
                    $html .= "<tr>"
                            . "<td>" . Auth::user()->name . "</td>"
                            . "<td>" . $task['project_name'] . "</td>"
                            . "<td>" . $task['task_name'] . "</td>"
                            . "<td>" . ($task['hours'] + ($task['minutes'] / 60)) . "</td>"
                            . "<td>" . $task['date'] . "</td>"
                            . "</tr>";
                }
                $html .= "</table>";
                $recipients['to'] = $lead_email;
                $recipients['cc'] = $manager_email;
                Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
                    $m->from('varsha.mittal@ganitsoftech.com', 'Your Application');

                    $m->to($recipients['to'])
                            ->cc($recipients['cc']) //need to add this recipent in mailgun
                            ->subject('Timesheet of ' . Auth::user()->name . '!');
                });
            }
            echo "Mail sent successfully!";
        }
    }

}
