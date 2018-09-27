<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LA\TimesheetsController;
use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
use Mail;
use Log;

class TimesheetEmail extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:dailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Timesheet Report to Ashok Chand';

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
        $sheet_data = Employee::
                        select([DB::raw('employees.emp_code as Emp_Code'), DB::raw('DATE_FORMAT(date,\'%d %b %Y\') as Date'), DB::raw('employees.name as Employee'), DB::raw('projects.name as Project'), DB::raw('projects_sprints.name as Sprint_Name'), DB::raw('tasks.name as Task'), DB::raw('comments as Description'), DB::raw('SUM(((hours*60)+minutes)/60) as Effort_Hours')])
                        ->where('date', '>=', date('Y-m-d', strtotime('-1 days')))
                        ->where('date', '<=', date('Y-m-d', strtotime('-1 days')))
                        ->leftJoin('timesheets', 'timesheets.submitor_id', '=', 'employees.id')
                        ->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')
                        ->leftJoin('tasks', 'timesheets.task_id', '=', 'tasks.id')
                        ->leftJoin('projects_sprints', 'timesheets.projects_sprint_id', '=', 'projects_sprints.id')
                        ->groupBy('date', 'timesheets.submitor_id', 'timesheets.project_id', 'timesheets.task_id')
                        ->orderBy(DB::raw("STR_TO_DATE(date,'%Y-%m-%d')"), 'desc')
                        ->get()->toArray();
        $existingEmployees = [];

        foreach ($sheet_data as $row) {
            if (!in_array($row['Emp_Code'], $existingEmployees)) {
                $existingEmployees[] = $row['Emp_Code'];
            }
        }
        $employees_No_timesheet = Employee::select('name', 'emp_code')->whereNull('deleted_at')->whereNotIn('emp_code', $existingEmployees)->get()->toArray();

        foreach ($employees_No_timesheet as $ganda_bacha) {
            $sheet_data[] = [
                'Emp_Code' => $ganda_bacha['emp_code'],
                'Date' => date('d M Y', strtotime('-1 days')),
                'Employee' => $ganda_bacha['name'],
                'Project' => '-',
                'Sprint_Name' => '-',
                'Task' => '-',
                'Description' => '-',
                'Effort_Hours' => '-'
            ];
        }

        $file = \Excel::create('Timesheet_' . date('d-M-Y', strtotime('-1 days')), function($excel) use ($sheet_data) {
                    $excel->sheet('Timesheets', function($sheet) use ($sheet_data) {
                        $sheet->fromArray($sheet_data);
                    });
                });
        $file = $file->string('xlsx');
        $attachement = array(
            'name' => 'Timesheet_' . date('d-M-Y', strtotime('-1 days')), //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($file)
        );

        $html = "Dear Ashok Chand,"
                . "<br>"
                . "<br>"
                . "Please find the attached file for timesheet of all members as on " . date('d M Y', strtotime('-1 days'))
                . "<br><br>"
                . "Regards,<br>"
                . "Team Ganit PlusMinus";
        $recipients['to'] = ['ashok.chand@ganitsoft.com'];

        Mail::send('emails.test', ['html' => $html], function ($m) use($recipients, $attachement) {
            $m->attach($attachement['file'], ['as' => $attachement['name'], 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            $m->to($recipients['to'])
                    ->subject('Timesheet Report for  ' . date('d M Y', strtotime('-1 days')));
        });
//        unlink(public_path('exports\\' . $attachement['name'] . '.xls'));
        Log::info(' - Daily Timesheet Report to Ashok Chand sent.');
        return true;
    }

}
