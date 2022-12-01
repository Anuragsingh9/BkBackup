<?php


namespace Modules\KctAdmin\Services\BusinessServices\factory;


use Illuminate\Database\Eloquent\Builder;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Services\BusinessServices\IAnalyticsService;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\LogEventActionCount;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain all logic related to the analytics feature
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class AnalyticsService
 * @package Modules\KctAdmin\Services\BusinessServices\factory
 */
class AnalyticsService implements IAnalyticsService {

    /**
     * @inheritDoc
     */
    public function fetchAnalyticsData($groupIds, $startDate, $endDate): Builder {
        return Event::with(['organiser', 'eventUsers', 'eventJoinedReport', 'createdBy', 'moments', 'spaces', 'spaces.conversations' => function ($q) {
            $q->withoutGlobalScopes();
        }])->whereHas('group', function ($q) use ($groupIds) {
            $q->whereIn('group_id', $groupIds);
        })->whereDoesntHave('draft', function ($q) {
            $q->where('event_status', 2);
        })->whereBetween('start_time', [$startDate, $endDate]);
    }

    public function getEventTypeBySearchKey($key): int {
        $type = 0;
        if (str_contains('cafeteria', strtolower($key))) {
            $type = Event::$eventType_cafeteria;
        }
        if (str_contains('executive', strtolower($key))) {
            $type = Event::$eventType_executive;
        }
        if (str_contains('manager', strtolower($key))) {
            $type = Event::$eventType_manager;
        }
        return $type;
    }

    /**
     * @param Builder $builder
     * @param $orderBy
     * @param $order
     * @param $keysAlise
     * @return Builder
     */
    public function addSortingToAnalytics(Builder $builder, $orderBy, $order, $keysAlise): Builder {
        switch ($orderBy) {
            case 'event_name':
            case 'event_type':
                $orderQuery = Event::selectRaw("lower($keysAlise[$orderBy])")
                    ->whereColumn('event_single_recurrences.event_uuid', 'events.event_uuid')
                    ->limit(1);
                break;
            case 'zoom_meeting':
            case 'zoom_webinar':
                $orderQuery = Moment::select('moment_type')
                    ->whereColumn('event_single_recurrences.event_uuid', 'event_moments.event_uuid')
                    ->where('moment_type', $keysAlise[$orderBy] == 'is_zoom_meeting' ? Moment::$momentType_webinar : Moment::$momentType_meeting);
                break;
            case 'total_conv_count':
            case 'total_registration':
            case 'total_attendees':
            case 'media_image':
            case 'media_video':
            case 'sh_conv_count':
                $orderQuery = LogEventActionCount::select($keysAlise[$orderBy])
                    ->whereColumn('event_single_recurrences.recurrence_uuid', 'log_event_action_counts.recurrence_uuid')
                    ->orderByRaw($keysAlise[$orderBy])
                    ->limit(1);
                break;
            case 'total_duration':
                $orderQuery = Conversation::selectRaw('SUM(TIME_TO_SEC(end_at)-TIME_TO_SEC(created_at)) as cbount')
                    ->whereHas('space', function ($q) {
                        $q->whereColumn('event_uuid', 'event_single_recurrences.event_uuid');
                    })
                    ->withoutGlobalScopes();
                break;
            default:
                $orderQuery = 'recurrence_date';
        }
        return $builder->orderBy($orderQuery, $order);
    }

}
