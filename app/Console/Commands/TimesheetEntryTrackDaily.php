<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Holidays_List;
use DB;
use Mail;
use Log;

class TimesheetEntryTrackDaily extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:dailyEntryTrack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track every users daily timesheet entry record.';

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
        //if Monday or tuesday then subtract 4 days else 2 days
        $sub_day = (date('N') == 1 || date('N') == 2) ? '4' : '2';

        if (Holidays_List::where('day', date('Y-m-d', strtotime('-' . $sub_day . ' days')))->count() == 0) {
            $timesheet_users = DB::table('timesheets')
                            ->where('date', '=', date('Y-m-d', strtotime('-' . $sub_day . ' days')))
                            ->whereNull('timesheets.deleted_at')
                            ->groupBy(['submitor_id', 'date'])->pluck('submitor_id');

            $bade_log = config('custom.bade_log');

            //get records of defaulters and mail that you haven't filled the timesheet for the entire week

            $detail = Employee::select('name', 'id', 'email')->whereNull('deleted_at');

            if (!empty($timesheet_users)) {
                $detail->whereNotIn('id', $timesheet_users);
            }

            $employees_No_timesheet = $detail->get()->toArray();

            $receipents = [];
            foreach ($employees_No_timesheet as $ganda_bacha) {
                if (!in_array($ganda_bacha['email'], $bade_log)) {
                    $receipents[] = $ganda_bacha['email'];
                }
            }

            if (count($receipents) > 0) {
                $mail_body = 'Dear All,'
                        . '<br/><br/>'
                        . 'You have not filled timesheet for <b>' . date('d M Y', strtotime('-' . $sub_day . ' days')) . '</b>. Kindly fill the same ASAP.';

//            $manager = Employee::getLeadDetails($ganda_bacha['id']); //taking lead as manager here

                $recipients['to'] = $receipents;
                $recipients['cc'] = ['ashok.chand@ganitsoft.com', 'mohit.arora@ganitsoftech.com'];
            } else {
                $mail_body = 'Dear All,'
                        . '<br><br>'
                        . 'No defaulters found for <b>' . date('d M Y', strtotime('-' . $sub_day . ' days')) . '</b>';
                $recipients['to'] = ['ashok.chand@ganitsoft.com', 'mohit.arora@ganitsoftech.com'];
            }

            $mail_body .= "<br><br>"
                    . "Regards,<br>"
                    . "Team Ganit PlusMinus";

            Mail::send('emails.test', ['html' => $mail_body], function ($m) use($recipients) {
                $m->to($recipients['to'])
                        ->cc($recipients['cc'])
                        ->subject('Timesheets Not Submitted!');
            });
            Log::info(' - CRON :  Daily Timesheet Mail sent For ' . date('d M Y', strtotime('-' . $sub_day . ' days')) . ' On ' . date('d M Y'));
        }
    }

}
