<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class LeaveCarryForward extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:carryForward';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carry Forward leave on 1st Jan';

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
            if ($employee->available_leaves > 6) {
                Employee::find($employee->id)->update(['available_leaves' => '6']);
            }
        }
        
        $this->info('Leaves Forwarded successfully!');
    }

}
