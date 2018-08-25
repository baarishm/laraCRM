<?php

/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;

class Timesheet extends Model {

    use SoftDeletes;

    protected $table = 'timesheets';
    protected $hidden = [
    ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    /**
     * Used to get list of 
     * Leads
     * Managers
     * Tasks
     * Entries not yet submitted
     */
    public static function leads_managers_tasks_notSubmitted() {
        $leads = DB::table('leads')
                ->select([DB::raw('users.name as lead_name'), DB::raw('leads.id AS lead_id'), DB::raw('users.email AS lead_email')])
                ->leftJoin('users', 'users.id', '=', 'leads.employee_id')
                ->get();

        $managers = DB::table('managers')
                ->select([DB::raw('users.name as manager_name'), DB::raw('managers.id AS manager_id'), DB::raw('users.email AS manager_email')])
                ->leftJoin('users', 'users.id', '=', 'managers.employee_id')
                ->get();

        $role_id = DB::table('employees')->whereRaw('id = "' . Auth::user()->context_id . '"')->first();
        $tasks = DB::table('task_roles')
                ->select(['name', 'task_id'])
                ->leftJoin('tasks', 'tasks.id', '=', 'task_roles.task_id')
                ->whereRaw('role_id = ' . $role_id->dept . ' or role_id = 0')
                ->whereNull('tasks.deleted_at')
                ->get();
        
        $task_deleted = (session('task_removed') != '') ? " and timesheets.id NOT IN (" . trim(session('task_removed'), ',') . ")" : '';
        $notSubmitted = DB::table('timesheets')
                ->select([DB::raw('projects.name as project_name'), DB::raw('tasks.name as task_name'), 'hours', 'minutes', 'date', 'timesheets.id'])
                ->leftJoin('tasks', 'timesheets.task_id', '=', 'tasks.id')
                ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                ->whereRaw('submitor_id = ' . Auth::user()->context_id . " and mail_sent = 0 and timesheets.deleted_at IS NULL "
                        . $task_deleted)
                ->get();

        return [
            'leads' => $leads,
            'managers' => $managers,
            'tasks' => $tasks,
            'notSubmitted' => $notSubmitted,
        ];
    }

    /** Hours already added to timesheet 
     * 
     * @param string $date For this perticular date
     * @param string $rowsNotToBeIncluded TS entries for which date not to be 
     */
    public function hoursWorked($date, $rowsNotToBeIncluded = '') {
        $hours_q = DB::table($this->table)
                ->select([DB::raw('SUM(hours + minutes/60) as total_hours')])
                ->whereRaw('submitor_id = ' . Auth::user()->context_id)
                ->whereRaw('date = "' . date('Y-m-d', strtotime($date)) . '"');
        if ($rowsNotToBeIncluded != '') {
            $hours_q = $hours_q->whereRaw('id NOT IN (' . trim($rowsNotToBeIncluded, ',') . ')');
        }
        $hours = $hours_q->first();
        return $hours->total_hours;
    }

    /** Dates for which no mail has been sent 
     * 
     * @param string $rowsNotToBeIncluded TS entries for which date not to be 
     * checked
     */
    public function datesMailPending($rowsNotToBeIncluded = '') {

        $date_q = DB::table($this->table)
                ->select([DB::raw('Distinct(date) as date')])
                ->whereRaw('mail_sent = 0 and submitor_id = '. Auth::user()->context_id);
        if ($rowsNotToBeIncluded != '') {
            $date_q = $date_q->whereRaw('id NOT IN (' . trim($rowsNotToBeIncluded, ',') . ')');
        }
        $dates = $date_q->get();
        return $dates;
    }

}
