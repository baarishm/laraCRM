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

use App\Models\Reimbursement_Form;

class Reimbursement_FormsController extends Controller
{
	public $show_action = true;
	public $view_col = 'emp_id';
	public $listing_cols = ['id', 'emp_id', 'type_id', 'amount', 'user_comment', 'verified_level', 'paid_status', 'document_attached', 'verfication_status', 'hard_copy_accepted', 'payment_date', 'cosharing', 'cosharing_count', 'created_by', 'update_by', 'deleted_by', 'date'];
	
	public function __construct() {
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('Reimbursement_Forms', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('Reimbursement_Forms', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the Reimbursement_Forms.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Reimbursement_Forms');
		
		if(Module::hasAccess($module->id)) {
			return View('la.reimbursement_forms.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new reimbursement_form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created reimbursement_form in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Reimbursement_Forms", "create")) {
		
			$rules = Module::validateRules("Reimbursement_Forms", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			$insert_id = Module::insert("Reimbursement_Forms", $request);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified reimbursement_form.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Reimbursement_Forms", "view")) {
			
			$reimbursement_form = Reimbursement_Form::find($id);
			if(isset($reimbursement_form->id)) {
				$module = Module::get('Reimbursement_Forms');
				$module->row = $reimbursement_form;
				
				return view('la.reimbursement_forms.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('reimbursement_form', $reimbursement_form);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("reimbursement_form"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified reimbursement_form.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Reimbursement_Forms", "edit")) {			
			$reimbursement_form = Reimbursement_Form::find($id);
			if(isset($reimbursement_form->id)) {	
				$module = Module::get('Reimbursement_Forms');
				
				$module->row = $reimbursement_form;
				
				return view('la.reimbursement_forms.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('reimbursement_form', $reimbursement_form);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("reimbursement_form"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified reimbursement_form in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Reimbursement_Forms", "edit")) {
			
			$rules = Module::validateRules("Reimbursement_Forms", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			$insert_id = Module::updateRow("Reimbursement_Forms", $request, $id);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified reimbursement_form from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Reimbursement_Forms", "delete")) {
			Reimbursement_Form::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}
	
	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax()
	{
		$values = DB::table('reimbursement_forms')->select($this->listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Reimbursement_Forms');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/reimbursement_forms/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Reimbursement_Forms", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/reimbursement_forms/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("Reimbursement_Forms", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}
}
