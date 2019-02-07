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

        $this->listing_cols = ['id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
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
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'verified_status'];
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
                $account = $_GET['account'];
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
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
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
        } elseif ($department == 'BusinessAnalysis') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');

                $role = Employee::employeeRole();

                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
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
        } elseif ($department == 'Development') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $role = Employee::employeeRole();
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
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
        } elseif ($department == 'QualityAnalysis') {

            $role = Employee::employeeRole();
            if ($role != 'engineer') {
                session(['task_removed' => '']);
                $module = Module::get('Reimbursement_Forms');
                $role = Employee::employeeRole();
                $this->custom_cols = ['emp_id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
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
    }

    public function create() {
        $employeename = DB::table('employees')
                ->where('id', '!=', Auth::user()->context_id)
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
            unset($insert_data['hard_copy_attached_hidden']);
            unset($insert_data['name']);
            $insert_row = Reimbursement_Form::create($insert_data);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != '') {
                    $images[$i]->move(public_path('/uploads'), $images[$i]->getClientOriginalName());
                    Reimbursement_Document::insert([
                        'request_id' => $insert_row->id,
                        'created_by' => Auth::user()->context_id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }
            //total_approve_level
            //first_approver
            $data = $request->session()->all();
            if ($insert_row->verified_approval > 2) {
                if ($insert_row->verified_approval > 1) {
                    Reimbursement_Approval::insert([
                        'request_id' => $insert_row->id,
                        'action_taken_by' => $data['employee_details']['first_approver'],
                        'level' => 1
                    ]);
                }
                if ($insert_row->verified_approval > 2) {
                    Reimbursement_Approval::insert([
                        'request_id' => $insert_row->id,
                        'action_taken_by' => $data['employee_details']['second_approver'],
                        'level' => 2
                    ]);
                }
                Reimbursement_Approval::insert([
                    'request_id' => $insert_row->id,
                    'action_taken_by' => 34,
                    'level' => 3
                ]);
            } else {
                if ($insert_row->verified_approval > 1) {
                    Reimbursement_Approval::insert([
                        'request_id' => $insert_row->id,
                        'action_taken_by' => $data['employee_details']['first_approver'],
                        'level' => 1
                    ]);
                }
                Reimbursement_Approval::insert([
                    'request_id' => $insert_row->id,
                    'action_taken_by' => 34,
                    'level' => 2
                ]);
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
                $account = $_GET['account'];
                $data = $request->session()->all();
                $reimbursement_level = DB::table('reimbursement_approval')
                        ->select([DB::raw('reimbursement_forms.* , reimbursement_approval.*')])
                        ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_approval.request_id')
                        ->where('reimbursement_approval.request_id', '=', $id)
                        ->where('reimbursement_approval.action_taken_by', '=', Auth::user()->context_id)
                        ->first();

                $reimbursement_status = DB::table('reimbursement_approval')
                        ->select([DB::raw('reimbursement_approval.*')])
                        ->where('reimbursement_approval.request_id', '=', $id)
                        ->where('reimbursement_approval.status', '=', 2)
                        ->get();
                $reimb_types = DB::table('reimbursement_types')
                        ->where('status', '=', 1)
                        ->whereNull('deleted_at')
                        ->get();
                $reimbursement_total_level = DB::table('reimbursement_forms')
                        ->select([DB::raw('reimbursement_forms.*')])
                        ->where('reimbursement_forms.id', '=', $id)
                        ->first();
                $join_approve_form = DB::table('reimbursement_approval')
                        ->select([DB::raw('reimbursement_forms.* , reimbursement_approval.*')])
                        ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_approval.request_id')
                        ->where('reimbursement_approval.request_id', '=', $id)
//                         ->where('reimbursement_approval.id' )
//                        ->where('updated_at')
                          
                       ->get();
                
                $images = DB::table('reimbursement_documents')
                        ->select([DB::raw('reimbursement_forms.* , reimbursement_documents.*')])
                        ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_documents.request_id')
                        ->where('reimbursement_documents.request_id', '=', $id)
                        ->whereNull('reimbursement_documents.deleted_at')
                        ->whereNull('reimbursement_forms.deleted_at')
                        ->get();
                $updateby = DB::table('reimbursement_approval')
                        ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_approval.request_id')
                        ->where('reimbursement_approval.request_id', '=', $id)
                        ->where('status', '<>', 0)
                        ->groupBy('action_taken_by')
                        ->get(['reimbursement_approval.*', 'action_taken_by', DB::raw('MAX(level) as level_new')]);
             
//echo "<pre>"; print_r($join_approve_form);die;
                return view('la.reimbursement_forms.show', [
                   

                            'join_approve_form' => $join_approve_form,
                            'images' => $images,
                            'updateby' => $updateby,
                            'reimbursement_total_level' => $reimbursement_total_level,
                            'reimb_types' => $reimb_types,
                            'module' => $module,
                            'employeename' => $employeename,
                            'reimbursement_status' => $reimbursement_status,
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
                    ->select(['id', 'emp_id', 'amount', 'user_comment', 'type_id', 'document_attached', 'cosharing', 'cosharing_count', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'verified_level', 'verified_approval'])
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
                            ->where('id', '!=', Auth::user()->context_id)
                            ->whereNull('deleted_at')
                            ->get();
                    $manager = Employee::getManagerDetails(Auth::user()->context_id);

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
            unset($update_data['hard_copy_attached_hidden']);
            unset($update_data['name']);
            $image = $update_data['isImages'];
            if ($image == "1") {
                $update_data['document_attached'] = 1;
            }
            unset($update_data['isImages']);
            $insert_id = Reimbursement_Form::find($id)->update($update_data);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != '') {
                    $images[$i]->move(public_path('/uploads'), $images[$i]->getClientOriginalName());
                    Reimbursement_Document::insert([
                        'request_id' => $id,
                        'update_by' => Auth::user()->context_id,
                        'name' => $images[$i]->getClientOriginalName()
                    ]);
                }
            }
            $approval = DB::table('reimbursement_approval')
                    ->select([DB::raw('reimbursement_forms.* , reimbursement_approval.*')])
                    ->leftJoin('reimbursement_forms', 'reimbursement_forms.id', '=', 'reimbursement_approval.request_id')
                    ->where('reimbursement_approval.request_id', '=', $id)
                    ->whereNull('reimbursement_approval.deleted_at')
                    ->delete();
            $data = $request->session()->all();
            if ($update_data['verified_approval'] > 2) {
                if ($update_data['verified_approval'] > 1) {
                    Reimbursement_Approval::insert([
                        'request_id' => $id,
                        'action_taken_by' => $data['employee_details']['first_approver'],
                        'level' => 1
                    ]);
                }
                if ($update_data['verified_approval'] > 2) {
                    Reimbursement_Approval::insert([
                        'request_id' => $id,
                        'action_taken_by' => $data['employee_details']['second_approver'],
                        'level' => 2
                    ]);
                }
                Reimbursement_Approval::insert([
                    'request_id' => $id,
                    'action_taken_by' => $data['employee_details']['second_approver'],
                    'level' => 3
                ]);
            } else {
                if ($update_data['verified_approval'] > 1) {
                    Reimbursement_Approval::insert([
                        'request_id' => $id,
                        'action_taken_by' => $data['employee_details']['first_approver'],
                        'level' => 1
                    ]);
                }
                Reimbursement_Approval::insert([
                    'request_id' => $id,
                    'action_taken_by' => $data['employee_details']['second_approver'],
                    'level' => 2
                ]);
            }
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
            $this->custom_cols = [($request->teamMember) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), DB::raw("(case when (verified_level = 0 ) THEN 'pending' 
                      when(verified_level < verified_approval) THEN CONCAT('Action taken at level ', verified_level)
 ELSE 'Application close'  end) AS verified_level"), 'hard_copy_attached',

                (!$request->teamMember) ? 'emp_id' : 'reimbursement_forms.id'
            ];
        } else if (!$request->teamMember && $request->account == 0) {
            $this->custom_cols = [($request->teamMember) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), DB::raw("(case when (verified_level = 0 ) THEN 'pending' 
                         when(verified_level < verified_approval) THEN CONCAT('Action taken at level ', verified_level)
 ELSE 'Application close'  end) AS verified_level"), 'hard_copy_attached',

                (!$request->teamMember) ? 'emp_id' : 'reimbursement_forms.id'
            ];
        } else if ($request->teamMember || $request->account == 1) {
            $this->custom_cols = [($request->account == 1) ? 'reimbursement_forms.emp_id' : 'reimbursement_forms.id', DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as date'), 'type_id', 'amount', DB::raw("(case when (document_attached = 0) THEN 'No' ELSE 'Yes' end) as document_attached"), DB::raw("(case when (verified_level = 0 ) THEN 'pending' 
                        when(verified_level < verified_approval) THEN CONCAT('Action taken at level ', verified_level)
 ELSE 'Application close'  end) AS verified_level"), 'hard_copy_attached',

                (!$request->account == 1) ? 'emp_id' : 'reimbursement_forms.id'
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


        if ($request->teamMember) {
            $value = DB::table('reimbursement_forms')
                    ->select($this->custom_cols)
                    ->leftJoin('reimbursement_approval', 'reimbursement_forms.id', '=', 'reimbursement_approval.request_id')
                    ->where('reimbursement_approval.action_taken_by', '=', Auth::user()->context_id)
                    ->orderBy('reimbursement_forms.id', 'desc')
                    ->whereNull('reimbursement_forms.deleted_at');
        } else {
            $value = DB::table('reimbursement_forms')
                    ->select($this->custom_cols)
                    ->orderBy('id', 'desc')
                    ->whereNull('reimbursement_forms.deleted_at');
        }


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
        $col_arr = [($request->teamMember || $request->account) ? 'emp_id' : 'id', 'date', 'type_id', 'amount', 'document_attached', 'verified_level'];
        $fields_popup = ModuleFields::getModuleFields('reimbursement_forms');
         foreach ($fields_popup as $column => $val) {
            if (!in_array($column, $col_arr)) {
                unset($fields_popup[$column]);
            }
        }
        for ($i = 0; $i < count($data->data); $i++) {
            $verified_level = $data->data[$i][5];

            for ($j = 0; $j < count($col_arr); $j++) {
                $col = $col_arr[$j];
                //action buttons
                if ($j == 0) { //render only when first column is being checked
                    $output = '';
                    if ($this->show_action && !$request->teamMember && !$request->account) {
                        if ($verified_level == 'pending') {
                            if (Module::hasAccess("Reimbursement_Forms", "edit")) {
                                $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0] . '/edit/') . '"class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>';
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
                        $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][(count($data->data[$i]) - 1)]) . '?teamMember=1&account=0"><i class="fa fa-eye"></i></a>';
                    } else if ($request->account == 1) {
                        $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][(count($data->data[$i]) - 1)]) . '?teamMember=0&account=1"><i class="fa fa-eye"></i></a>';
                    }

                    $data->data[$i][count($col_arr)] = (string) $output;
                }

                //link for viewable column
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/reimbursement_forms/' . $data->data[$i][0]) . '?teamMember=0&account=0">' . $data->data[$i][$j] . '</a>';
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
        $Result = $request->all();
          if ($request->datepicker == 00 - 00 - 0000) {
            $update_level['payment_date'] = 0000 - 00 - 00;
        } else {
            $update_level['payment_date'] = date('y-m-d', strtotime($_GET['datepicker']));
        }
        $update_level['paid_amount'] = $_GET['amount'];
        $update_level['payment_mode'] = $_GET['mode'];

        if ($request->approved == "1") {
             $insert_form = Reimbursement_Form::find($Result['id'])->increment('verified_level');
           
        } else {
         $insert_form = Reimbursement_Form::find($Result['id'])->increment('verified_level');
        }
        $insert_id = Reimbursement_Form::find($Result['id'])->update($update_level);
        $update_field = ['status' => $_GET['approved']];
        if ($_GET['approved']) {
            
            $update_field['action_taken_by'] = Auth::user()->context_id;
            $update_field['update_by'] = Auth::user()->context_id;
            $update_field['comment'] = $_GET['actionReason'];
            $update_data = $request->all();
            $request_id = $update_data['id'];
            if ($request->approved == "1") {
                $update_field['status'] = 1;
            } else if ($request->reject == "2") {
                $update_field['status'] = 2;
            }

            $reimbursement_approval = DB::table('Reimbursement_Approval')
                    ->whereRaw("action_taken_by = $update_field[action_taken_by]")
                    ->whereRaw("request_id = $request_id")
                    ->get();
          $insert_id = Reimbursement_Approval::find($reimbursement_approval[0]->id)->update($update_field);
        }
    }

}
