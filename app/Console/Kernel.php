<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\LeaveAdditionCron',
        'App\Console\Commands\LeaveCarryForward',
        'App\Console\Commands\LeaveWeekTrack',
        'App\Console\Commands\TimesheetEmail',
        'App\Console\Commands\TimesheetEntryTrack',
        'App\Console\Commands\CompOffCollapse',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('leave:add')
                ->cron('0 10 24 * *');
        $schedule->command('leave:carryForward')
                ->cron('0 10 1 1 *');
        $schedule->command('timesheet:dailyReport')
                ->cron('0 10 * * *');
        $schedule->command('timesheet:weekTrack')
                ->cron('0 18 5 * *');
        $schedule->command('leave:weekTrack')
                ->cron('0 10 1 * *');
        $schedule->command('compOff:collapse')
                ->cron('0 10 * * *');
    }

}
