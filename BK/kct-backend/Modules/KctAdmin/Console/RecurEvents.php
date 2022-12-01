<?php

namespace Modules\KctAdmin\Console;

use Carbon\Carbon;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Entities\EventRecurrences;
use Modules\KctAdmin\Entities\EventSingleRecurrence;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\ConversationUser;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Events\EventReset;
use When\InvalidStartDate;
use When\Valid;
use When\When;

class RecurEvents extends Command {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recur:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';
    private ?Carbon $todayCarbon;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->todayCarbon = Carbon::now();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return array
     * @throws InvalidStartDate
     */
    public function handle() {

        $webSites = Website::all();

        $result = [];

        foreach ($webSites as $website) {
            $this->adminServices()->superAdminService->setTenant($website);
            DB::connection('tenant')->beginTransaction();

            $events = Event::where('end_time', '<=', Carbon::now()->toDateTimeString())->get();

            foreach ($events as $event) {
                $this->endAllConversations($event);
            }

            $recurrences = EventRecurrences::with('event.spaces.spaceUsers')
                ->whereHas('event')
                ->where('end_date', '>=', $this->todayCarbon->toDateString())
                ->where('start_date', '<=', $this->todayCarbon->toDateString())
                ->get();

            foreach ($recurrences as $recurrence) {
                if (!$recurrence->recurrences_settings) {
                    continue;
                }
                $recStart = Carbon::make($recurrence->start_date);
                $recEnd = Carbon::make($recurrence->end_date);
                $recEnd->addDay(); // adding one day in end date so that recurrence can happen till last date
                $eventEndTime = $this->getEventDateTime($recurrence->event, 'end_time');
                $this->today = Carbon::now();
                switch ($recurrence->recurrence_type) {
                    case 1: // Daily
                        $nextDate = $this->handleDaily($recurrence, $recStart, $recEnd, $eventEndTime);
                        break;
                    case 2: // Weekdays
                    case 3: // Weekly
                        $nextDate = $this->handleWeekly($recurrence, $recStart, $recEnd, $eventEndTime);
                        break;
                    case 5: // Monthly
                        $nextDate = $this->handleMonthly($recurrence, $recStart, $recEnd, $eventEndTime);
                        break;
                    default:
                        $nextDate = $this->todayCarbon;
                }
                if ($nextDate) {
//                    $this->endAllConversations($recurrence->event);
                    $event = $this->updateEvent($recurrence, $nextDate);

                    $this->updateMoments($recurrence->event->moments, $nextDate);
                    $result[] = "$event->title - {$nextDate->toDateString()}";
                }
            }

            $this->updatePermanentEvent();

            DB::connection('tenant')->commit();
        }
        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the latest date from the recurrences this method will iterate through the dates and will pick
     * the date which is either today or closest to today in future
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $r
     * @param $eventEndTime
     * @return mixed|null
     */
    public function getLatestRecurrence($r, $eventEndTime) {
        if (count($r->occurrences)) {
            foreach ($r->occurrences as $occurrence) {
                if ($occurrence->toDateString() >= $this->todayCarbon->toDateString()) {
                    $occurrenceDateTime = $this->getCarbonByDateTime($occurrence->toDateString(), $eventEndTime);
                    if ($occurrenceDateTime <= $this->todayCarbon) {
                        continue;
                    }
                    return $occurrence;
                }
            }
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the daily event recurrence and find the date for next occur
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $recurrence
     * @param $recStart
     * @param $recEnd
     * @param $eventEndTime
     * @return mixed|null
     * @throws InvalidStartDate
     */
    public function handleDaily($recurrence, $recStart, $recEnd, $eventEndTime) {
        $r = new When();
        $r->startDate($recStart)
            ->freq("daily")
            ->interval($recurrence->recurrences_settings['repeat_interval'])
            ->until($recEnd)
            ->generateOccurrences();

        return $this->getLatestRecurrence($r, $eventEndTime);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the recurrence for the weekly type event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $recurrence
     * @param Carbon $recStart
     * @param Carbon $recEnd
     * @param $eventEndTime
     * @return mixed
     * @throws InvalidStartDate
     */
    public function handleWeekly($recurrence, $recStart, $recEnd, $eventEndTime) {
        $r = new When();
        $r->RFC5545_COMPLIANT = When::IGNORE;
        $days = $recurrence->recurrences_settings['weekdays'];
        $days = sprintf("%07d", decbin($days));
        $weekdays = ['mo', 'tu', 'we', 'th', 'fr', 'sa', 'su'];
        $recurDays = [];
        foreach ($weekdays as $n => $day) {
            if ($days[$n] == 1) {
                $n = ($n + 1) % 7;
                $validDay = Valid::$weekDays[$n];
                $recurDays[] = $validDay;
            }
        }
        $r->startDate($recStart)
            ->freq("weekly")
            ->byday($recurDays)
            ->interval($recurrence->recurrences_settings['repeat_interval'])
            ->until($recEnd)
            ->generateOccurrences();
        return $this->getLatestRecurrence($r, $eventEndTime);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the recurrence for month by on date or by (month week number and day)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $recurrence
     * @param $recStart
     * @param $recEnd
     * @param $eventEndTime
     * @return mixed|null
     * @throws InvalidStartDate
     */
    public function handleMonthly($recurrence, $recStart, $recEnd, $eventEndTime) {
        if ($recurrence->recurrences_settings['recurrence_month_type'] == 1) {
            $r = new When();
            $r->RFC5545_COMPLIANT = When::IGNORE;
            $r->startDate($recStart)
                ->freq("monthly")
                ->bymonthday($recurrence->recurrences_settings['month_date'])
                ->interval($recurrence->recurrences_settings['repeat_interval'])
                ->until($recEnd)
                ->generateOccurrences();
            return $this->getLatestRecurrence($r, $eventEndTime);
        } else {
            $monthWeekDay = strtolower(
                substr(
                    $recurrence->recurrences_settings['recurrence_on_month_week_day'], 0, 2
                )
            );
            $monthWeek = $recurrence->recurrences_settings['recurrence_on_month_week'];
            $r = new When();
            $r->RFC5545_COMPLIANT = When::IGNORE;
            $r->startDate($recStart)
                ->freq("monthly")
                ->byday("$monthWeek$monthWeekDay")
                ->interval($recurrence->recurrences_settings['repeat_interval'])
                ->until($recEnd)
                ->generateOccurrences();

//            $this->printR($r);
            return $this->getLatestRecurrence($r, $eventEndTime);
        }
        return null;
    }

    public function printR($r) {
        $result = [];
        foreach ($r->occurrences as $occurrence) {
            $result[] = $occurrence->toDateString() . ' ' . $occurrence->dayName;
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the event timing
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $recurrence
     * @param $nextDate
     * @return mixed
     */
    private function updateEvent($recurrence, $nextDate) {
        $event = $recurrence->event;
        $event->start_time = $this->getCarbonByDateTime($event->start_time)->setDateFrom($nextDate);
        $event->end_time = $this->getCarbonByDateTime($event->end_time)->setDateFrom($nextDate);
        $event->update();

        $recurrence = EventSingleRecurrence::where('event_uuid', $event->event_uuid)
            ->whereDate('recurrence_date', $nextDate->toDateString())->first();
        if (!$recurrence) {
            $r = EventSingleRecurrence::select('recurrence_count')->where('event_uuid', $event->event_uuid)->orderBy('recurrence_count', 'desc')->first();
            EventSingleRecurrence::create([
                'event_uuid'       => $event->event_uuid,
                'recurrence_date'  => $event->start_time,
                'recurrence_count' => $r ? $r->recurrence_count + 1 : 1,
            ]);
        }
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the moments for event with next recurrence date
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $moments
     * @param $nextDate
     */
    private function updateMoments($moments, $nextDate) {
        foreach ($moments as $moment) {
            $moment->start_time = $this->getCarbonByDateTime($moment->start_time)->setDateFrom($nextDate);
            $moment->end_time = $this->getCarbonByDateTime($moment->end_time)->setDateFrom($nextDate);
            $moment->update();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description After event is recurred all the conversation from previous recur must be closed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     */
    private function endAllConversations($event) {
        $spaces = $event->spaces;
        EventSpaceUser::whereIn('space_uuid', $spaces->pluck('space_uuid'))
            ->whereNotNull('current_conversation_uuid')
            ->update(['current_conversation_uuid' => null]);
        $conversations = Conversation::whereIn('space_uuid', $spaces->pluck('space_uuid'))
            ->whereNull('end_at')
            ->pluck('uuid');
        Conversation::whereIn('uuid', $conversations)
            ->update(['end_at' => $event->end_time]);
        ConversationUser::whereIn('conversation_uuid', $conversations)
            ->whereNull('leave_at')
            ->update(['leave_at' => $event->end_time]);
        event(new EventReset(['event_uuid' => $event->event_uuid]));
    }

    public function updatePermanentEvent() {
        $event = Event::where('event_type', Event::$eventType_all_day)->first();
        if(!$event) {
            return;
        }
        $eventRecurrence = EventSingleRecurrence::where('event_uuid', $event->event_uuid)
            ->orderBy('recurrence_count', 'desc')->first();
        $count = $eventRecurrence ? $eventRecurrence->recurrence_count + 1 : 1;

        if (!$eventRecurrence ||
            (
                $eventRecurrence
                && Carbon::make($eventRecurrence->recurrence_date)->toDateString() != Carbon::now()->toDateString()
            )
        ) {
            EventSingleRecurrence::create([
                'event_uuid'       => $event->event_uuid,
                'recurrence_count' => $count,
                'recurrence_date'  => Carbon::now()->setHours(0)->setMinutes(0)->setSeconds(0),
            ]);
        }

        $endTime = Carbon::make($event->end_time);
        if(Carbon::now()->diff($endTime)->days < 10) {
            $event->end_time = Carbon::now()->addDays(100);
            foreach($event->moments as $moment) {
                $moment->start_time = $event->start_time;
                $moment->end_time = $event->end_time;
                $moment->update();
            }
            $event->update();
        }

        EventMeta::updateOrCreate([
            'event_uuid' => $event->event_uuid,
        ], [
            'reg_start_time' => $event->start_time,
            'reg_end_time'   => $event->end_time,
            'event_status'   => EventMeta::$eventStatus_live, // 1 for live. 2 for draft
            'share_agenda'   => 0,
            'is_reg_open'    => EventMeta::$event_regIsOpen, // 0 for close , 1 for open
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array {
        return [
//            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array {
        return [
//            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
