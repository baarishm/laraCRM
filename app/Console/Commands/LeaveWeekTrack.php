<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveMaster;
use App\Models\Employee;
use DB;
use Mail;

class LeaveWeekTrack extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:weekTrack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mail to HR for leaves approved for the week.';

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
        //code to export excel
        $sheet_data = LeaveMaster::
                        select([DB::raw('employees.name as Employee'), DB::raw('leavemaster.NoOfDays as Total_Days'), DB::raw('DATE_FORMAT(FromDate,\'%d %b %Y\') as FromDate'), DB::raw('DATE_FORMAT(ToDate,\'%d %b %Y\') as ToDate'), DB::raw('approver.name as ApprovedBy')])
                        ->where(function($q) {
                            $q->where(function($internal) {
                                $internal->where('leavemaster.created_at', '>=', date('Y-m-d', strtotime('last monday')))
                                ->where('leavemaster.created_at', '<=', date('Y-m-d', strtotime('last friday')));
                            })
                            ->orWhere(function($internal) {
                                $internal->where('leavemaster.updated_at', '>=', date('Y-m-d', strtotime('last monday')))
                                ->where('leavemaster.updated_at', '<=', date('Y-m-d', strtotime('last friday')));
                            });
                        })
                        ->where('Approved', '1')
                        ->leftJoin('employees', 'leavemaster.EmpId', '=', 'employees.id')
                        ->leftJoin('employees as approver', 'leavemaster.ApprovedBy', '=', 'approver.id')
                        ->get()->toArray();

        $file = \Excel::create('Leaves_Details_' . date('d-M-Y', strtotime('-1 days')), function($excel) use ($sheet_data) {
                    $excel->sheet('Leaves_Details', function($sheet) use ($sheet_data) {
                        $sheet->fromArray($sheet_data);
                    });
                });
        $file = $file->string('xlsx');
        $attachement = array(
            'name' => 'Leaves_Details', //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($file)
        );

        $html = "Dear Priyanka Kandpal,"
                . "<br>"
                . "<br>"
                . "Please find the attached file for leaves approved from " . date('d M Y', strtotime('last monday')) . ' to ' . date('d M Y', strtotime('last friday'))
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";
        $recipients['to'] = ['priyanka.kandpal@ganitsoftech.com', 'ashok.chand@ganitsoft.com'];  

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients, $attachement) {
            $m->attach($attachement['file'], ['as' => $attachement['name'], 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            $m->to($recipients['to'])
                    ->subject('Leave Report for  ' . date('Y-m-d', strtotime('-1 days')));
        });
    }

}
