<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use DB;
use Mail;
use Log;

class LeaveAdditionCron extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs cron for adding the leave on specific date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        //send mail to HR against -ive leaves
        //code to export excel
        $sheet_data = Employee::select([DB::raw('emp_code as Emp_Code'), DB::raw('name as Name'), DB::raw('total_leaves as Total_Leaves'), DB::raw('available_leaves as Available_Leaves')])->get()->toArray();

        $file_data = \Excel::create('Leaves_Details_Monthly_' . date('d-M-Y',strtotime('-1 days')), function($excel) use ($sheet_data) {
                    $excel->sheet('Leaves_Details', function($sheet) use ($sheet_data) {
                        $sheet->fromArray($sheet_data);
                    });
                });
        $file = $file_data->string('xlsx');
        $file_data->store('xlsx', storage_path('leave-deatils-monthly'));
        $attachement = array(
            'name' => 'Leaves_Details_' . date('d-M-Y'), //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($file)
        );

        $html = "Dear Priyanka Kandpal,"
                . "<br>"
                . "<br>"
                . "Please find the attached file for leaves available to employees as on " . date('d M Y', strtotime('-1 days')) . "."
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";
        $recipients['to'] = ['priyanka.kandpal@ganitsoftech.com', 'ashok.chand@ganitsoft.com','varsha.mittal@ganitsoftech.com'];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients, $attachement) {
            $m->attach($attachement['file'], ['as' => $attachement['name'], 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            $m->to($recipients['to'])
                    ->subject('Leave Report till  ' . date('Y-m-d', strtotime('-1 days')));
        });



        $employees = Employee::get();

        foreach ($employees as $employee) {

            if ($employee->available_leaves < 0) {
                $employee->available_leaves = 0;
            }

            if (date('Y-m-d', strtotime('-15 days')) >= $employee->date_hire) {
                if ($employee->is_confirmed) {
                    $leave = $employee->total_leaves + 2;
                    $leave_avialable = $employee->available_leaves + 2;
                } else {
                    $leave = $employee->total_leaves + 1.5;
                    $leave_avialable = $employee->available_leaves + 1.5;
                }
            } else {
                $leave = $employee->total_leaves + 1;
                $leave_avialable = $employee->available_leaves + 1;
            }
            Employee::find($employee->id)->update(['total_leaves' => $leave, 'available_leaves' => $leave_avialable]);
        }
        Log::info(' - Leaves added successfully!');
    }

}
