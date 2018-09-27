<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Timesheet;
use App\Models\Employee;
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
        $timesheet_users = DB::table('timesheets')
                        ->where('date', '=', date('Y-m-d', strtotime('-1 days')))
                        ->groupBy(['submitor_id', 'date'])->pluck('submitor_id');

        $bade_log = ['mohit.arora@ganitsoftech.com', 'tarun.chawla@ganitsoft.com', 'neeta.chawla@ganitsoft.com', 'ashok.chand@ganitsoft.com', 'priyanka.kandpal@ganitsoftech.com', 'sachin.mishra@ganitsoftech.com'];

        //get records of defaulters and mail that you haven't filled the timesheet for the entire week

        $detail = Employee::select('name', 'id', 'email')->whereNull('deleted_at');

        if (!empty($timesheet_users)) {
            $detail->whereNotIn('id', $timesheet_users);
        }
        $employees_No_timesheet = $detail->get()->toArray();

        foreach ($employees_No_timesheet as $ganda_bacha) {
            $mail_body = 'Dear ' . $ganda_bacha['name'] . ','
                    . '<br/><br/>'
                    . 'You have not filled timesheet for <b>'.date('d M Y', strtotime('-1 days')).'</b>. Kindly fill the same ASAP.'
                    . "<br><br>"
                    . "Regards,<br>"
                    . "Team Ganit PlusMinus";
            $recipients['to'] = [$ganda_bacha['email']];

            if (!in_array($ganda_bacha['email'], $bade_log)) {
                Mail::send('emails.test', ['html' => $mail_body], function ($m) use($recipients) {
                    $m->to($recipients['to'])
                            ->subject('Timesheets Not Found');
                });
            }
        }
      Log::info(' - Daily Timesheet Mail sent');
    }

}
