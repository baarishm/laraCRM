<?php

/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model {

    use SoftDeletes;

    protected $table = 'managers';
    protected $hidden = [
    ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    /**
     * Get the Employee associated with the user.
     */
    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }

    /**
     * Get the Timesheet associated with the user.
     */
    public function timesheets() {
        return $this->hasMany('App\Models\Timesheet');
    }
}
