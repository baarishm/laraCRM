<?php

/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use App\Models\Projects_Sprint;
use App\Models\Project;

class Projects_SprintsController extends Controller {

    public $show_action = false;
    public $view_col = '';

    public function __construct() {
        //
    }

    /**
     * Show the form for creating a new timesheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created timesheet in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $insert_data = $request->all();
        $row = Projects_Sprint::sprintExists($insert_data['project_id'], $insert_data['start_date'], $insert_data['end_date']);
        $Exist = $row->count();
        if ($Exist) {
            return "Sprint Already exists with these dates!";
        } else {
            unset($insert_data['_token']);
            if ($id = Projects_Sprint::create($insert_data)->id)
                return $id;
            return 'Creation failed! Please retry.';
        }
    }

    /**
     * Display the specified timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified timesheet in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $update_data = $request->all();
        $row = Projects_Sprint::sprintExists($update_data['project_id'], $update_data['start_date'], $update_data['end_date']);
        $Exist = $row->count();
        if ($Exist && !in_array($id, $row->toArray())) {
            return "Sprint Already exists with these dates!";
        } else {
            if ($update_row = Projects_Sprint::find($id)->update($request->all()))
                return $id;
            return 'Updation failed! Please retry.';
        }
    }

    /**
     * Remove the specified timesheet from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {
        Projects_Sprint::find($id)->delete();
        return "Record deleted successfully!";
    }

    /* Ajax Functions */

    /**
     * To get running projects as per the date
     * @param request $request 
     * date against which running sprints are to be bought
     * project against which sprints are to be bought
     * @return array list of projects
     */
    public function ajaxSprintList(Request $request) {
        $list = Projects_Sprint::where('project_id', $request->project_id);
        if (isset($request->date) && $request->date != '') {
            $list = $list->where('start_date', '<=', $request->date)
                    ->where('end_date', '>=', $request->date);
        }
        return $list->get();
    }

    /**
     * Check the sprint dates are inside project date range
     * @param request $request 
     * start_date, end_date, project_id
     * @return string true or false
     */
    public function ajaxProjectDatesCheck(Request $request) {
        $project = Project::find($request->project_id);
        if ($request->start_date < $project->start_date || $request->end_date > $project->end_date) {
            return "false";
        } else {
            return "true";
        }
    }

    /* Ended Ajax Functions */
}
