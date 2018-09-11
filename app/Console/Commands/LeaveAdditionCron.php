<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class LeaveAdditionCron extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:leave';

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
        $employees = Employee::get();

        foreach ($employees as $employee) {
            if ($employee->is_confirmed) {
                $leave = $employee->total_leaves + 2;
            } else {
                $leave = $employee->total_leaves + 1.5;
            }
            Employee::find($employee->id)->update(['total_leaves' => $leave]);
        }
        $this->info('Leaves added successfully!');
    }

}
