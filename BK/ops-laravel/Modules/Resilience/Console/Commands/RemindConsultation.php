<?php

    namespace Modules\Resilience\Console\Commands;

    use App\Setting;
    use App\User;
    use Carbon\Carbon;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Console\Command;
    use Modules\Resilience\Entities\Consultation;
    use Modules\Resilience\Events\ConsultationReminder;
    use Modules\Resilience\Services\ResilienceService;

    class RemindConsultation extends Command
    {

        protected $signature = 'mail:consultation-reminder';

        protected $description = 'Remind users regarding consultations';

        private $tenancy;

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->service = ResilienceService::getInstance();
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle()
        {
            $hostnames = Hostname::whereIn('id', [1, 55, 56, 40])->get();
            foreach ($hostnames as $key => $value) {
                $host = Hostname::find($value->id);
                $hostname = $this->tenancy->hostname($host);
                $reminder = Setting::where('setting_key', "consultation_reminder")->first();
                if (isset($reminder->id)) {
                    $reminders = json_decode($reminder->setting_value);
                    foreach ($reminders->reminders as $key => $reminder) {
                        if ($reminder->active) {
                            $this->reminderCheckOnConsultation($key, $reminder->days, $hostname);
                        }
                    }
                }
            }
        }

        public function reminderCheckOnConsultation($reminderType, $days, $hostname)
        {
            if ($reminderType == "reminder1" || $reminderType == "reminder2") {
                $dateCalc = Carbon::today()->subDays($days)->toDateString();
                $column = 'start_date';
            } else {
                $dateCalc = Carbon::today()->addDays($days)->toDateString();
                $column = 'end_date';
            }
            $consultations = Consultation::with('consultationAnsweredUser')->whereDate($column, $dateCalc)->with('workshop.meta.user')->get();
            $consultations->each(function ($consultation, $key) use ($reminderType, $hostname) {
                $this->mailToParticularConsultation($consultation, $reminderType, $hostname);
            });
        }

        public function mailToParticularConsultation($consultation, $reminderType, $hostname)
        {
            $users = $consultation->workshop->meta->pluck('user')->filter()->unique();
            if ($reminderType == "reminder1") {
                $template = 'resilience_reminder_1';
            } elseif ($reminderType == "reminder2") {
                $template = 'resilience_reminder_2';
            } elseif ($reminderType == "reminder3") {
                $template = 'resilience_reminder_3';
            } elseif ($reminderType == "reminder4") {
                $template = 'resilience_reminder_4';
            }
            $domain = strtok($hostname->fqdn, '.');
            foreach ($users as $user) {
                $answered = collect($consultation->consultationAnsweredUser)->where('consultation_uuid', $consultation->uuid)->where('user_id', $user['id'])->count();
                if (!$answered) {
                    $data = ($this->service->prepareEmailData($user, $consultation->workshop_id, $template, $consultation, $hostname));
                    $params[$consultation->uuid]['name'] = $consultation->name;
                    if ($consultation->is_reinvent) {
                        if (!empty(env('REINVENT_URL'))) {
                            $params[$consultation->uuid]['link'] = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL') . '/#/';
                        } else {
                            $params[$consultation->uuid]['link'] = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions/#/';
                        }
                    } else {
                        $params[$consultation->uuid]['link'] = env('HOST_TYPE') . $hostname->fqdn . '/#/organiser/commissions/' . $consultation->workshop_id . '/resilience/' . $consultation->uuid . '/mini-consultation';
                    }
                    $data['mail']['url'] = $params;
                    event(new ConsultationReminder('email_template.dynamic_workshop_template', $data, $user['email']));
                }
            }
        }
    }
