<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Models\Employee;
use App\Models\Holidays_List;
use App\Role;
use Validator;
use Eloquent;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Auth;
use DB;

class AuthController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware($this->guestMiddleware(), ['except' => ['logout', 'getLogout']]);
    }

    public function showRegistrationForm() {
        $roleCount = Role::count();
        if ($roleCount != 0) {
            $userCount = User::count();
            if ($userCount == 0) {
                return view('auth.register');
            } else {
                return redirect('login');
            }
        } else {
            return view('errors.error', [
                'title' => 'Migration not completed',
                'message' => 'Please run command <code>php artisan db:seed</code> to generate required table data.',
            ]);
        }
    }

    public function showLoginForm() {
        $roleCount = Role::count();
        if ($roleCount != 0) {
            $userCount = User::count();
            if ($userCount == 0) {
                return redirect('register');
            } else {
                return view('auth.login');
            }
        } else {
            return view('errors.error', [
                'title' => 'Migration not completed',
                'message' => 'Please run command <code>php artisan db:seed</code> to generate required table data.',
            ]);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        // TODO: This is Not Standard. Need to find alternative
        Eloquent::unguard();

        $employee = Employee::create([
                    'name' => $data['name'],
                    'designation' => "Super Admin",
                    'mobile' => "8888888888",
                    'mobile2' => "",
                    'email' => $data['email'],
                    'gender' => 'Male',
                    'dept' => "1",
                    'city' => "Pune",
                    'address' => "Karve nagar, Pune 411030",
                    'about' => "About user / biography",
                    'date_birth' => date("Y-m-d"),
                    'date_hire' => date("Y-m-d"),
                    'date_left' => date("Y-m-d"),
                    'salary_cur' => 0,
        ]);

        $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'context_id' => $employee->id,
                    'type' => "Employee",
        ]);
        $role = Role::where('name', 'SUPER_ADMIN')->first();
        $user->attachRole($role);

        return $user;
    }

    public function login(Request $request) {
        if (Auth::attempt([
                    'email' => $request->get('email'),
                    'password' => $request->get('password')
                ])) {
            // Login success and get user's info to check
            // If soft deleted then logout the user.
            if (\auth()->user()->deleted_at != NULL) {
                return redirect('/logout');
            } else {
                $holiday_list = collect(Holidays_List::select(['day', 'occasion'])->get())->keyBy('day');
                $emp_details = Employee::find(\auth()->user()->context_id);
                $emp_details->address = urlencode($emp_details->address);
                $emp_details->available_leaves = "'" . ($emp_details->available_leaves) . "'";
                $request->session()->put('holiday_list', json_encode($holiday_list));
                $request->session()->put('role', Employee::employeeRole());
                $request->session()->put('employee_details', $emp_details);
                return redirect($this->redirectTo);
            }
        } else {
            return redirect()->back()->withErrors(['Credentials don\'t match!']);
        }
    }

}
