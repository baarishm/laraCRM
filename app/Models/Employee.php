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

class Employee extends Model {

    use SoftDeletes;

    protected $table = 'employees';
    protected $hidden = [
    ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public static function employeeRole() {
        $role_array = collect(DB::table('roles')->whereNull('deleted_at')->get())->keyBy('name');
        $user_role_id = DB::table('role_user')->whereRaw('user_id = "' . Auth::user()->id . '"')->first();

        $where = '';
        if ($role_array['SUPER_ADMIN']->id == $user_role_id->role_id) {
            return 'superAdmin';
        } else if ($role_array['MANAGER']->id == $user_role_id->role_id) {
            return "manager";
        } else if ($role_array['LEAD']->id == $user_role_id->role_id) {
            return "lead";
        } else if ($role_array['ENGINEER']->id == $user_role_id->role_id) {
            return "engineer";
        }
    }

    /**
     * To get employees under a manager or lead
     * @param string $approvalType  Manager or Lead
     */
    public static function getEngineersUnder($approvalType = 'Manager') {
        if ($approvalType == 'Manager') {
            return implode(',', DB::table('employees')->whereNull('deleted_at')->where(function($q) {
                        $q->where('second_approver', Auth::user()->context_id)
                          ->orWhere('first_approver', Auth::user()->context_id);
                    })->pluck('id'));
        } else if ($approvalType == 'Lead') {
            return implode(',', DB::table('employees')->where('first_approver', Auth::user()->context_id)->whereNull('deleted_at')->pluck('id'));
        } else {
            return '';
        }
    }

    /**
     * To update role of user
     * @param string $roleName  Manager or Lead
     * @param string $emp_id  Context_id of user
     */
    public static function updateRole($roleName = 'LEAD', $emp_id = 0) {
        $role_array = collect(DB::table('roles')->whereNull('deleted_at')->get())->keyBy('name');
        $user_id = DB::table('users')->whereRaw('context_id = "' . $emp_id . '"')->first();
        DB::table('role_user')->where('user_id', $user_id->id)->update(['role_id' => $role_array[strtoupper($roleName)]->id]);
    }

    /**
     * Get Manager Details
     * @param int $of  ID of employee whose manager is to be found
     * @return array Details of manager
     */
    public static function getManagerDetails($of = '') {
        $manager = Employee::leftJoin('employees as manager', 'employees.second_approver', '=', 'manager.id')->where('employees.id', $of)->first();
        return $manager;
    }

    /**
     * Get Lead Details
     * @param int $of  ID of employee whose lead is to be found
     * @return array Details of lead
     */
    public static function getLeadDetails($of = '') {
        $lead = Employee::leftJoin('employees as lead', 'employees.first_approver', '=', 'lead.id')->where('employees.id', $of)->first();
        return $lead;
    }

}
