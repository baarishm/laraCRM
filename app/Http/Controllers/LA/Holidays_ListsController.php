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
use App\Models\Holidays_List;

class Holidays_ListsController extends Controller {

    public $show_action = true;
    public $view_col = 'occasion';
    public $listing_cols = ['id', 'day', 'occasion'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Holidays_Lists', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Holidays_Lists', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Holidays_Lists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Holidays_Lists');

        if (Module::hasAccess($module->id)) {
            return View('la.holidays_lists.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new holidays_list.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created holidays_list in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Holidays_Lists", "create")) {

            $rules = Module::validateRules("Holidays_Lists", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $insert_date = $request->all();
            $insert_date['day'] = date("Y-m-d", strtotime($insert_date['day']));
            $insert_id = Holidays_List::create($insert_date);

            return redirect()->route(config('laraadmin.adminRoute') . '.holidays_lists.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified holidays_list.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Holidays_Lists", "view")) {

            $holidays_list = Holidays_List::find($id);
            if (isset($holidays_list->id)) {
                $module = Module::get('Holidays_Lists');
                $module->row = $holidays_list;

                return view('la.holidays_lists.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('holidays_list', $holidays_list);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("holidays_list"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified holidays_list.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Holidays_Lists", "edit")) {
            $holidays_list = Holidays_List::find($id);
            if (isset($holidays_list->id)) {
                $module = Module::get('Holidays_Lists');

                $module->row = $holidays_list;

                return view('la.holidays_lists.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('holidays_list', $holidays_list);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("holidays_list"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified holidays_list in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Holidays_Lists", "edit")) {

            $rules = Module::validateRules("Holidays_Lists", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $update_data = $request->all();
            $update_data['day'] = date("Y-m-d", strtotime($update_data['day']));

            $update_id = Holidays_List::find($id)->update($update_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.holidays_lists.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified holidays_list from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Holidays_Lists", "delete")) {
            Holidays_List::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.holidays_lists.index');
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
        $values = DB::table('holidays_lists')->select($this->listing_cols)->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Holidays_Lists');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col && $col != 'day') {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/holidays_lists/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                } 
                if ($col == "day") {
                    $data->data[$i][$j] = date('d M Y', strtotime($data->data[$i][$j]));
                }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Holidays_Lists", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/holidays_lists/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Holidays_Lists", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.holidays_lists.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline']);
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
