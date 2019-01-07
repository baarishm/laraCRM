<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveMaster;
use App\Models\Holidays_List;
use DB;
use Mail;
use Log;

class LeaveDailyList extends Command {

      /**
       * The name and signature of the console command.
       *
       * @var string
       */
      protected $signature = 'leave:DailyList';

      /**
       * The console command description.
       *
       * @var string
       */
      protected $description = 'People on leave daily list.';

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
            //not saturday and not sunday and not a holiday
            if ((date('N') != 6 && date('N') != 0) && (Holidays_List::where('day', date('Y-m-d'))->count() == 0)) {
                  $sheet_data = LeaveMaster::
                          select([DB::raw('employees.name as Employee'), DB::raw('leavemaster.NoOfDays as Total_Days'), DB::raw('DATE_FORMAT(FromDate,\'%d %b %Y\') as FromDate'), DB::raw('DATE_FORMAT(ToDate,\'%d %b %Y\') as ToDate'), DB::raw('if(leavemaster.approved = 1, "Approved", if(leavemaster.approved = 0, "Rejected","Pending")) as Status')])
                          ->whereRaw('FromDate <= CURDATE()')
                          ->whereRaw('ToDate >= CURDATE()')
                          ->whereRaw('withdraw = 0')
                          ->leftJoin('employees', 'leavemaster.EmpId', '=', 'employees.id')
                          ->leftJoin('employees as approver', 'leavemaster.ApprovedBy', '=', 'approver.id')
                          ->orderBy('leavemaster.created_at', 'desc')
                          ->get();

                  $html = "Dear Mohit Arora,"
                          . "<br>"
                          . "<br>";

                  if ($sheet_data->count() == 0) {
                        return ; //as asked, if nobody is on leave then no email notification has to be sent
                        $html .= "Nobody is on leave today.";
                  } else {
                        $html .= "List of employees on leave is as under : <br> "
                                . "<table border=1 >"
                                . "<tr>"
                                . "<th>S. No.</th>"
                                . "<th>Name</th>"
                                . "<th>From</th>"
                                . "<th>To</th>"
                                . "<th>Total Days</th>"
                                . "<th>Status</th>"
                                . "</tr>";

                        foreach ($sheet_data as $n => $rec) {
                              $html .= "<tr>"
                                      . "<td>" . ($n + 1) . "</td>"
                                      . "<td>" . $rec->Employee . "</td>"
                                      . "<td>" . $rec->FromDate . "</td>"
                                      . "<td>" . $rec->ToDate . "</td>"
                                      . "<td>" . $rec->Total_Days . "</td>"
                                      . "<td>" . $rec->Status . "</td>"
                                      . "</tr>";
                        }

                        $html .= "</table>";
                  }
                  $html .= "<br><br>"
                          . "Regards,<br>"
                          . "Team Ganit PlusMinus";

                  $recipients['to'] = ['mohit.arora@ganitsoft.com', 'ashok.chand@ganitsoft.com'];
//            $recipients['to'] = ['varsha.mittal@ganitsoft.com'];

                  Mail::send('emails.test', ['html' => $html], function ($m) use($recipients) {
                        $m->to($recipients['to'])
                                ->subject('Employees on Leave | ' . date('Y-m-d'));
                  });
                  Log::info(' - CRON : Mail to Mohit for list of employees on leave today - ' . date('d M Y') . '.');
            }
      }
}
