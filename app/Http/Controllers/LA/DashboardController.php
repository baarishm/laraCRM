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
            $where = 'submitor_id = ' . Auth::user()->context_id;
            $where = '';
            $role = Employee::employeeRole();
            if ($role == 'superAdmin') {
                  //no condition to be applied
                  $leaveMaster = DB::table('leavemaster')
                          ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                          ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                          ->groupby('EmpId')
                          ->where('FromDate', '<=', date('Y-m-d'))
                          ->where('ToDate', '>=', date('Y-m-d'))
                          ->where('Approved', '=', 1)
                          ->get();

                  $employee = DB::table('employees')
                          ->whereNull('deleted_at')
                          ->get();
                  $ganitemp = DB::table('timesheets')
                          ->select([DB::raw('employees.name AS employees_name,timesheets.*'), DB::raw('employees.emp_code AS emp_code')])
                          ->leftJoin('employees', 'employees.id', '=', 'timesheets.submitor_id')
                          ->where('date', '=', date('Y-m-d'))
                          ->groupBy(['timesheets.submitor_id'])
                          ->get();
                  $projectname = DB::table('projects')
                          ->select('name')
                          ->distinct()
                          ->whereNull('deleted_at')
                          ->get();
                  $data = array('employeelist' => $employee, 'leaveMaster' => $leaveMaster, 'ganitemp' => $ganitemp, 'projectname' => $projectname);
            } else if ($role == 'manager') {
                  $people_under_manager = Employee::getEngineersUnder('Manager');
                  if ($people_under_manager != '')
                        $where = 'submitor_id IN (' . $people_under_manager . ')';

                  $empdetail = Employee::where('id', Auth::user()->context_id)
                          ->first();
                  $timesheet = DB::table('timesheets')
                          ->where('date', '=', date('Y-m-d'))
                          ->where('submitor_id', '=', Auth::user()->context_id)
                          ->count();

                  if ($people_under_manager != '')
                        $where = 'submitor_id IN (' . $people_under_manager . ')';
                  $leaveMaster = DB::table('leavemaster')
                          ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                          ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                          ->groupBy('EmpId')
                          ->where(function($query) {
                                $query->where('first_approver', '=', Auth::user()->context_id)
                                ->orwhere('second_approver', '=', Auth::user()->context_id);
                          })
                          ->where('FromDate', '<=', date('Y-m-d'))
                          ->where('ToDate', '>=', date('Y-m-d'))
                          ->where('Approved', '=', 1)
                          ->get();
                  $where = '';
                  if ($people_under_manager != '')
                        $where = 'submitor_id IN (' . $people_under_manager . ')';
                  $teamMember = DB::table('employees')
                          ->where(function($query) {
                                $query->where('first_approver', '=', Auth::user()->context_id)
                                ->orwhere('second_approver', '=', Auth::user()->context_id);
                          })
                          ->whereNull('employees.deleted_at')
                          ->get();
                  $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'leaveMaster' => $leaveMaster, 'teammumber' => $teamMember];
            } else if ($role == 'lead') {
                  $people_under_lead = Employee::getEngineersUnder('Lead');
                  $empdetail = Employee::where('id', Auth::user()->context_id)
                          ->first();
                  $timesheet = DB::table('timesheets')
                          ->where('date', '=', date('Y-m-d'))
                          ->where('submitor_id', '=', Auth::user()->context_id)
                          ->count();
                  if ($people_under_lead != '')
                        $where = 'submitor_id IN (' . $people_under_lead . ')';
                  $leaveMaster = DB::table('leavemaster')
                          ->select([DB::raw('employees.name AS employees_name,leavemaster.*'), DB::raw('employees.emp_code AS emp_code')])
                          ->leftJoin('employees', 'employees.id', '=', 'leavemaster.EmpId')
                          ->where('first_approver', '=', Auth::user()->context_id)
                          ->where('FromDate', '<=', date('Y-m-d'))
                          ->where('ToDate', '>=', date('Y-m-d'))
                          ->where('Approved', '=', 1)
                          ->get();
                  $teammumber = DB::table('employees')
                          ->where('first_approver', '=', Auth::user()->context_id)
                          ->get();
                  $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'leaveMaster' => $leaveMaster, 'teammumber' => $teammumber];
            } else if ($role == 'engineer') {
                  $this->show_action = true;
                  $where = 'submitor_id = ' . Auth::user()->context_id;
                  $empdetail = Employee::where('id', Auth::user()->context_id)
                          ->first();
                  $timesheet = DB::table('timesheets')
                          ->where('date', '=', date('Y-m-d'))
                          ->where('submitor_id', '=', Auth::user()->context_id)
                          ->count();

                  $Workingprojectname = DB::table('resource_allocations')
                          ->select([DB::raw('DISTINCT(projects.name) AS project_name,resource_allocations.*')])
                          ->leftJoin('projects', 'projects.id', '=', 'resource_allocations.project_id')
                          ->where('resource_allocations.start_date', '<=', date('Y-m-d'))
                          ->where('resource_allocations.end_date', '>=', date('Y-m-d'))
                          ->where('employee_id', '=', Auth::user()->context_id)
                          ->groupBy(['resource_allocations.employee_id', 'resource_allocations.project_id'])
                          ->distinct()
                          ->get();
                  count($Workingprojectname);
                  $holidayname = DB::table('holidays_lists')
                          ->whereRaw('((MONTH(day)) = (MONTH(CURRENT_DATE())))')
                          ->where('day', '>=', date('Y-m-d'))
                          ->get();
                  count($holidayname);
                  $data = ['empdetail' => $empdetail, 'timesheet' => $timesheet, 'Workingprojectname' => $Workingprojectname, 'holidayname' => $holidayname];
            }
            return view('la.dashboard', $data);
      }

}
