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
        'App\Console\Commands\TimesheetEntryTrackDaily',
        'App\Console\Commands\CompOffCollapse',
        'App\Console\Commands\LeaveRemindLeadsAndManagers',
        'App\Console\Commands\LeaveRemindLeadsAndManagers',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('leave:add')                     //every month on 24th 10 AM
                ->cron('0 10 24 * *');
        $schedule->command('leave:carryForward')            //every 1st Jan 10 AM
                ->cron('0 10 1 1 *');
        $schedule->command('timesheet:dailyReport')         //every day 10 AM
                ->cron('0 10 * * *');
        $schedule->command('timesheet:weekTrack')           //every friday 6 PM
                ->cron('0 18 5 * *');
        $schedule->command('leave:weekTrack')               //every monday 10 AM
                ->cron('0 10 1 * *');
        $schedule->command('compOff:collapse')              //every day 10 AM
                ->cron('0 10 * * *');
        $schedule->command('timesheet:dailyEntryTrack')     //every day 10 AM
                ->cron('0 10 * * *');
        $schedule->command('leave:approvalReminder')        //every monday 10 AM
                ->cron('0 10 1 * *');
        $schedule->command('leave:DailyList')               //every day 10 AM
                ->cron('0 10 * * *');
    }

}
