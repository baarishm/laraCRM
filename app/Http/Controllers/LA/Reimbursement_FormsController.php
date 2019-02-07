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
use App\Models\Department;
use App\Models\Reimbursement_Form;
use App\Models\Reimbursement_Document;
use App\Models\Reimbursement_Approval;

class Reimbursement_FormsController extends Controller {

    public $show_action = true;
    public $view_col = 'id';
    public $listing_cols = ['id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'hard_copy_attached', 'cosharing', 'cosharing_count', 'created_by', 'update_by', 'deleted_by', 'date'];
    public $custom_cols = ['id', 'emp_id', 'type_id', 'amount', 'user_comment', 'document_attached', 'verified_level', 'hard_copy_attached', 'cosharing', 'cosharing_count', 'created_by', 'update_by', 'deleted_by', 'date'];

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

        $this->listing_cols = ['id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];


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
    public function accountApprovalReimbursement($account = 1) {
        $department = Department::department();

        if ($department == 'Account') {
            $role = Employee::employeeRole();

            if ($role == 'superAdmin' || $role == 'manager' || $role == 'lead') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_status'];
                $where = '';
                $employees = DB::table('reimbursement_forms')
                        ->select([DB::raw('distinct(reimbursement_forms.emp_id)'), DB::raw('employees.name AS employee_name')])
                        ->where('employees.id', '!=', Auth::user()->context_id)
                        ->leftJoin('employees', 'reimbursement_forms.emp_id', '=', 'employees.id')
                        ->whereNull('employees.deleted_at');
                if ($where != '') {
                    $employees = $employees->whereRaw($where);
                }
                $employees = $employees->get();
                // echo "<pre>"; print_r($employees[0]->employee_name);die;
                $account = $_GET['account'];
                //  $teamMember = $_GET['teamMember'];

                return view('la.reimbursement_forms.accountApprovalReimbursement', [
                    'show_actions' => $this->show_action, 'listing_cols' => $this->custom_cols, 'employees' => $employees, 'module' => $module, 'account' => $account, 'teamMember' => false,
                ]);
            } else {
                return redirect()->back();
            }
        }
    }

    public function teamMemberReimbursement() {
        $department = Department::department();
        if ($department == 'Account') {
            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $role = Employee::employeeRole();
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];
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
                //     echo "<pre>"; print_r($employees);die;
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
        } elseif ($department == 'BusinessAnalysis') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');

                $role = Employee::employeeRole();

                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];
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
                //     echo "<pre>"; print_r($employees);die;
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
        } elseif ($department == 'Development') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $role = Employee::employeeRole();
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];
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
                //     echo "<pre>"; print_r($employees);die;
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
        } elseif ($department == 'QualityAnalysis') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $role = Employee::employeeRole();
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];
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
                //     echo "<pre>"; print_r($employees);die;
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
    }

    public function create() {
        $employeename = DB::table('employees')
                ->whereNull('deleted_at')
                ->get();
        $reimbursement_types = DB::table('reimbursement_types')
                ->where('status', '=', 1)
                ->whereNull('deleted_at')
                ->get();
        $manager = Employee::getManagerDetails(Auth::user()->context_id);
        return view('la.reimbursement_forms.add', [
            'reimbursement_types' => $reimbursement_types, 'employeename' => $employeename, 'manager' => ucwords($manager->name),
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
            //  'cosharing' => 'required',
            'user_comment' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'user_comment' => 'required'
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
            if (isset($insert_data['cosharing'])) {
                $insert_data['cosharing'] = implode("|", $insert_data['cosharing']);
            } else {
                $insert_data['cosharing'] = '';
            }
            unset($insert_data['document_attached_hidden']);
            unset($insert_data['hard_copy_accepted_hidden']);
            unset($insert_data['name']);
            $insert_row = Reimbursement_Form::create($insert_data);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != '') {
                    $images[$i]->move(storage_path('/uploads'), $images[$i]->getClientOriginalName());
                    Reimbursement_Document::insert([
                        'request_id' => $insert_row->id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }
            //first_approver
            $data = $request->session()->all();
            // echo "<pre>"; print_r($data);die;
            if ($insert_row->verified_approval > 1) {
                Reimbursement_Approval::insert([
                    'form_id' => $insert_row->id,
                    'approved_by' => $data['employee_details']['first_approver'],
                    'level' => 1
                ]);
            }
            if ($insert_row->verified_approval > 2) {
                Reimbursement_Approval::insert([
                    'form_id' => $insert_row->id,
                    'approved_by' => $data['employee_details']['second_approver'],
                    'level' => 2
                ]);
            }
            Reimbursement_Approval::insert([
                'form_id' => $insert_row->id,
                'approved_by' => $data['employee_details']['second_approver'],
                'level' => $insert_row->verified_approval
            ]);

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
    public function show(Request $request, $id, $teamMember = 1, $account = 1) {
        if (Module::hasAccess("Reimbursement_Forms", "view")) {
            $reimbursement_form = Reimbursement_Form::find($id);
            $reimbursement_form['Date'] = date('d M Y', strtotime($reimbursement_form['Date']));
            if (isset($reimbursement_form->id)) {
                $cosharing = $reimbursement_form['cosharing'];
                $reimbursement_form['cosharing'] = explode('|', $cosharing);
                $module = Module::get('Reimbursement_Forms');
                $module->row = $reimbursement_form;
                $employeename = DB::table('employees')
                        ->whereNull('deleted_at')
                        ->get();
                $teamMember = $_GET['teamMember'];
                $data = $request->session()->all();
                $reimbursement_level = DB::table('reimbursement_approval')
                        ->select([DB::raw('reimbursement_forms.* , reimbursement_approval.*')])
                      ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_approval.form_id')
                        ->whereRaw('reimbursement_forms.verified_level+1 = reimbursement_approval.level')
                        ->where('reimbursement_approval.form_id', '=', $id)
                         ->where('reimbursement_approval.approved_by', '=', Auth::user()->context_id)
                        ->first();

//  echo "<pre>"; print_r($reimbursement_level);die;
                return view('la.reimbursement_forms.show', [
                            'module' => $module,
                            'employeename' => $employeename,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'teamMember' => $teamMember,
                            'reimbursement_level' => $reimbursement_level,
                            'account' => $account,
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
                $cosharing = $reimbursement_form['cosharing'];
                $reimbursement_form['cosharing'] = explode('|', $cosharing);
                if ($reimbursement_form->verified_level == 0) {
                    $module = Module::get('Reimbursement_Forms');
                    $module->row = $reimbursement_form;
                    $reimb_types = DB::table('reimbursement_types')
                            ->where('status', '=', 1)
                            ->whereNull('deleted_at')
                            ->get();
                    $images = DB::table('reimbursement_documents')
                            ->select([DB::raw('reimbursement_forms.* , reimbursement_documents.*')])
                            ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_documents.request_id')
                            ->where('reimbursement_documents.request_id', '=', $id)
                            ->whereNull('reimbursement_documents.deleted_at')
                            ->whereNull('reimbursement_forms.deleted_at')
                            ->get();
                    $employeename = DB::table('employees')
                            ->whereNull('deleted_at')
                            ->get();
                    $manager = Employee::getManagerDetails(Auth::user()->context_id);
                    //   echo "<pre>"; print_r($reimbursement_form);die;
                    return view('la.reimbursement_forms.edit', [
                                'module' => $module,
                                'reimb_types' => $reimb_types,
                                'images' => $images,
                                'employeename' => $employeename,
                                'view_col' => $this->view_col,
                                'manager' => ucwords($manager->name),
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
        $this->validate(request(), [
            'user_comment' => 'required',
            'document_attached' => 'required',
            'date' => 'required|date',
            //     'cosharing' => 'required',
            'amount' => 'required',
            'user_comment' => 'required'
        ]);
        if (Module::hasAccess("Reimbursement_Forms", "edit")) {
            $rules = Module::validateRules("Reimbursement_Forms", $request, true);
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput(); ;
            }
            $update_data = $request->all();
            $update_data['emp_id'] = Auth::user()->context_id;
            $update_data['date'] = date('Y-m-d', strtotime($request->date));
            if (isset($update_data['cosharing'])) {
                $update_data['cosharing'] = implode("|", $update_data['cosharing']);
            } else {
                $update_data['cosharing'] = '';
            }
//             $update_data['cosharing'] = implode("|", $update_data['cosharing']);
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
                        'request_id' => $id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }
            //    echo "<pre>"; print_r($update_data);die;
            //first_approver
            $data = $request->session()->all();
            // echo "<pre>"; print_r($data);die;
            if ($update_data['verified_approval'] > 1) {
                Reimbursement_Approval::insert([
                    'form_id' => $insert_id,
                    'approved_by' => $data['employee_details']['first_approver'],
                    'level' => 1
                ]);
            }
            if ($update_data['verified_approval'] > 2) {
                Reimbursement_Approval::insert([
                    'form_id' => $insert_id,
                    'approved_by' => $data['employee_details']['second_approver'],
                    'level' => 2
                ]);
            }
            Reimbursement_Approval::insert([
                'form_id' => $insert_id,
                'approved_by' => $data['employee_details']['second_approver'],
                'level' => 3
            ]);
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
            $employee = ' reimbursement_forms.emp_id = "' . $request->employee_search . '"';
        }
        if ($request->teamMember && $request->account == 0) {
            $this->custom_cols = [($request->teamMember) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), 'cosharing_count', 'user_comment', DB::raw("(case when (verified_level = 3 ) THEN 'Rejected' 
                        when(verified_level =1) THEN 'Approved' 
                        ELSE 'Pending'  end) AS verified_level"), 'hard_copy_attached', 'approved_by', 'rejected_by',
                (!$request->teamMember) ? 'emp_id' : 'id'
            ];
        } else if (!$request->teamMember && $request->account == 0) {
            $this->custom_cols = [($request->teamMember) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), 'cosharing_count', 'user_comment', DB::raw("(case when (verified_level = 3 ) THEN 'Rejected ' 
                        when(verified_level =1) THEN 'Approved' 
                        ELSE 'Pending'  end) AS verified_level"), 'hard_copy_attached', 'approved_by', 'rejected_by',
                (!$request->teamMember) ? 'emp_id' : 'id'
            ];
        } else if ($request->teamMember || $request->account == 1) {
            $this->custom_cols = [($request->account == 1) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), 'cosharing_count', 'user_comment', DB::raw("(case when (verified_level = 3 ) THEN 'Rejected' 
                        when(verified_level =1) THEN 'Approved' 
                        ELSE 'Pending'  end) AS verified_level"), 'hard_copy_attached', 'approved_by', 'rejected_by',
                (!$request->account == 1) ? 'emp_id' : 'id'
            ];
        }
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
        } else if ($request->account == 1) {

            $department = Department::department();
            if ($department == 'Account') {
                $role = Employee::employeeRole();
                $where = '';
                $where = 'emp_id != ' . Auth::user()->context_id;
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

        $col_arr = [($request->teamMember || $request->account) ? 'emp_id' : 'id', 'date', 'type_id', 'amount', 'document_attached', 'cosharing_count', 'user_comment', 'verified_level'];

        $fields_popup = ModuleFields::getModuleFields('reimbursement_forms');
        foreach ($fields_popup as $column => $val) {
            if (!in_array($column, $col_arr)) {
                unset($fields_popup[$column]);
            }
        }
        for ($i = 0; $i < count($data->data); $i++) {
            $verified_level = $data->data[$i][7];
            $approved_by = $data->data[$i][10];
            $rejected_by = $data->data[$i][11];
            for ($j = 0; $j < count($col_arr); $j++) {
                $col = $col_arr[$j];
                //action buttons
                if ($j == 0) { //render only when first column is being checked
                    $output = '';
                    if ($this->show_action && !$request->teamMember && !$request->account) {
                        if ($verified_level == 'Pending') {
                            if (Module::hasAccess("Reimbursement_Forms", "edit")) {
                                $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0] . '/edit/') . '"class="btn btn-warning btn-xs" style="display: inline;padding: 2px 5px;margin-right: 3px;"><i class="fa fa-edit"></i></a>';
                            }
                            if (Module::hasAccess("Reimbursement_Forms", "delete")) {
                                $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.reimbursement_forms.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline-block']);
                                $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                                $output .= Form::close();
                            }
                        } else {
                            $output .= 'Action Taken';
                        }
                    } else if ($request->teamMember) {
                        $output = '';
                        if ($role == 'lead') {
                            if ($verified_level == 'Pending' || $verified_level == 'Rejected' || $verified_level == 'Approved') {

                                $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][(count($data->data[$i]) - 1)]) . '?teamMember=1&account=1"><i class="fa fa-eye"></i></a>';
                            }
                        } else if
                        ($role == 'manager') {
                            if ($verified_level == 'Pending' || $verified_level == 'Rejected' || $verified_level == 'Approved') {
                                $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][(count($data->data[$i]) - 1)]) . '?teamMember=1&account=1"><i class="fa fa-eye"></i></a>';
                            }
                        }
                    } else if ($request->account == 1) {
                        $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][(count($data->data[$i]) - 1)]) . '?teamMember=1&account=1"><i class="fa fa-eye"></i></a>';
                    }

                    $data->data[$i][count($col_arr)] = (string) $output;
                }

                //link for viewable column
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0]) . '?teamMember=0&account=1">' . $data->data[$i][$j] . '</a>';
                }
                if ($col == 'user_comment') {
                    $data->data[$i][$j] = '<span class="tooltips" title="' . $data->data[$i][$j] . '">' . ((strlen($data->data[$i][$j]) > 20) ? substr($data->data[$i][$j], 0, 20) . '...' : $data->data[$i][$j]) . '</span>';
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
        //    $update_approval = ['status' => $_GET['approved']];
        if ($_GET['approved']) {
            $update_field['approved_by'] = Auth::user()->context_id;
        } else {
            $update_field['rejected_by'] = Auth::user()->context_id;
        }

        if ($request->approved == "1") {
            $update_field['verified_level'] = 1;
            //       $update_approval['status'] = 1;
        } elseif ($request->reject == "0") {
            $update_field['verified_level'] = 2;
            //      $update_approval['status'] = 2;
        } else {
            $update_field['verified_level'] = 3;
            //      $update_approval['status'] = 0;
        }
        //  echo "<pre>"; print_r($update_field);die;

        $insert_id = Reimbursement_Form::find($request->id)->update($update_field);
        //   $insert_id = Reimbursement_Approval::find($request->id)->update($update_approval);

        return redirect()->route(config('laraadmin.adminRoute') . '.reimbursement_forms.index');
        // return json_encode($result);
    }

    public function ajaxApprovedReimbursement() {
        $update_field = ['status' => $_GET['approved']];
        $update = Reimbursement_Approval::where('form_id', '=', $_GET['form_id'])
                ->where('approved_by', '=', Auth::user()->context_id)
                ->update($update_field);
    }

}
