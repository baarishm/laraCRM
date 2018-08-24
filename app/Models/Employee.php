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

    public function getEngineersUnder($approvalType = 'Manager') {
        if ($approvalType == 'Manager') {
            return implode(',', DB::table('employees')->where('second_approver', Auth::user()->context_id)->whereNull('deleted_at')->pluck('id'));
        } else if ($approvalType == 'Lead') {
            return implode(',', DB::table('employees')->where('first_approver', Auth::user()->context_id)->whereNull('deleted_at')->pluck('id'));
        } else {
            return '';
        }
    }

}
