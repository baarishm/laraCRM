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

class Department extends Model {

    use SoftDeletes;

    protected $table = 'departments';
    protected $hidden = [
    ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public static function department() {
        $department_array = collect(DB::table('departments')->whereNull('deleted_at')->get())->keyBy('tags');
        $user_department_id = DB::table('employees')->whereRaw('id = "' . Auth::user()->context_id . '"')->first();

        $where = '';
        if ($department_array['dev']->id == $user_department_id->dept) {
            return 'Development';
        } else if ($department_array['BA']->id == $user_department_id->dept) {
            return "BusinessAnalysis";
        } else if ($department_array['QA']->id == $user_department_id->dept) {
            return "QualityAnalysis";
        } else if ($department_array['Acc']->id == $user_department_id->dept) {
            return "Account";
        }
    }

}
