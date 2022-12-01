<?php

    namespace App\Console;

    use App\Console\Commands\CreateAccount;
    use App\Console\Commands\TestEmails;
    use Carbon\Carbon;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
    use Modules\Qualification\Http\Controllers\ReminderController;

    class Kernel extends ConsoleKernel
    {
        /**
         * The Artisan commands provided by your application.
         *
         * @var array
         */
        protected $commands = [
            Commands\TestEmails::class,
            \Modules\Resilience\Console\Commands\RemindConsultation::class,
            \Modules\Resilience\Console\Commands\RemindOpeningConsultation::class,
            CreateAccount::class,
            Commands\SaveZipContent::class,
        ];

        /**
         * Define the application's command schedule.
         *
         * @param \Illuminate\Console\Scheduling\Schedule $schedule
         * @return void
         */
        protected function schedule(Schedule $schedule)
        {
            $this->qualificationCronCommands($schedule);
            //if (config('constants.QUALIFICATION')) {
            $today = Carbon::now('Europe/Paris')->dayOfWeek;
            $reminderDay1 = config('constants.qual_reminder_day1');
            $reminderDay2 = config('constants.qual_reminder_day2');
            $cronDay = config('constants.qual_cron_day');
            if ($today == $cronDay || $today == $reminderDay1 || $today == $reminderDay2) {
                $this->qualificationReminderCommands($schedule);
            }
            $schedule->command('mail:consultation-reminder')->daily()->withoutOverlapping();
            $schedule->command('mail:consultation-opening-reminder')->daily()->withoutOverlapping();
            //  $this->qualificationCronCommands($schedule);
//            }

            $schedule->command('mail:event-reminders')->daily()->withoutOverlapping();
        }

        /**
         * Register the Closure based commands for the application.
         *
         * @return void
         */
        protected
        function commands()
        {
            require base_path('routes/console.php');
        }

        protected
        function qualificationReminderCommands($schedule)
        {
            $hostnames = Hostname::where('id', 1)->get(); //dd($hostnames);

            $tenancy = app(\Hyn\Tenancy\Environment::class);
//            $reminder = app(\Modules\Qualification\Http\Controllers\ReminderController::class);
            $reminderDay1 = config('constants.qual_reminder_day1');
            $reminderTime1 = config('constants.qual_reminder_time1');
            $reminderDay2 = config('constants.qual_reminder_day2');
            $reminderTime2 = config('constants.qual_reminder_time2');
            $cronDay = config('constants.qual_cron_day');
            $cronTime = config('constants.qual_cron_time');

            foreach ($hostnames as $key => $value) {
                $firstRefferre = $schedule->call('\Modules\Qualification\Http\Controllers\ReminderController@remindersCronReferrer', [$value])->weeklyOn($reminderDay1, $reminderTime1)->timezone('Europe/Paris')->runInBackground();
                $secondRefferre = $schedule->call('\Modules\Qualification\Http\Controllers\ReminderController@remindersCronReferrer', [$value])->weeklyOn($reminderDay2, $reminderTime2)->timezone('Europe/Paris')->runInBackground();

                $third = $schedule->call('\Modules\Qualification\Http\Controllers\ReminderController@remindersCronWeekly', [$value])->weeklyOn($cronDay, $cronTime)->timezone('Europe/Paris')->runInBackground();
            }
        }

        protected
        function qualificationCronCommands($schedule)
        {
            $hostnames = Hostname::where('id', 1)->get(); //dd($hostnames);

            foreach ($hostnames as $key => $value) {
                $dailyCron = $schedule->call('\Modules\Qualification\Http\Controllers\ReminderController@cronMail', [$value])->daily()->timezone('Europe/Paris')->runInBackground();

                $dailyReminder = $schedule->call('\Modules\Qualification\Http\Controllers\ReminderController@remindersCron', [$value])->daily()->timezone('Europe/Paris')->runInBackground();

            }
        }

        protected
        function scheduleTimezone()
        {
            return 'Europe/Paris';
        }
    }
