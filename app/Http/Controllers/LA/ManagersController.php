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
use App\Models\Manager;
use App\Models\Employee;

class ManagersController extends Controller {

    public $show_action = true;
    public $view_col = 'employee_id';
    public $listing_cols = ['id', 'employee_id'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Managers', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Managers', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Managers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Managers');

        if (Module::hasAccess($module->id)) {
            return View('la.managers.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Managers');
        if (Module::hasAccess("Managers", "create")) {
            return view('la.managers.add', [
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created manager in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Managers", "create")) {

            $rules = Module::validateRules("Managers", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $row = Manager::where('employee_id', $request->employee_id)
                    ->withTrashed()
                    ->get();

            $Exists = $row->count();

            if ($Exists > 0) {
                return redirect()->route(config('laraadmin.adminRoute') . '.managers.create')->withErrors(['message' => 'Manager already exists. Please check or contact Admin to revoke it.']);
            }

            $insert_id = Module::insert("Managers", $request);
            Employee::updateRole('MANAGER', $request->employee_id);

            return redirect()->route(config('laraadmin.adminRoute') . '.managers.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Managers", "view")) {

            $manager = Manager::find($id);
            if (isset($manager->id)) {
                $module = Module::get('Managers');
                $module->row = $manager;

                return view('la.managers.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('manager', $manager);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("manager"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Managers", "edit")) {
            $manager = Manager::find($id);
            if (isset($manager->id)) {
                $module = Module::get('Managers');

                $module->row = $manager;

                return view('la.managers.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('manager', $manager);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("manager"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified manager in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Managers", "edit")) {

            $rules = Module::validateRules("Managers", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }
            
            $row = Manager::where('employee_id', $request->employee_id)
                    ->withTrashed()
                    ->pluck('id');

            $Exists = $row->count();

            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect()->route(config('laraadmin.adminRoute') . '.managers.edit', ['id' => $id])->withErrors(['message' => 'Manager already exists. Please check or contact Admin to revoke it.']);
            }
            
            $insert_id = Module::updateRow("Managers", $request, $id);
            Employee::updateRole('MANAGER', $request->employee_id);

            return redirect()->route(config('laraadmin.adminRoute') . '.managers.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified manager from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Managers", "delete")) {
            Manager::find($id)->forceDelete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.managers.index');
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
        $values = DB::table('managers')->select($this->listing_cols)->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Managers');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/managers/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Managers", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/managers/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Managers", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.managers.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

}
