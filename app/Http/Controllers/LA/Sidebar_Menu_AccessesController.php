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
use App\Models\Sidebar_Menu_Access;


class Sidebar_Menu_AccessesController extends Controller {

    public $show_action = true;
    public $view_col = 'role_id';
    public $listing_cols = ['id', 'role_id', 'menu_id'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Sidebar_Menu_Accesses', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Sidebar_Menu_Accesses', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Sidebar_Menu_Accesses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Sidebar_Menu_Accesses');
        
       if (Module::hasAccess($module->id)) {
            return View('la.sidebar_menu_accesses.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module
              
            ]);
          } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new sidebar_menu_access.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created sidebar_menu_access in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Sidebar_Menu_Accesses", "create")) {

            $rules = Module::validateRules("Sidebar_Menu_Accesses", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $row = Sidebar_Menu_Access::where('menu_id', $request->menu_id)
                    ->where('role_id', $request->role_id)
                    ->withTrashed()
                    ->get();

            $Exists = $row->count();

            if ($Exists > 0) {
                return redirect()->route(config('laraadmin.adminRoute') . '.sidebar_menu_accesses.index')->withErrors(['message' => 'Sidebar Access assigned to this role.']);
            }

            $insert_id = Module::insert("Sidebar_Menu_Accesses", $request);

            return redirect()->route(config('laraadmin.adminRoute') . '.sidebar_menu_accesses.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified sidebar_menu_access.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Sidebar_Menu_Accesses", "view")) {

            $sidebar_menu_access = Sidebar_Menu_Access::find($id);
            if (isset($sidebar_menu_access->id)) {
                $module = Module::get('Sidebar_Menu_Accesses');
                $module->row = $sidebar_menu_access;

                return view('la.sidebar_menu_accesses.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('sidebar_menu_access', $sidebar_menu_access);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("sidebar_menu_access"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified sidebar_menu_access.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Sidebar_Menu_Accesses", "edit")) {
            $sidebar_menu_access = Sidebar_Menu_Access::find($id);
            if (isset($sidebar_menu_access->id)) {
                $module = Module::get('Sidebar_Menu_Accesses');

                $module->row = $sidebar_menu_access;

                return view('la.sidebar_menu_accesses.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('sidebar_menu_access', $sidebar_menu_access);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("sidebar_menu_access"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified sidebar_menu_access in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Sidebar_Menu_Accesses", "edit")) {

            $rules = Module::validateRules("Sidebar_Menu_Accesses", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }
            $row = Sidebar_Menu_Access::where('menu_id', $request->menu_id)
                    ->where('role_id', $request->role_id)
                    ->withTrashed()
                    ->pluck('id');

            $Exists = $row->count();

            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect()->route(config('laraadmin.adminRoute') . '.sidebar_menu_accesses.index')->withErrors(['message' => 'Sidebar Access assigned to this role.']);
            }

            $insert_id = Module::updateRow("Sidebar_Menu_Accesses", $request, $id);

            return redirect()->route(config('laraadmin.adminRoute') . '.sidebar_menu_accesses.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified sidebar_menu_access from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Sidebar_Menu_Accesses", "delete")) {
            Sidebar_Menu_Access::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.sidebar_menu_accesses.index');
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
        $values = DB::table('sidebar_menu_accesses')->select($this->listing_cols)->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Sidebar_Menu_Accesses');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/sidebar_menu_accesses/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Sidebar_Menu_Accesses", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/sidebar_menu_accesses/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Sidebar_Menu_Accesses", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.sidebar_menu_accesses.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
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
