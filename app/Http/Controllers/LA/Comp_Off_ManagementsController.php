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
use App\Models\Comp_Off_Management;
use App\Models\Employee;
use Mail;

class Comp_Off_ManagementsController extends Controller {

    public $show_action = true;
    public $view_col = 'employee_id';
    public $listing_cols = ['id', 'employee_id', 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Comp_Off_Managements', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Comp_Off_Managements', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Comp_Off_Managements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($teamMember = 'false') {
        $module = Module::get('Comp_Off_Managements');

        $this->custom_cols = ['Id', 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by'];
        $role = Employee::employeeRole();
        if ($role == 'superAdmin') {
            $this->show_action = false;
            $this->custom_cols = ['employee_id', 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by'];
        } else if ($teamMember === 'true') {
            $this->show_action = false;
            $this->custom_cols = ['employee_id', 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by', 'Action'];
        } else if ($teamMember === 'false') {
            $this->show_action = false;
            $this->custom_cols = ['id', 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by', 'Action'];
        }

        if (Module::hasAccess($module->id) || $teamMember === 'true') {
            return View('la.comp_off_managements.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->custom_cols,
                'module' => $module,
                'teamMember' => $teamMember
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    public function teamMemberList() {
        return $this->index('true');
    }

    /**
     * Show the form for creating a new comp_off_management.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $module = Module::get('Comp_Off_Managements');
        if (Module::hasAccess("Comp_Off_Managements", "create")) {
            return view('la.comp_off_managements.add', [
                'module' => $module,
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Store a newly created comp_off_management in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Comp_Off_Managements", "create")) {

            $rules = Module::validateRules("Comp_Off_Managements", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            //check existance  
            $comp_off_record['employee_id'] = base64_decode(base64_decode($request->get('employee_id')));
            $comp_off_record['start_date'] = date('Y-m-d', strtotime($request->get('start_date')));
            $comp_off_record['end_date'] = date('Y-m-d', strtotime($request->get('start_date')));
//            $comp_off_record['end_date'] = date('Y-m-d', strtotime($request->get('end_date')));
            $comp_off_record['description'] = $request->get('description');
            $record = Comp_Off_Management::where('employee_id', $comp_off_record['employee_id'])
                    ->where('start_date', '>=', $comp_off_record['start_date'])
                    ->where('end_date', '<=', $comp_off_record['end_date'])
                    ->get();

            $Exists = $record->count();

            if ($Exists > 0) {
                return redirect(config('laraadmin.adminRoute') . '/comp_off_managements')->withErrors(['message' => 'You Smarty! Already applied comp off for these dates.']);
            } else if (($comp_off_record['start_date'] < date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))) || ($comp_off_record['end_date'] < date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))) || ($comp_off_record['end_date'] > date('Y-m-d')) || ($comp_off_record['start_date'] > date('Y-m-d'))) {
                return redirect(config('laraadmin.adminRoute') . '/comp_off_managements')->with('error', 'Smarty! Your dates are out of applicable range.');
            }

            $insert_id = Comp_Off_Management::create($comp_off_record)->id;

            return redirect()->route(config('laraadmin.adminRoute') . '.comp_off_managements.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified comp_off_management.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Comp_Off_Managements", "view")) {

            $comp_off_management = Comp_Off_Management::find($id);
            if (isset($comp_off_management->id)) {
                $module = Module::get('Comp_Off_Managements');
                $module->row = $comp_off_management;

                return view('la.comp_off_managements.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('comp_off_management', $comp_off_management);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("comp_off_management"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified comp_off_management.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Comp_Off_Managements", "edit")) {
            $comp_off_management = Comp_Off_Management::find($id);
            if (isset($comp_off_management->id) && (date('Y-m-d') <= date('Y-m-d', strtotime('+30 days', strtotime($comp_off_management->start_date))))) {
                $module = Module::get('Comp_Off_Managements');

                $module->row = $comp_off_management;

                return view('la.comp_off_managements.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('comp_off_management', $comp_off_management);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("comp_off_management"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified comp_off_management in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Comp_Off_Managements", "edit")) {

            $rules = Module::validateRules("Comp_Off_Managements", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

//check existance  
            $update_data = $request->all();
            $update_data['employee_id'] = base64_decode(base64_decode($request->get('employee_id')));
            $update_data['start_date'] = date('Y-m-d', strtotime($request->get('start_date')));
            $update_data['end_date'] = date('Y-m-d', strtotime($request->get('start_date')));
//            $update_data['end_date'] = date('Y-m-d', strtotime($request->get('end_date')));

            $row = Comp_Off_Management::where('employee_id', $update_data['employee_id'])
                    ->where('start_date', '>=', $update_data['start_date'])
                    ->where('end_date', '<=', $update_data['end_date'])
                    ->pluck('id');

            $Exists = $row->count();

            if ($Exists > 0 && !in_array($id, $row->toArray())) {
                return redirect(config('laraadmin.adminRoute') . '/comp_off_managements')->withErrors(['message' => 'You Smarty! Already applied comp off for these dates.']);
            } else if (($update_data['start_date'] < date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))) || ($update_data['end_date'] < date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))) || ($update_data['end_date'] > date('Y-m-d')) || ($update_data['start_date'] > date('Y-m-d'))) {
                return redirect(config('laraadmin.adminRoute') . '/comp_off_managements')->with('error', 'Smarty! Your dates are out of applicable range.');
            }

            $insert_id = Comp_Off_Management::find($id)->update($update_data);

            return redirect()->route(config('laraadmin.adminRoute') . '.comp_off_managements.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified comp_off_management from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Comp_Off_Managements", "delete")) {
            $comp_off_management = Comp_Off_Management::find($id);
            if (date('Y-m-d') <= date('Y-m-d', strtotime('+30 days', strtotime($comp_off_management->start_date))))
                Comp_Off_Management::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.comp_off_managements.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax($teamMember) {
        $first_col = 'comp_off_managements.id';
        $first_col_list = 'id';
        $role = Employee::employeeRole();
        if ($role == 'superAdmin' || $teamMember === 'true') {
            $first_col = $first_col_list = 'employee_id';
        }
        $this->listing_cols = [$first_col_list, 'start_date', 'end_date', 'description', 'approved', 'approved_by', 'rejected_by'];
        $values = DB::table('comp_off_managements')
                ->select([$first_col, DB::raw('DATE_FORMAT(comp_off_managements.start_date, "%d %b %Y") as start_date'), DB::raw('DATE_FORMAT(comp_off_managements.end_date, "%d %b %Y") as end_date'), 'description', DB::raw('if(approved IS NOT NULL, (IF(approved = 1, "Approved","Rejected")),"Pending") as approved'), 'approved_by', 'rejected_by', DB::raw('comp_off_managements.id as id')])
                ->leftJoin('employees', 'employees.id', '=', 'comp_off_managements.employee_id')
                ->whereNull('comp_off_managements.deleted_at');

        if ($role == 'engineer' && $teamMember === 'false') {
            $values = $values->where('employee_id', Auth::user()->context_id);
        } else if ($teamMember === 'true') {
            $engineersUnder = Employee::getEngineersUnder(ucwords($role));
            if ($engineersUnder != '')
                $values = $values->whereRaw('employee_id IN (' . $engineersUnder . ')');
        }

        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Comp_Off_Managements');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/comp_off_managements/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }
            if ($teamMember === 'false' && $this->show_action && $data->data[$i][4] == 'Pending' && (date('Y-m-d') <= date('Y-m-d', strtotime('+30 days', strtotime($data->data[$i][1]))))) {
                $output = '';
                if (Module::hasAccess("Comp_Off_Managements", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/comp_off_managements/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Comp_Off_Managements", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.comp_off_managements.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline', 'class' => 'delete']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][7] = (string) $output;
            } else if ($teamMember === 'false' && $this->show_action) {
                $data->data[$i][7] = '';
            } else if ($teamMember === 'true') {
                $output = '';
                $role = Employee::employeeRole();
                if ($role == 'lead') {
                    if ($data->data[$i][4] == '1' || $data->data[$i][4] == '0') {
                        $output .= 'Action Taken';
                    } else {
                        $output .= '<button type="button" class="btn btn-success actionCompOff mr10" name="Approved" id="Approved" data-id =' . $data->data[$i][7] . ' data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '">Approve</button>';
                        $output .= '<button type="button" class="btn btn actionCompOff" name="Rejected" id="Rejected" data-id =' . $data->data[$i][7] . ' style="background-color: #f55753;border-color: #f43f3b;color: white"  data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '">Reject</button> ';
                    }
                } else if ($role == 'manager') {
                    if (($data->data[$i][4] == 'Approved' || $data->data[$i][4] == 'Rejected') && $data->data[$i][5] != '' && $data->data[$i][6] != '') {
                        $output .= 'Action Taken';
                    } else if ($data->data[$i][4] == 'Approved' && ($data->data[$i][6] == '' || $data->data[$i][6] == null)) {
                        $output .= '<button type="button" class="btn btn actionCompOff" name="Rejected" id="Rejected" data-id =' . $data->data[$i][7] . ' style="background-color: #f55753;border-color: #f43f3b;color: white" data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '" >Reject</button> ';
                    } else if ($data->data[$i][4] == 'Rejected' && ($data->data[$i][5] == '' || $data->data[$i][5] == null)) {
                        $output .= '<button type="button" class="btn btn-success actionCompOff mr10" name="Approved" id="Approved" data-id =' . $data->data[$i][7] . ' data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '">Approve</button>';
                    } else {
                        $output .= '<button type="button" class="btn btn-success actionCompOff mr10" name="Approved" id="Approved" data-id =' . $data->data[$i][7] . ' data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '">Approve</button>';
                        $output .= '<button type="button" class="btn btn actionCompOff" name="Rejected" id="Rejected" data-id =' . $data->data[$i][7] . ' data-start-date="' . $data->data[$i][1] . '" data-end-date="' . $data->data[$i][2] . '" style="background-color: #f55753;border-color: #f43f3b;color: white" >Reject</button> ';
                    }
                }
                $data->data[$i][7] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

    //Ajax Functions 

    public function ajaxApproveCompOff() {
        $update_field = ['approved' => $_GET['approved']];
        if ($_GET['approved']) {
            $update_field['approved_by'] = Auth::user()->context_id;
        } else {
            $update_field['rejected_by'] = Auth::user()->context_id;
        }

        $datetime1 = date_create($_GET['start_date']);
        $datetime2 = date_create($_GET['end_date']);
        $interval = date_diff($datetime1, $datetime2);
        $days = $interval->format('%a') + 1;

        Comp_Off_Management::where('id', $_GET['id'])->update($update_field);
        $compoffRow = Comp_Off_Management::find($_GET['id']);
        $employee = Employee::find($compoffRow->employee_id);
        if ($compoffRow->approved && $compoffRow->approved_by != '') {
            $comp_off = $employee->comp_off + $days;
        } else if (!$compoffRow->approved && $compoffRow->approved_by != '' && $compoffRow->rejected_by != '') {
            $comp_off = $employee->comp_off - $days;
        }

        DB::update("update employees set comp_off = $comp_off where id = ?", [$compoffRow->employee_id]);

        $employee_update = Employee::find($compoffRow->employee_id);

        $mail_data = [
            'approved' => $_GET['approved'],
            'action_by' => ucwords(Auth::user()->name),
            'action_date' => date('d M Y'),
            'mail_to' => $employee_update->email,
            'mail_to_name' => ucwords($employee_update->name),
            'leave_from' => date('d M Y', strtotime($compoffRow->start_date)),
            'leave_to' => date('d M Y', strtotime($compoffRow->end_date))
        ];
        $this->sendApprovalMail($mail_data);
        return "true";
    }

    /**
     * Send mail to Employee in case of approval or rejection
     * @param array $data contains 
     * approved 0/1
     * action_by
     * action_date
     * mail_to
     * mail_to_name
     * leave_from
     * leave_to
     */
    private function sendApprovalMail($data) {
        $html = "Greetings of the day " . $data['mail_to_name'] . "!<br><br>"
                . "Your comp off are <b>" . (($data['approved']) ? 'Accepted' : 'Rejected') . "</b> by " . $data['action_by'] . " for dates from <b>" . $data['leave_from'] . "</b> to <b>" . $data['leave_to'] . "</b> on " . $data['action_date'] . "."
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";

        $recipients['to'] = [$data['mail_to']];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
            $m->to($recipients['to'])
                    ->subject('Approval of your Comp off Application');
        });
        return true;
    }

}
