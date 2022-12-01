<?php


namespace Modules\KctUser\Repositories\factory;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\EventDummyUser;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctUser\Entities\Event;
use Modules\KctUser\Entities\EventUser;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Exceptions\EventLeaveAgainException;
use Modules\KctUser\Repositories\IEventRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will handle the events management
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EventRepository
 * @package Modules\KctUser\Repositories\factory
 */
class EventRepository extends \Modules\KctAdmin\Repositories\factory\EventRepository implements IEventRepository {

    /**
     * @inheritDoc
     */
    public function findParticipant(?string $eventUuid, ?int $userId): ?EventUser {
        return EventUser::where('event_uuid', $eventUuid)->where('user_id', $userId)->first();
    }

    /**
     * @inheritDoc
     */
    public function getEventListBuilder($op, $groupIds = []): Builder {
        $eventUsers = function ($q) {
            $q->where('users.id', Auth::user()->id);
        };
        $notDraftEvents = function ($q) {
            $q->where('event_status', 2);
        };
        $relations = [
            'eventUsers' => $eventUsers, // loading auth user relation with event
            'isHostOfAnySpace',
            'selfUserBanStatus',
            'organiser',
        ];

        if ($op == '>') {
            // future events, fetching the links as well
            $relations[] = 'moderatorMoments';
            $relations[] = 'speakerMoments';
        }

        $builder = Event::with($relations)
            ->whereDoesntHave('draft', $notDraftEvents)
            ->when(count($groupIds) , function($q) use($groupIds) {
                $q->whereHas('group', function($q) use($groupIds){
                    $q->whereIn('groups.id', $groupIds);
                });
            })
            ->where('end_time', $op, Carbon::now()->toDateTimeString());

        if ($op == '<') { // < means past event and in past we need to show only those events in which user is participated
            $builder = $builder->whereHas('eventUsers', $eventUsers);
        }
        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function updateDummyUser($dummyId, $conversationUuid, $eventUuid) {
        EventDummyUser::where('dummy_user_id', $dummyId)
            ->where('event_uuid', $eventUuid)->update(['current_conv_uuid' => $conversationUuid]);
    }

    /**
     * @inheritDoc
     */
    public function findUserActiveEventUuid() {
        $user = request()->user('api');
        return Event::with('spaces')
            ->whereHas('eventUsers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->where('start_time', '<=', Carbon::now()->toDateString())
            ->where('end_time', '>', Carbon::now()->toDateString())
            ->orderBy('start_time', 'desc')
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function findMomentByMomentId(?string $momentId): ?Moment {
        return Moment::where('moment_id', $momentId)->first();
    }

    /**
     * @inheritDoc
     */
    public function getGroupEvents() {
        Event::with('group');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Here event user join or leave log will be created
     * if user join log creating without leaving the previous entry it will automatically close previous entry and
     * create the new join entry
     *
     * if user try to leave without any open entry or if there is already leave then it will throw error as user should
     * not able to leave without having no open entry before
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $userId
     * @param null $leaveOn
     * @return mixed|void
     * @throws EventLeaveAgainException
     */
    public function createEventAttendLog($eventUuid, $userId, $leaveOn = null) {
        $previousRecord = EventUserJoinReport::where('event_uuid', $eventUuid)
            ->where('user_id', $userId)
            ->orderBy('id', "desc")
            ->first();
        $eventRec = $this->findEventLatestRec($eventUuid);
        if ($previousRecord) { // there is already entry for the user
            if ($previousRecord->on_leave) { // checking if entry is already closed
                if ($leaveOn) {
                    throw new EventLeaveAgainException();
                }
                $now = Carbon::now();
                $previous = $previousRecord->on_leave;
                // todo check ip also
                if ($now->diff($previous)->s > 10) {
                    return EventUserJoinReport::create([
                        'event_uuid'      => $eventUuid,
                        'user_id'         => $userId,
                        'recurrence_uuid' => $eventRec ? $eventRec->recurrence_uuid : null,
                    ]);
                } else {
                    $previousRecord->on_leave = null;
                    $previousRecord->update();
                }
            } else { // previous record not closed
                if ($leaveOn) { // leave provided means this request is to close the opened entry
                    $previousRecord->on_leave = Carbon::now();
                    $previousRecord->update();
                } else {
                    // previous entry is not closed and this request is trying to create a new entry
                    // closing the previous entry and creating a new
                    $previousRecord->on_leave = Carbon::now();
                    $previousRecord->update();
                    return EventUserJoinReport::create([
                        'event_uuid' => $eventUuid,
                        'user_id'    => $userId,
                        'recurrence_uuid' => $eventRec ? $eventRec->recurrence_uuid : null,
                    ]);
                }
            }
        } else { // no record for user
            if ($leaveOn) { // as there is no record for previous then user still trying to leave
                throw new EventLeaveAgainException();
            }
            return EventUserJoinReport::create([
                'event_uuid'      => $eventUuid,
                'user_id'         => $userId,
                'recurrence_uuid' => $eventRec ? $eventRec->recurrence_uuid : null,
            ]);
        }
    }
}
