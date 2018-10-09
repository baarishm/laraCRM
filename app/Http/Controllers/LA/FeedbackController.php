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
use App\Models\Feedback;
use App\Models\Employee;
use Mail;

class FeedbackController extends Controller {

    public $show_action = false;
    public $view_col = 'employee_id';
    public $listing_cols = ['id', 'employee_id', 'type', 'suggestion'];

    public function __construct() {
        // Field Access of Listing Columns
        if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Feedback', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Feedback', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the Feedback.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $module = Module::get('Feedback');

        if (Module::hasAccess($module->id)) {
            return View('la.feedback.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new feedback.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created feedback in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Module::hasAccess("Feedback", "create")) {

            $rules = Module::validateRules("Feedback", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $insert_data = $request->all();
            $insert_data['employee_id'] = base64_decode(base64_decode($insert_data['employee_id']));
            $insert_id = Feedback::create($insert_data);
            
            //send mail to authority
            $html = "Dear Ganit,<br><br>"
                    . ucwords(Auth::user()->name)
                    . " has given a suggestion under type <b>" . $request->all()['type'] . "</b>: <br><br>"
                    . $request->all()['suggestion']
                    . "<br><br>"
                    . "Regards,<br>"
                    . "Team Ganit PlusMinus";

            $recipients['to'] = ['mohit.arora@ganitsoftech.com'];
            $recipients['cc'] = ['varsha.mittal@ganitsoftech.com', 'pritam.swami@ganitsoftech.com'];
            Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
                $m->to($recipients['to'])
                        ->cc($recipients['cc']) //need to add this recipent in mailgun
                        ->subject('Suggestion Received by ' . ucwords(Auth::user()->name));
            });

            return redirect()->route(config('laraadmin.adminRoute') . '.feedback.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified feedback.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Module::hasAccess("Feedback", "view")) {

            $feedback = Feedback::find($id);
            if (isset($feedback->id)) {
                $module = Module::get('Feedback');
                $module->row = $feedback;

                return view('la.feedback.show', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                            'no_header' => true,
                            'no_padding' => "no-padding"
                        ])->with('feedback', $feedback);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("feedback"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified feedback.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Module::hasAccess("Feedback", "edit")) {
            $feedback = Feedback::find($id);
            if (isset($feedback->id)) {
                $module = Module::get('Feedback');

                $module->row = $feedback;

                return view('la.feedback.edit', [
                            'module' => $module,
                            'view_col' => $this->view_col,
                        ])->with('feedback', $feedback);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("feedback"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Module::hasAccess("Feedback", "edit")) {

            $rules = Module::validateRules("Feedback", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
                ;
            }

            $insert_id = Module::updateRow("Feedback", $request, $id);

            return redirect()->route(config('laraadmin.adminRoute') . '.feedback.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified feedback from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Module::hasAccess("Feedback", "delete")) {
            Feedback::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.feedback.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax() {
        $this->show_action = false;

        $role = Employee::employeeRole();
        $where = '';
        if ($role != 'superAdmin') {
            $where = 'employee_id = ' . Auth::user()->context_id;
        }
        $value = DB::table('feedback')->select($this->listing_cols)->whereNull('deleted_at');
        if ($where != '') {
            $value->whereRaw($where);
        }

        $values = $value;

        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Feedback');

        for ($i = 0; $i < count($data->data); $i++) {
            for ($j = 0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if ($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if ($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/feedback/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }

            if ($this->show_action) {
                $output = '';
                if (Module::hasAccess("Feedback", "edit")) {
                    $output .= '<a href="' . url(config('laraadmin.adminRoute') . '/feedback/' . $data->data[$i][0] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if (Module::hasAccess("Feedback", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.feedback.destroy', $data->data[$i][0]], 'method' => 'delete', 'style' => 'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string) $output;
            }
        }
        $out->setData($data);
        return $out;
    }

}
