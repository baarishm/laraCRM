<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveMaster;
use App\Models\Employee;
use DB;
use Mail;
use Log;

class LeaveRemindLeadsAndManagers extends Command {

      /**
       * The name and signature of the console command.
       *
       * @var string
       */
      protected $signature = 'leave:approvalReminder';

      /**
       * The console command description.
       *
       * @var string
       */
      protected $description = 'Reminder to Managers and Leads about the leave '
              . 'approval for people under them on every monday.';

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
            //get the unapproved list of leaves grouped by employee_id
            $leaves_pending = collect(
                    LeaveMaster::whereNull('approved')
                            ->where('withdraw', '0')
                            ->get()
                    )->groupBy('EmpId');

            //get the list of managers & people under them grouped by manager's email
            $managers = collect(
                    Employee::whereNull('manager.deleted_at')
                            ->leftJoin('employees as manager', 'manager.id', '=', 'employees.second_approver')
                            ->select(['employees.id', 'employees.name', 'manager.email as manager_email'])
                            ->get()
                    )->groupBy('manager_email');

            //get the list of leads & people under them grouped by lead's email
            $leads = collect(
                    Employee::whereNull('lead.deleted_at')
                            ->leftJoin('employees as lead', 'lead.id', '=', 'employees.first_approver')
                            ->select(['employees.id', 'lead.email as lead_email'])
                            ->get()
                    )->groupBy('lead_email');


            if ($leaves_pending != null || !empty($leaves_pending)) {
                  foreach ($managers as $manager_email => $empRecord) {
                        if ($table = $this->create_table($empRecord, $leaves_pending)) {
                              $mail_body = "Dear Manager, <br>"
                                      . "Following is the list of leaves to be approved/rejected by you: <br><br>";
                              $mail_body .= $table;
                              $mail_body .= "<br><br>"
                                      . "Regards,<br>"
                                      . "Team Ganit PlusMinus";

                              //send mail to manager
                              $recipients['to'] = [$manager_email];

                              Mail::send('emails.test', ['html' => $mail_body], function ($m) use($recipients) {
                                    $m->to($recipients['to'])
                                            ->subject('Leaves pending to be approved/rejected.');
                              });
                        }
                  }

                  foreach ($leads as $lead_email => $empRecord) {
                        if ($table = $this->create_table($empRecord, $leaves_pending)) {
                              $mail_body = "Dear Lead, <br>"
                                      . "Following is the list of leaves to be approved/rejected by you: <br><br>";
                              $mail_body .= $table;
                              $mail_body .= "<br><br>"
                                      . "Regards,<br>"
                                      . "Team Ganit PlusMinus";

                              //send mail to lead
                              $recipients['to'] = [$lead_email];

                              Mail::send('emails.test', ['html' => $mail_body], function ($m) use($recipients) {
                                    $m->to($recipients['to'])
                                            ->subject('Leaves pending to be approved/rejected.');
                              });
                        }
                  }
            }
            Log::info(' - CRON :  Reminder mail for leave approval  On ' . date('d M Y'));
      }

      /**
       * Creates table for pending leaves to be approved 
       * according to each employee under manager or lead
       * @param array $empRecord Employees under manager/lead
       * @param array $leaves_pending Pending leaves record array
       */
      private function create_table($empRecord, $leaves_pending) {
            $table = "";
            $rows = '';
            
            foreach ($empRecord as $record) {
                  if (!empty($leaves_pending[$record['id']])) {
                        foreach ($leaves_pending[$record['id']] as $leave_record) {
                              $rows .= "<tr>"
                                      . "<td>" . $record['name'] . "</td>"
                                      . "<td>" . date('d M Y', strtotime($leave_record['FromDate'])) . "</td>"
                                      . "<td>" . date('d M Y', strtotime($leave_record['ToDate'])) . "</td>"
                                      . "<td>" . $leave_record['NoOfDays'] . "</td>"
                                      . "<td>" . $leave_record['LeaveReason'] . "</td>"
                                      . "<td>" . date('d M Y', strtotime($leave_record['created_at'])) . "</td>"
                                      . "</tr>";
                        }
                  }
            }

            if ($rows == '') {
                  return false;
            } else {
                  $table .= "<table border=1>";
                  $table .= "<tr>"
                          . "<th>Name</th>"
                          . "<th>From</th>"
                          . "<th>To</th>"
                          . "<th>Total Days</th>"
                          . "<th>Reason</th>"
                          . "<th>Apply Date</th>"
                          . "</tr>";
                  $table .= $rows;
                  $table .= "</table>";
            }
            return $table;
      }

}
