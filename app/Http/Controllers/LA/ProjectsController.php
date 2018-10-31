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
use App\Models\Project;
use App\Models\Employee;
use Session;

class ProjectsController extends Controller {

    public $show_action = true;
    public $view_col = 'name';
    public $listing_cols = ['id', 'name', 'manager_id', 'lead_id', 'client_id', 'start_date', 'end_date'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Projects', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Projects', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Projects');
             
         if (Module::hasAccess($module->id))
             {
            return View('la.projects.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new project.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Projects');
        if (Module::hasAccess("Projects", "create")) {
            return view('la.projects.add', [
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created project in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Projects", "create")) {

            $rules = Module::validateRules("Projects", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $insert_data = $request->all();
            $insert_data['start_date'] = date('Y-m-d', strtotime($request->start_date));
            $insert_data['end_date'] = date('Y-m-d', strtotime($request->end_date));

            $row = Project::where('name', $request->name)
                    ->withTrashed()
                    ->get();

            $Exists = $row->count();

            if ($Exists > 0) {
                return redirect()->route(config('laraadmin.adminRoute') . '.projects.create')->withErrors(['message' => 'Project with this name already exists. Please check or contact Admin to revoke it.']);
            }

            $insert_id = Project::create($insert_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.projects.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Projects", "view")) {

            $project = Project::find($id);
            if (isset($project->id)) {
                $module = Module::get('Projects');
                $module->row = $project;

                return view('la.projects.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('project', $project);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("project"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Projects", "edit")) {
            $project = Project::find($id);
            if (isset($project->id)) {
                $module = Module::get('Projects');

                $module->row = $project;

                return view('la.projects.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('project', $project);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("project"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Projects", "edit")) {

            $rules = Module::validateRules("Projects", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $row = Project::where('name', $request->name)
                    ->withTrashed()
                    ->pluck('id');

            $Exists = $row->count();

            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect()->route(config('laraadmin.adminRoute') . '.projects.edit', ['id' => $id])->withErrors(['message' => 'Project with this name already exists. Please check or contact Admin to revoke it.']);
            }

            $update_data = $request->all();
            $update_data['start_date'] = date('Y-m-d', strtotime($request->start_date));
            $update_data['end_date'] = date('Y-m-d', strtotime($request->end_date));

            $insert_id = Project::find($id)->update($update_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.projects.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Projects", "delete")) {
            Project::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.projects.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax() {
         $role = \Session::get('role');
        $values = DB::table('projects')->select(['id', 'name', 'manager_id', 'lead_id', 'client_id', DB::raw('DATE_FORMAT(start_date, "%d %b %Y") as start_date'), DB::raw('DATE_FORMAT(end_date, "%d %b %Y") as end_date')])->whereNull('deleted_at');
          if ($role != 'superAdmin') {  
              $values = DB::table('projects')->select(['id', 'name', 'manager_id', 'lead_id', 'client_id', DB::raw('DATE_FORMAT(start_date, "%d %b %Y") as start_date'), DB::raw('DATE_FORMAT(end_date, "%d %b %Y") as end_date')])->whereNull('deleted_at')
               ->where('manager_id', '=', Auth::user()->context_id)
              ->whereNull('deleted_at');
            
          }   
       $out = Datatables::of($values)->make();
        $data = $out->getData();
     
        $fields_popup = ModuleFields::getModuleFields('Projects');
       
        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/projects/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Projects", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/projects/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Projects", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.projects.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

    /**
     * To get running projects as per the date
     * @param string $date date against which running projects are to be bought
     * @return array list of projects
     */
    public function ajaxProjectList(Request $request) {
        $projects = Project::whereNull('projects.deleted_at')
                ->select([DB::raw('projects.id as id'), DB::raw('projects.name as name')])
                ->whereNull('resource_allocations.deleted_at')
                ->leftJoin('resource_allocations', 'resource_allocations.project_id', '=', 'projects.id')
                ->where('resource_allocations.start_date', '<=', (($request->date) ? $request->date : date('Y-m-d')))
                ->where('resource_allocations.end_date', '>=', (($request->date) ? $request->date : date('Y-m-d')))
                ->where('resource_allocations.employee_id', Auth::user()->context_id)
                ->get();
             
        return $projects;
        
    }

}
