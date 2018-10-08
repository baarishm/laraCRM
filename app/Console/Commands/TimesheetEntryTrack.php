<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Holidays_List;
use DB;
use Mail;
use Log;

class TimesheetEntryTrack extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:weekTrack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track every users weekly timesheet entry record.';

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
        $timesheet = collect(DB::table('employees')
                        ->select([DB::raw('count(timesheets.id) as day_entry'), 'date', 'submitor_id', 'name', 'email'])
                        ->where('date', '>=', date('Y-m-d', strtotime('last monday')))
                        ->where('date', '<=', date('Y-m-d', strtotime('friday')))
                        ->leftJoin('timesheets', 'timesheets.submitor_id', '=', 'employees.id')
                        ->groupBy(['submitor_id', 'date'])->get())->groupBy('submitor_id');

        $empArray = [];
        $bade_log = config('custom.bade_log');

        $allDays = [
            date('Y-m-d', strtotime('last monday')) => date('d M Y', strtotime('last monday')),
            date('Y-m-d', strtotime('last tuesday')) => date('d M Y', strtotime('last tuesday')),
            date('Y-m-d', strtotime('last wednesday')) => date('d M Y', strtotime('last wednesday')),
            date('Y-m-d', strtotime('last thursday')) => date('d M Y', strtotime('last thursday')),
            date('Y-m-d', strtotime('friday')) => date('d M Y', strtotime('friday')),
        ];

        $get_holiday_in_week = Holidays_List::
                where('day', '>=', date('Y-m-d', strtotime('last monday')))
                ->where('day', '<=', date('Y-m-d', strtotime('friday')))
                ->pluck('day');

        if (count($get_holiday_in_week) != 0) {
            foreach ($get_holiday_in_week as $holiday) {
                unset($allDays[$holiday]);
            }
        }
        
        //foreach user, check if date are full or not means 5 records or not
        foreach ($timesheet as $acha_bacha => $records) {
            //for this, make an array of dates in week in every loop
            $dates = $allDays;
            //if not then loop through date records, collect dates which are not present and mail
            if (count($records) < count($dates)) {
                // unset value which is present in array and keep for which timesheet not sent
                foreach ($records as $record) {
                    if (array_key_exists($record->date, $dates)) {
                        unset($dates[$record->date]);
                    }
                }
                $mail_body = 'Dear ' . $records[0]->name . ','
                        . '<br/><br/>'
                        . 'You have not filled timesheet for <b>' . implode(',', $dates) . '</b>. Kindly fill the same ASAP.'
                        . "<br><br>"
                        . "Regards,<br>"
                        . "Team Ganit PlusMinus";
                $recipients['to'] = [$records[0]->email];
                if (!in_array($records[0]->email, $bade_log)) {
                    Mail::send('emails.test', ['html' => $mail_body], function ($m) use($recipients) {
                        $m->to($recipients['to'])
                                ->subject('Timesheets Not Found');
                    });
                }
            }
            //also keep emp_ids in array which have filled atleast one timesheet
            $empArray[] = $acha_bacha;
        }

        //get records of defaulters and mail that you haven't filled the timesheet for the entire week

        $detail = Employee::select('name', 'id', 'email')->whereNull('deleted_at');

        if (!empty($empArray)) {
            $detail->whereNotIn('id', $empArray);
        }
        $employees_No_timesheet = $detail->get()->toArray();

        foreach ($employees_No_timesheet as $ganda_bacha) {
            $mail_body = 'Dear ' . $ganda_bacha['name'] . ','
                    . '<br/><br/>'
                    . 'You have not filled timesheet for this week, i.e, from <b>' . date('d M Y', strtotime('last monday')) . ' to ' . date('d M Y', strtotime('friday')) . '</b>. Kindly fill the same ASAP.'
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
        Log::info(' - CRON :  Mail every users weekly timesheet entry record.');
    }

}
