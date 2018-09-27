<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Employee;
use Log;

class CompOffCollapse extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compOff:collapse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collapse comp offs after a month';

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
        $get_collapsable_count = DB::table('comp_off_managements')
                ->whereNull('deleted_at')
                ->where('availed', '<>', '1')
                ->where('start_date', '<', date('Y-m-d', strtotime('-30 days')))
                ->select([DB::raw('SUM((DATEDIFF(end_date,start_date)+1)) as collapsed_days'), 'employee_id'])
                ->groupBy('employee_id')
                ->get();

        foreach ($get_collapsable_count as $record) {
            if ($record->collapsed_days != '') {
                $employee = Employee::find($record->employee_id);
                $comp_off = $employee->comp_off - $record->collapsed_days;
                DB::table('employees')
                        ->where('id', $record->employee_id)
                        ->update(['comp_off' => $comp_off]);
            }
        }

        DB::table('comp_off_managements')
                ->whereNull('deleted_at')
                ->where('start_date', '<', date('Y-m-d', strtotime('-30 days')))
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        Log::info(' - CRON : Comp offs lapsed successfully!');
    }

}
