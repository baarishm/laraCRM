<?php

/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\LeaveMaster;
use Illuminate\Http\Request;
use Auth;
use DB;
use Dwij\Laraadmin\Models\LAConfigs;

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index() {
        $data = [];
        $role = \Session::get('role');

        if ($role == 'engineer') {
            $empdetail = Employee::where('id', Auth::user()->context_id)
                    ->first();
            $timesheet = DB::table('timesheets')
                    ->where('date', '=', date('Y-m-d'))
                    ->where('submitor_id', '=', Auth::user()->context_id)
                    ->count();

            $Workingprojectname = DB::table('resource_allocations')
                    ->select([DB::raw('projects.name AS project_name,resource_allocations.*')])
                    ->leftJoin('projects', 'projects.id', '=', 'resource_allocations.project_id')
                    ->where('employee_id', '=', Auth::user()->context_id)
                    ->distinct()
                    ->get();
            count($Workingprojectname);

            $holidayname = DB::table('holidays_lists')
                    
                    ->whereRaw('((MONTH(day)) = (MONTH(CURRENT_DATE())))')
                    ->get();
            count($holidayname);
            $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'Workingprojectname' => $Workingprojectname, 'holidayname' => $holidayname];
        } else if ($role == 'manager' || $role == 'lead') {
            $empdetail = Employee::where('id', Auth::user()->context_id)
                    ->first();
            $timesheet = DB::table('timesheets')
                    ->where('date', '=', date('Y-m-d'))
                    ->where('submitor_id', '=', Auth::user()->context_id)
                    ->count();
            if ($role == 'manager') {
                $leaveMaster = DB::table('leavemaster')
                        ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                        ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                        ->where(function($query) {
                            $query->where('first_approver', '=', Auth::user()->context_id)
                            ->orwhere('second_approver', '=', Auth::user()->context_id);
                        })
                        ->where('FromDate', '<=', date('Y-m-d'))
                        ->where('ToDate', '>=', date('Y-m-d'))
                        ->where('Approved', '=', 1)
                        ->get();
                count($leaveMaster);

                $teammumber = DB::table('leavemaster')
                        ->select([DB::raw('employees.name AS employees_name,employees.*')])
                        ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')->distinct()
                        ->where(function($query) {
                            $query->where('first_approver', '=', Auth::user()->context_id)
                            ->orwhere('second_approver', '=', Auth::user()->context_id);
                        })
                        ->get();
                count($teammumber);
                $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'leaveMaster' => $leaveMaster, 'teammumber' => $teammumber];
            } else {
                $leaveMaster = DB::table('leavemaster')
                        ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                        ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                        ->where('first_approver', '=', Auth::user()->context_id)
                        ->where('FromDate', '<=', date('Y-m-d'))
                        ->where('ToDate', '>=', date('Y-m-d'))
                        ->where('Approved', '=', 1)
                        ->get();
                count($leaveMaster);

                $teammumber = DB::table('leavemaster')
                        ->select([DB::raw('employees.name AS employees_name,employees.*')])
                        ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')->distinct()
                        ->where('first_approver', '=', Auth::user()->context_id)
                        ->get();
                count($teammumber);

                $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'leaveMaster' => $leaveMaster, 'teammumber' => $teammumber];
            }
        } else {
            $leaveMaster = DB::table('leavemaster')
                    ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                    ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                    ->where('FromDate', '<=', date('Y-m-d'))
                    ->where('ToDate', '>=', date('Y-m-d'))
                    ->where('Approved', '=', 1)
                    ->get();
            count($leaveMaster);
            $employee = DB::table('employees')
                    ->get();
            count($employee);

            $ganitemp = DB::table('timesheets')
                    ->select([DB::raw('employees.name AS employees_name,timesheets.*'), DB::raw('employees.emp_code AS emp_code')])
                    ->leftJoin('employees', 'employees.id', '=', 'timesheets.submitor_id')
                    ->where('date', '=', date('Y-m-d'))
                    ->get();
            count($ganitemp);

            $projectname = DB::table('projects')
                    ->select('name')
                    ->distinct()
                    ->get();
            count($projectname);


            $data = array('employeelist' => $employee, 'leaveMaster' => $leaveMaster, 'ganitemp' => $ganitemp, 'projectname' => $projectname);
        }
//        echo "<pre>"; print_r($data); die;
        return view('la.dashboard', $data);
    }

}
