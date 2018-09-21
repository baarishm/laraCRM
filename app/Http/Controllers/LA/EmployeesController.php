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
use Dwij\Laraadmin\Helpers\LAHelper;
use App\User;
use App\Models\Employee;
use App\Role;
use Mail;
use Log;
use Dwij\Laraadmin\Models\LAConfigs;
use Session;

class EmployeesController extends Controller {

    public $show_action = true;
    public $view_col = 'employees.name as name';
    public $listing_cols = ['id', 'name', 'gender', 'mobile', 'mobile2', 'email', 'date_birth', 'city', 'address', 'about', 'first_approver', 'second_approver', 'dept', 'date_hire'];
    public $index_listing_cols = ['employees.id as id', 'employees.name as name', 'roles.display_name as Role', 'gender', 'mobile', 'employees.email as email', 'date_birth', 'first_approver', 'second_approver', 'date_hire'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Employees', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Employees', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Employees');

        if (Module::hasAccess($module->id) && (Session::get('role') == 'superAdmin')) {
            return View('la.employees.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => ['id', 'name', 'role', 'gender', 'mobile', 'email', 'date_birth', 'first_approver', 'second_approver', 'date_hire'],
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Employees');
        if (Module::hasAccess("Employees", "create")) {
            return view('la.employees.add', [
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created employee in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Employees", "create")) {

            $rules = Module::validateRules("Employees", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $insert_data = $request->all();
            $insert_data['date_hire'] = date('Y-m-d', strtotime($request->date_hire));
            $insert_data['date_birth'] = date('Y-m-d', strtotime($request->date_birth));
            $insert_data['is_confirmed'] = ($request->is_confirmed) ? 1 : 0;
            $insert_data['name'] = ucwords($request->name);
            unset($insert_data['role']);
            unset($insert_data['is_confirmed_hidden']);

            $row = Employee::where('email', $request->email)
                    ->withTrashed()
                    ->get();

            $Exists = $row->count();

            if ($Exists > 0) {
                return redirect()->route(config('laraadmin.adminRoute') . '.employees.create')->withErrors(['message' => 'Employee with this email ID already exists. Please check or ask admin to revoke it.']);
            }

            // Create Employee
            $employee = Employee::create($insert_data);
            // Create User
            $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt('123456'),
                        'context_id' => $employee->id,
                        'type' => "Employee",
            ]);

            // update user role
            $user->detachRoles();
            $role = Role::find($request->role);
            $user->attachRole($role);

            Log::info("User created: username: " . $user->email . " Password: 123456");

            return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Employees", "view") && ((Session::get('role') == 'superAdmin') || (Session::get('role') != 'superAdmin' && $id == Auth::user()->context_id))) {
            $employee = Employee::find($id);
            if (isset($employee->id)) {
                $module = Module::get('Employees');
                $module->row = $employee;

                // Get User Table Information
                $user = User::where('context_id', '=', $id)->firstOrFail();

                return view('la.employees.show', [
                            'user' => $user,
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('employee', $employee);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("employee"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Employees", "edit") && ((Session::get('role') == 'superAdmin') || (Session::get('role') != 'superAdmin' && $id == Auth::user()->context_id))) {
            $employee = Employee::find($id);
            if (isset($employee->id)) {
                $module = Module::get('Employees');

                $module->row = $employee;

                // Get User Table Information
                $user = User::where('context_id', '=', $id)->firstOrFail();

                return view('la.employees.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'user' => $user,
                        ])->with('employee', $employee);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucwords("employee"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Employees", "edit") && ((Session::get('role') == 'superAdmin') || (Session::get('role') != 'superAdmin' && $id == Auth::user()->context_id))) {

            $rules = Module::validateRules("Employees", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $update_data = $request->all();
            $update_data['date_hire'] = date('Y-m-d', strtotime($request->date_hire));
            $update_data['date_birth'] = date('Y-m-d', strtotime($request->date_birth));
            if ((Session::get('role') == 'superAdmin')) {
                $update_data['is_confirmed'] = ($request->is_confirmed) ? 1 : 0;
            } else {
                unset($update_data['is_confirmed']);
                unset($update_data['emp_code']);
                unset($update_data['first_approver']);
                unset($update_data['second_approver']);
            }
            $update_data['name'] = ucwords($request->name);
            unset($update_data['role']);
            unset($update_data['is_confirmed_hidden']);

            $row = Employee::where('email', $request->email)
                    ->withTrashed()
                    ->pluck('id');

            $Exists = $row->count();
            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect()->route(config('laraadmin.adminRoute') . '.employees.edit', ['id' => $id])->withErrors(['message' => 'Employee with this email ID already exists. Please check or ask admin to revoke it.']);
            }

            $insert_id = Employee::find($id)->update($update_data);

            // Update User
            $user = User::where('context_id', $id)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            // update user role
            if ($request->role != '') {
                $user->detachRoles();
                $role = Role::find($request->role);
                $user->attachRole($role);
            }
            if (Session::get('role') == 'superAdmin') {
                return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
            } else if (Session::get('role') != 'superAdmin' && $id == Auth::user()->context_id) {
                return redirect(config('laraadmin.adminRoute') . '/employees/' . Auth::user()->context_id);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Employees", "delete")) {
            Employee::find($id)->delete();
            User::where('context_id', '=', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
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
        $values = DB::table('employees')
                ->leftJoin('users', 'users.context_id', '=', 'employees.id')
                ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->select(['employees.id as id', 'employees.name as name', 'roles.display_name as Role', 'gender', 'mobile', 'employees.email as email', DB::raw('DATE_FORMAT(date_birth,\'%d %b %Y\') as date_birth'), 'first_approver', 'second_approver', DB::raw('DATE_FORMAT(date_hire,\'%d %b %Y\') as date_hire')])
                ->whereNull('employees.deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Employees');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->index_listing_cols); $j++) {
                $col = $this->index_listing_cols[$j];
                if (array_key_exists($col, $fields_popup) && $fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/employees/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action && $data->data[$i][2] != 'Super Admin') {
                $output = '';
                if (Module::hasAccess("Employees", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/employees/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Employees", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.employees.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string) $output;
            } else {
                $data->data[$i][] = '';
            }
        }
        $out->setData($data);
        return $out;
    }

    /**
     * Change Employee Password
     *
     * @return
     */
    public function change_password($id, Request $request) {

        $validator = Validator::make($request->all(), [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|min:6|same:password'
        ]);

        if ($validator->fails()) {
            return \Redirect::to(config('laraadmin.adminRoute') . '/employees/' . $id.'#tab-account-settings')->withErrors($validator);
        }

        $employee = Employee::find($id);
        $user = User::where("context_id", $employee->id)->where('type', 'Employee')->first();
        $user->password = bcrypt($request->password);
        $user->save();

        \Session::flash('success_message', 'Password is successfully changed');

        // Send mail to User his new Password
        if (env('MAIL_USERNAME') != null && env('MAIL_USERNAME') != "null" && env('MAIL_USERNAME') != "") {
            // Send mail to User his new Password
            Mail::send('emails.send_login_cred_change', ['user' => $user, 'password' => $request->password], function ($m) use ($user) {
                $m->to($user->email, $user->name)->subject('PlusMinus - Login Credentials changed');
            });
        } else {
            Log::info("User change_password: username: " . $user->email . " Password: " . $request->password);
        }

        return redirect(config('laraadmin.adminRoute') . '/employees/' . $id . '#tab-account-settings');
    }

}
