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
use App\Models\Resource_Allocation;
use App\Models\Employee;
use App\Models\Project;
use Session;

class Resource_AllocationsController extends Controller {

    public $show_action = true;
    public $view_col = 'project_id';
    public $listing_cols = ['id', 'project_id', 'employee_id', 'start_date', 'end_date', 'allocation'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Resource_Allocations', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Resource_Allocations', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Resource_Allocations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Resource_Allocations');

        $projects = DB::table('resource_allocations')
                ->select([DB::raw('distinct(resource_allocations.project_id)'), DB::raw('projects.name AS project_name')])
                ->leftJoin('projects', 'resource_allocations.project_id', '=', 'projects.id')
                ->whereNull('projects.deleted_at')
                ->get();

        $role = Session::get('role');
        $where = '';
        if ($role == 'manager') {
            $people_under_manager = Employee::getEngineersUnder('Manager');
            if ($people_under_manager != '')
                $where = 'employee_id IN (' . $people_under_manager . ')';
        } else if ($role == 'lead') {
            $people_under_lead = Employee::getEngineersUnder('Lead');
            if ($people_under_lead != '')
                $where = 'employee_id IN (' . $people_under_lead . ')';
        }

        $employees = DB::table('resource_allocations')
                ->select([DB::raw('distinct(resource_allocations.employee_id)'), DB::raw('employees.name AS employee_name')])
                ->leftJoin('employees', 'resource_allocations.employee_id', '=', 'employees.id')
                ->whereNull('employees.deleted_at');

        if ($where != '') {
            $employees = $employees->whereRaw($where);
        }
        $employees = $employees->get();

        if (Module::hasAccess($module->id)) {
            return View('la.resource_allocations.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module,
                'projects' => $projects,
                'employees' => $employees,
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new resource_allocation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Resource_Allocations');
        if (Module::hasAccess("Resource_Allocations", "create") && (Session::get('role') != 'employee')) {
            $emp_list = '';
            if (Session::get('role') != 'employee') {
                $emp_list = Employee::getEngineersUnder(ucfirst(Session::get('role')));
            }
            if ($emp_list != '') {
                $employees = Employee::whereRaw('id IN (' . $emp_list . ')')->get();
            } else {
                $employees = Employee::get();
            }
            return view('la.resource_allocations.add', [
                'module' => $module,
                'employees' => $employees,
                'projects' => Project::get()
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created resource_allocation in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Resource_Allocations", "create") && (Session::get('role') != 'employee')) {

            $rules = Module::validateRules("Resource_Allocations", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $insert_data = $request->all();
            $insert_data['start_date'] = date('Y-m-d', strtotime($request->start_date));
            $insert_data['end_date'] = date('Y-m-d', strtotime($request->end_date));

            //validate user allocation dates inside project date
            $project = Project::find($request->project_id);
            if (isset($project->id)) {
                $pstart = date('d M Y', strtotime($project->start_date));
                $pend = date('d M Y', strtotime($project->end_date));

                if ($insert_data['start_date'] < $project->start_date || $insert_data['start_date'] > $project->end_date) {
                    return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.create')->withErrors(['message' => 'Start date should be between Project\'s start date (' . $pstart . ') and end date(' . $pend . ').'])->withInput();
                }
                if ($insert_data['end_date'] < $project->start_date || $insert_data['end_date'] > $project->end_date) {
                    return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.create')->withErrors(['message' => 'End date should be between Project\'s start date (' . $pstart . ') and end date(' . $pend . ').'])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['message' => 'Project is invalid.'])->withInput();
            }

            $row = Resource_Allocation::where('project_id', $request->project_id)
                    ->where('employee_id', $request->employee_id)
                    ->whereRaw('(((start_date < "' . $insert_data['start_date'] . '" and end_date > "' . $insert_data['start_date'] . '") OR (start_date < "' . $insert_data['end_date'] . '" and end_date > "' . $insert_data['end_date'] . '")) OR (start_date >= "' . $insert_data['start_date'] . '" and end_date <= "' . $insert_data['end_date'] . '"))')
                    ->withTrashed()
                    ->get();

            $Exists = $row->count();

            if ($Exists > 0) {
                return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.create')->withErrors(['message' => 'User already allocated for same dates.'])->withInput();
            }

            $insert_id = Resource_Allocation::create($insert_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified resource_allocation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Resource_Allocations", "view")) {

            $resource_allocation = Resource_Allocation::find($id);
            if (isset($resource_allocation->id)) {
                $module = Module::get('Resource_Allocations');
                $module->row = $resource_allocation;
                return view('la.resource_allocations.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('resource_allocation', $resource_allocation);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("resource_allocation"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified resource_allocation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Resource_Allocations", "edit") && (Session::get('role') != 'employee')) {
            $resource_allocation = Resource_Allocation::find($id);
            if (isset($resource_allocation->id)) {
                $module = Module::get('Resource_Allocations');

                $module->row = $resource_allocation;
                $emp_list = '';
                if (Session::get('role') != 'employee') {
                    $emp_list = Employee::getEngineersUnder(ucfirst(Session::get('role')));
                }
                if ($emp_list != '') {
                    $employees = Employee::whereRaw('id IN (' . $emp_list . ')')->get();
                } else {
                    $employees = Employee::get();
                }

                return view('la.resource_allocations.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'employees' => $employees,
                            'projects' => Project::get(),
                        ])->with('resource_allocation', $resource_allocation);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("resource_allocation")
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified resource_allocation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Resource_Allocations", "edit") && (Session::get('role') != 'employee')) {

            $rules = Module::validateRules("Resource_Allocations", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $update_data = $request->all();
            $update_data['start_date'] = date('Y-m-d', strtotime($request->start_date));
            $update_data['end_date'] = date('Y-m-d', strtotime($request->end_date));

            //validate user allocation dates inside project date
            $project = Project::find($request->project_id);
            if (isset($project->id)) {
                $pstart = date('d M Y', strtotime($project->start_date));
                $pend = date('d M Y', strtotime($project->end_date));
                if ($update_data['start_date'] < $project->start_date || $update_data['start_date'] > $project->end_date) {
                    return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.edit', ['id' => $id])->withErrors(['message' => 'Start date should be between Project\'s start date (' . $pstart . ') and end date(' . $pend . ').'])->withInput();
                }
                if ($update_data['end_date'] < $project->start_date || $update_data['end_date'] > $project->end_date) {
                    return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.edit', ['id' => $id])->withErrors(['message' => 'End date should be between Project\'s start date (' . $pstart . ') and end date(' . $pend . ').'])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['message' => 'Project is invalid.'])->withInput();
            }

            $row = Resource_Allocation::where('project_id', $request->project_id)
                    ->where('employee_id', $request->employee_id)
                    ->whereRaw('(((start_date <= "' . $update_data['start_date'] . '" and end_date >= "' . $update_data['start_date'] . '") OR (start_date <= "' . $update_data['end_date'] . '" and end_date >= "' . $update_data['end_date'] . '")) OR (start_date >= "' . $update_data['start_date'] . '" and end_date <= "' . $update_data['end_date'] . '"))')
                    ->withTrashed()
                    ->pluck('id');

            $Exists = $row->count();
            
            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.edit', ['id' => $id])->withErrors(['message' => 'User already allocated for same dates.'])->withInput();
            }

            $insert_id = Resource_Allocation::find($id)->update($update_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified resource_allocation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Resource_Allocations", "delete") && (Session::get('role') != 'employee')) {
            Resource_Allocation::find($id)->forceDelete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.resource_allocations.index');
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
            $date = ' resource_allocations.start_date <= "' . date('Y-m-d', strtotime($request->date_search)) . '" and resource_allocations.end_date >= "' . date('Y-m-d', strtotime($request->date_search)) . '"';
        }
        $employee = '';
        if ($request->employee_search != '') {
            $date = ' resource_allocations.employee_id = "' . $request->employee_search . '"';
        }

        $value = DB::table('resource_allocations')
                ->select(['resource_allocations.id AS id', 'project_id', 'employee_id', DB::raw('DATE_FORMAT(resource_allocations.start_date, "%d %b %Y") as start_date'), DB::raw('DATE_FORMAT(resource_allocations.end_date, "%d %b %Y") as end_date'), 'allocation'])
                ->join('projects', 'projects.id', '=', 'resource_allocations.project_id')
                ->whereNull('resource_allocations.deleted_at');

        if ($project != "") {
            $value->whereRaw($project);
        }
        if ($date != "") {
            $value->whereRaw($date);
        }
        if ($employee != "") {
            $value->whereRaw($employee);
        } else {
            $emp_list = '';
            if (Session::get('role') != 'employee') {
                $emp_list = Employee::getEngineersUnder(ucfirst(Session::get('role')));
            }
            if ($emp_list != '') {
                $value->whereRaw(' resource_allocations.employee_id IN (' . $emp_list . ')');
            }
        }
        $values = $value;
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Resource_Allocations');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/resource_allocations/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Resource_Allocations", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/resource_allocations/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }
//
//                if (Module::hasAccess("Resource_Allocations", "delete")) {
//                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.resource_allocations.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline']);
//                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
//                    $output .= Form::close();
//                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

}
