<?php

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
use App\Models\Employee;
use App\Models\Reimbursement_Form;
use App\Models\Reimbursement_Document;

class Reimbursement_FormsController extends Controller {

    public $show_action = true;
    public $view_col = 'id';
    public $listing_cols = ['id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'paid_status', 'hard_copy_accepted', 'payment_date', 'cosharing', 'cosharing_count', 'created_by', 'update_by', 'deleted_by', 'date'];
    public $custom_cols = ['id', 'emp_id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'paid_status', 'hard_copy_accepted', 'payment_date', 'cosharing', 'cosharing_count', 'created_by', 'update_by', 'deleted_by', 'date'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
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
    public function index() {
        $module = Module::get('Reimbursement_Forms');
        $this->listing_cols = ['id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'hard_copy_accepted', 'cosharing', 'cosharing_count', 'date'];

        if (Module::hasAccess($module->id)) {

            return View('la.reimbursement_forms.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module,
                'teamMember' => false
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new reimbursement_form.
     *
     * @return \Illuminate\Http\Response
     */
    public function teamMemberReimbursement() {
        $role = Employee::employeeRole();
        if ($role != 'engineer') {
            session(['task_removed' => '']);
            $module = Module::get('Reimbursement_Forms');

            $role = Employee::employeeRole();

            $this->custom_cols = ['emp_id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level ', 'hard_copy_accepted', 'cosharing', 'cosharing_count', 'date'];
            $where = '';
            if ($role == 'manager') {
                $people_under_manager = Employee::getEngineersUnder('Manager');
                if ($people_under_manager != '')
                    $where = 'emp_id IN (' . $people_under_manager . ')';
            } else if ($role == 'lead') {
                $people_under_lead = Employee::getEngineersUnder('Lead');
                if ($people_under_lead != '')
                    $where = 'emp_id IN (' . $people_under_lead . ')';
            }

            $employees = DB::table('reimbursement_forms')
                    ->select([DB::raw('distinct(reimbursement_forms.emp_id)'), DB::raw('employees.name AS employee_name')])
                    ->leftJoin('employees', 'reimbursement_forms.emp_id', '=', 'employees.id')
                    ->whereNull('employees.deleted_at');

            if ($where != '') {
                $employees = $employees->whereRaw($where);
            }
            $employees = $employees->get();
            if (Module::hasAccess($module->id)) {
                return View('la.reimbursement_forms.index', [
                    'show_actions' => $this->show_action,
                    'listing_cols' => $this->custom_cols,
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

    public function create() {


        $reimbursement_types = DB::table('reimbursement_types')
                ->whereNull('deleted_at')
                ->get();

        return view('la.reimbursement_forms.add', [
            'reimbursement_types' => $reimbursement_types
        ]);
    }

    /**
     * Store a newly created reimbursement_form in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->validate(request(), [
            'user_comment' => 'required',
                // 'amount'  =>'required',
                //   'Date' => 'required|date',
        ]);


        if (Module::hasAccess("Reimbursement_Forms", "create")) {

            $rules = Module::validateRules("Reimbursement_Forms", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $insert_data = $request->all();
            $insert_data['emp_id'] = Auth::user()->context_id;
            $insert_data['date'] = date('Y-m-d', strtotime($request->date));
            $images = (isset($insert_data['name']) ? $insert_data['name'] : []);
            unset($insert_data['document_attached_hidden']);
            unset($insert_data['hard_copy_accepted_hidden']);
            unset($insert_data['name']);



            $insert_row = Reimbursement_Form::create($insert_data);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != '') {
                    $images[$i]->move(storage_path('/uploads'), $images[$i]->getClientOriginalName());
                    Reimbursement_Document::insert([
                        'reimbursement_application_id' => $insert_row->id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }

            return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified reimbursement_form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Reimbursement_Forms", "view")) {

            $reimbursement_form = Reimbursement_Form::find($id);
            $reimbursement_form['Date'] = date('d M Y', strtotime($reimbursement_form['Date']));

            if (isset($reimbursement_form->id)) {

                $module = Module::get('Reimbursement_Forms');
//echo "<pre>"; print_r($module);die;
                $module->row = $reimbursement_form;
                //      echo "<pre>"; print_r($reimbursement_form);die;
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
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified reimbursement_form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Reimbursement_Forms", "edit")) {
            $reimbursement_form = Reimbursement_Form::find($id)
                    ->select(['id', 'emp_id', 'amount', 'user_comment', 'type_id', 'document_attached', 'cosharing', 'cosharing_count', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'verified_level'])
                    ->where('id', $id)
                    ->first();

            if (isset($reimbursement_form->id)) {
                if ($reimbursement_form->verified_level == 0) {
                    $module = Module::get('Reimbursement_Forms');

                    $module->row = $reimbursement_form;
                    $reimb_types = DB::table('reimbursement_types')
                            ->whereNull('deleted_at')
                            ->get();

                    $images = DB::table('reimbursement_documents')
                            ->select([DB::raw('reimbursement_forms.* , reimbursement_documents.*')])
                            ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_documents.reimbursement_application_id')
                            ->where('reimbursement_documents.reimbursement_application_id', '=', $id)
                            ->whereNull('reimbursement_documents.deleted_at')
                            ->whereNull('reimbursement_forms.deleted_at')
                            ->get();

                    return view('la.reimbursement_forms.edit', [
                                'module' => $module,
                                'reimb_types' => $reimb_types,
                                'images' => $images,
                                'view_col' => $this->view_col,
                            ])->with('reimbursement_form', $reimbursement_form);
                } else {
                    return redirect()->back();
                }
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("reimbursement_form"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified reimbursement_form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Reimbursement_Forms", "edit")) {

            $rules = Module::validateRules("Reimbursement_Forms", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput(); ;
            }
            $update_data = $request->all();
            $update_data['emp_id'] = Auth::user()->context_id;
            $update_data['date'] = date('Y-m-d', strtotime($request->date));
            $images = $update_data['name'];
            unset($update_data['document_attached_hidden']);
            unset($update_data['hard_copy_accepted_hidden']);
            unset($update_data['name']);
            $image = $update_data['isImages'];
            if ($image == "1") {
                $update_data['document_attached'] = 1;
            }
            unset($update_data['isImages']);
            $insert_id = Reimbursement_Form::find($id)->update($update_data);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != '') {
                    $images[$i]->move(storage_path('/uploads'), $images[$i]->getClientOriginalName());
                    Reimbursement_Document::insert([
                        'reimbursement_application_id' => $id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }
            //echo "<pre>"; print_r($images);die;
            return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
        } else {
            //	return redirect(config('laraadmin.adminRoute')."/");
            return redirect(config('laraadmin.adminRoute') . '/reimbursement_forms')->with('success', 'Information has been Update');
        }
    }

    /**
     * Remove the specified reimbursement_form from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Reimbursement_Forms", "delete")) {
            Reimbursement_Form::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

//remove image in reimbursement_documents
    public function removeimagesajax(Request $request) {
        Reimbursement_Document::find($request->id)->delete();
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax(Request $request) {
        $employee = '';
        $date = '';
        if ($request->employee_search != '') {
            $date = ' reimbursement_forms.emp_id = "' . $request->employee_search . '"';
        }
        $this->custom_cols = [($request->teamMember) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', 'type_id', 'amount', 'user_comment', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), DB::raw("(case when (verified_level = 3 ) THEN 'Rejected' 
                        when(verified_level =1) THEN 'Approved' 
                        ELSE 'Pending'  end) AS verified_level"), DB::raw("(case when (hard_copy_accepted = 0) THEN 'No' ELSE 'Yes' end) as hard_copy_accepted"), 'cosharing', 'cosharing_count', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'approved_by', 'rejected_by',
            (!$request->teamMember) ? 'emp_id' : 'id'
        ];
        $where = 'emp_id = ' . Auth::user()->context_id;
        if ($request->teamMember) {
            $where = '';
            $role = Employee::employeeRole();
            if ($role == 'superAdmin') {
                //no condition to be applied
            } else if ($role == 'manager') {
                $people_under_manager = Employee::getEngineersUnder('Manager');
                if ($people_under_manager != '')
                    $where = 'emp_id IN (' . $people_under_manager . ')';
            } else if ($role == 'lead') {
                $people_under_lead = Employee::getEngineersUnder('Lead');
                if ($people_under_lead != '')
                    $where = 'emp_id IN (' . $people_under_lead . ')';
            }
            else if ($role == 'engineer') {
                // $this->show_action = true;
                $where = 'emp_id = ' . Auth::user()->context_id;
            }
        } else {
            $this->show_action = true;
        }
        $value = DB::table('reimbursement_forms')
                ->select($this->custom_cols)
                ->orderBy('id', 'desc')
                ->whereNull('reimbursement_forms.deleted_at');
        if ($where != "") {
            $value->whereRaw($where);
        }
        if ($employee != "") {
            $value->whereRaw($employee);
        }
        $value->orderBy('reimbursement_forms.date', 'desc');
        $values = $value->orderBy('reimbursement_forms.id', 'desc');
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        $col_arr = [($request->teamMember) ? 'emp_id' : 'id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'hard_copy_accepted', 'cosharing', 'cosharing_count', 'date'];
        $fields_popup = ModuleFields::getModuleFields('reimbursement_forms');
        foreach ($fields_popup as $column => $val) {
            if (!in_array($column, $col_arr)) {
                unset($fields_popup[$column]);
            }
        }
        for ($i = 0; $i < count($data->data); $i++) {
            $verified_level = $data->data[$i][5];
            $approved_by = $data->data[$i][10];
            $rejected_by = $data->data[$i][11];
            for ($j = 0; $j < count($col_arr); $j++) {
                $col = $col_arr[$j];

                //action buttons
                if ($j == 0) { //render only when first column is being checked
                    $output = '';
                    if ($this->show_action && !$request->teamMember) {
                        if ($verified_level == 'Pending') {
                            if (Module::hasAccess("Reimbursement_Forms", "edit")) {
                                $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                            }
                            if (Module::hasAccess("Reimbursement_Forms", "delete")) {
                                $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline']);
                                $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                                $output .= Form::close();
                            }
                        } else {
                            $output .= 'Action Taken';
                        }
                    } else if ($request->teamMember) {
                        $output = '';
                        if ($role == 'lead') {
                            if ($verified_level == 'Pending') {
                                $output .= ' <button class="btn btn-success" name="Approved" id="Approved"  type="submit"  data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . '  onclick="myfunction(this)";>Approve</button>';
                                $output .= ' <button class="btn btn" name="Rejected" id="Rejected" data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . ' onclick="myfunction(this)"; id="3"  style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;">Reject</button>';
                            } else {
                                $output .= 'Action Taken';
                            }
                        } else if
                        ($role == 'manager') {
                            if ($verified_level == 'Pending') {
                                $output .= ' <button class="btn btn-success"  data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . ' onclick="myfunction(this)"; id="Approved"  type="submit">Approve</button>';
                                $output .= '   <button class="btn btn"  data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . ' onclick="myfunction(this)";  id="Rejected" style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;">Reject</button>';
                            } else if ($verified_level == 'Approved' && $rejected_by == null) {
                                $output .= '   <button class="btn btn"  data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . ' onclick="myfunction(this)";  id="Rejected" style="background-color: #f55753;border-color: #f43f3b;color: white;margin-left: 5px;">Reject</button>';
                            } else if ($verified_level == 'Rejected' && $approved_by == null) {
                                $output .= ' <button class="btn btn-success"  data-id =' . $data->data[$i][(count($data->data[$i]) - 1)] . ' onclick="myfunction(this)"; id="Approved"  type="submit">Approve</button>';
                            } else {
                                $output .= 'Action Taken';
                            }
                        }
                    }
                    $data->data[$i][count($col_arr)] = (string) $output;
                }

                //link for viewable column
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }

                //replace dependent values with there viewable values
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
            }
        }

        $out->setData($data);
        return $out;
    }

    public function ajaxApproveReimbursement(Request $request) {
        $update_field = ['verified_level' => $_GET['approved']];
        if ($_GET['approved']) {
            $update_field['approved_by'] = Auth::user()->context_id;
        } else {
            $update_field['rejected_by'] = Auth::user()->context_id;
        }

        if ($request->approved == "1") {
            $update_field['verified_level'] = 1;
        } elseif ($request->reject == "0") {
            $update_field['verified_level'] = 2;
        } else {
            $update_field['verified_level'] = 3;
        }

        $insert_id = Reimbursement_Form::find($request->id)->update($update_field);

        return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
        // return json_encode($result);
    }

}
