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
            return implode(',', DB::table('employees')->where('second_approver', Auth::user()->context_id)->orWhere('first_approver', Auth::user()->context_id)->whereNull('deleted_at')->pluck('id'));
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
        $user_id = DB::table('user')->whereRaw('context_id = "' . $emp_id . '"')->first();
        DB::table('role_user')->where('user_id', $user_id->id)->update(['role_id' => $role_array[strtoupper($roleName)]->id]);
    }

}
