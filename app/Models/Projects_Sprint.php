<?php

/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects_Sprint extends Model {

    use SoftDeletes;

    protected $table = 'projects_sprints';
    protected $hidden = [
    ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    /**
     * To check if sprint with dates already exists
     * @param int $project_id project's id
     * @param date $start_date Sprint start date
     * @param date $end_date Sprint end date
     * @return object id of similar records
     * @author Varsha Mittal
     */
    public static function sprintExists($project_id, $start_date, $end_date) {
        $row = Projects_Sprint::where('project_id', $project_id)
                ->where(function($q) use ($start_date, $end_date) {
                    $q->where(
                            function($qin) use ($start_date, $end_date) {
                        $qin->where('start_date', '<=', $start_date)->where('end_date', '>=', $start_date);
                    })
                    ->orWhere(function($qin) use ($start_date, $end_date) {
                        $qin->where('start_date', '<=', $end_date)->where('end_date', '>=', $end_date);
                    });
                })
                ->whereNull('deleted_at')
                ->pluck('id');
        return $row;
    }

}
