<?php


namespace Modules\Events\Service;


use App\Services\Service;
use App\Setting;
use Carbon\Carbon;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Services\Factory\MailableEmailFactory;
use Modules\Events\Entities\Event;

class EmailService extends Service {
    
    /**
     * @var Environment
     */
    private $tenancy;
    
    public function __construct() {
        $this->tenancy = app(Environment::class);
    }
    
    /**
     * @var MailableEmailFactory
     */
    private $mailFactory;
    
    private function getMailFactory() {
        if (!$this->mailFactory) {
            $this->mailFactory = app()->make(MailableEmailFactory::class);
        }
        return $this->mailFactory;
    }
    
    
    public function sendReminderEmails() {
        $hostnames = Hostname::whereIn('id', config('events.reminder_enabled_domain'))->get();
        foreach ($hostnames as $key => $value) {
            $host = Hostname::find($value->id);
            $hostname = $this->tenancy->hostname($host);
            $setting = Setting::where('setting_key', config('cocktail.setting_keys.reminder_settings'))->first();
            if ($setting) {
                $decode = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
                if (isset($decode['reminders'])) {
                    $reminders = $decode['reminders'];
                } else {
                    return null;
                }
                foreach ($reminders as $reminderKey => $reminderSetting) {  // as we are string the each reminder setting with same name of key used for template in setting key
                    // reminderKey now have setting key used for respective reminder key of its email template
                    // reminder setting have the setting like active and days
                    if (isset($reminderSetting['active']) && $reminderSetting['active']) {
                        $eventType = $this->findReminderKeyBelongsEventType($reminderKey);
                        $this->sendReminderToEventsByDay($reminderKey, $reminderSetting['days'], $eventType, $hostname);
                    }
                }
                return $decode;
            }
        }
        return null;
    }
    
    /**
     * To send the event by finding the events after day provided
     *
     * @param $reminderKey
     * @param $days
     * @param $eventType
     * @param Hostname $hostname
     */
    public function sendReminderToEventsByDay($reminderKey, $days, $eventType, $hostname) {
        $dateOnProvidedDay = Carbon::now()->addDays($days);
        $events = Event::with([
            'workshop',
            'workshop.meta' => function ($q) {
                $q->select('id', 'workshop_id', 'user_id', DB::raw('COUNT(user_id)'));
                $q->groupBy('user_id');
            },
            'workshop.meta.user',
            'workshop.meetings'
        ])->where('type', $eventType)
            ->where('date', $dateOnProvidedDay->toDateString())
            ->get();
        $this->getMailFactory();
        foreach ($events as $event) {
            $users = $events->first()->workshop->meta->pluck('user');
            $tags = EventService::getInstance()->prepareEmailTags($event, null, $hostname);
            $this->getMailFactory()->sendReminderEmailToEvent($reminderKey, $tags, $users);
        }
        
    }
    
    /**
     * As we storing which reminder setting keys belongs to which type of event
     *
     * so this will check the provided key present in which type of events key array
     *
     * @param $key
     * @return string|null
     */
    private function findReminderKeyBelongsEventType($key) {
        $configKeys = config('cocktail.setting_keys');
        if (in_array($key, $configKeys['virtual_event_emails_key'])) {
            return config('events.event_type.virtual');
        } else if (in_array($key, $configKeys['internal_event_emails_key'])) {
            return config('events.event_type.int');
        }
        return null;
    }
}
