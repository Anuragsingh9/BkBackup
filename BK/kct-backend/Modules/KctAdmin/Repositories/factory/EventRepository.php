<?php

namespace Modules\KctAdmin\Repositories\factory;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventDummyUser;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Entities\EventRecurrences;
use Modules\KctAdmin\Entities\EventSingleRecurrence;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Entities\EventUserRole;
use Modules\KctAdmin\Entities\GroupEvent;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Entities\Hostable;
use Modules\KctAdmin\Entities\Space;
use Modules\KctAdmin\Repositories\IEventRepository;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\UserManagement\Entities\DummyUser;
use Modules\UserManagement\Entities\Entity;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage event related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class EventRepository implements IEventRepository {
    use KctHelper;
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function create($param): Event {
        $groupId = $param['group_id'];
        unset($param['group_id']);
        $event = Event::create($param);
        $this->attachEventGroup($event->event_uuid, $groupId);
        EventSingleRecurrence::create([
            'event_uuid'       => $event->event_uuid,
            'recurrence_count' => 1,
            'recurrence_date'  => $event->start_time,
        ]);
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function attachEventGroup($eventUuid, $groupId): ?GroupEvent {
        return GroupEvent::create([
            'group_id'   => $groupId,
            'event_uuid' => $eventUuid,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function addUserToEvent($eventUuid, $userId, $spaceUuid = null, array $roles = []): ?EventUser {
        // create the event users
        $eventUser = EventUser::firstOrCreate([
            'event_uuid' => $eventUuid,
            'user_id'    => $userId,
        ], [
            'event_user_role'     => $roles['event_role'] ?? null,
            'state'               => 1,
            'presence'            => isset($roles['is_organiser']) ? 2 : 1,// organiser present already
            'is_organiser'        => Arr::exists($roles, 'is_organiser') ? $roles['is_organiser'] : 0,
            'is_joined_after_reg' => 0, // as after adding user, they should go through qss
        ]);
        // find the event for add user in space
        $event = Event::with('spaces')->find($eventUuid);
        $groupEvent = $event->load('group');
        $groupId = $groupEvent->group->id;
        $space = $spaceUuid ? $event->spaces->where('space_uuid', $spaceUuid)->first() : null;
        $space = $space ?: $event->spaces()->orderBy('created_at')->first();
        EventSpaceUser::updateOrCreate(
            ['space_uuid' => $space->space_uuid, 'user_id' => $userId],
            ['role' => isset($roles['is_host']) && $roles['is_host'] ? EventSpaceUser::$ROLE_HOST : EventspaceUser::$ROLE_MEMBER]);
        if (isset($roles['is_moderator'])) {
            EventUserRole::updateOrCreate([
                'event_user_id' => $eventUser->id,
                'role'          => EventUserRole::$role_moderator,
                'moment_id'     => $roles['is_moderator'],
            ]);
        } else if (isset($roles['remove_as_moderator'])) {
            EventUserRole::where([
                'event_user_id' => $eventUser->id,
                'role'          => EventUserRole::$role_moderator,
                'moment_id'     => $roles['remove_as_moderator'],
            ])->delete();
        }
        if (isset($roles['is_speaker'])) {
            EventUserRole::updateOrCreate([
                'event_user_id' => $eventUser->id,
                'role'          => EventUserRole::$role_speaker,
                'moment_id'     => $roles['is_speaker'],
            ]);
        }
        // adding user to group if not added
        GroupUser::firstOrCreate([
            'group_id' => $groupId,
            'user_id'  => $userId
        ], [
            'role' => GroupUser::$role_User
        ]);
        return $eventUser;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function addUserAndMarkRegistered($request, $user){
        $this->addUserToEvent($request->event_uuid, $user->id);
        $user->load('eventUser');
        $user->eventUser->is_joined_after_reg = 1; // marking user as registered into the event
        $updated = $user->eventUser->update();
        if (!$updated) {
            throw new Exception();
        }
    }

    /**
     * @inheritDoc
     */
    public function findByEventUuid($event_uuid, $relation = []): ?Event {
        if (count($relation)) {
            return Event::with($relation)->find($event_uuid);
        }
        return Event::where('event_uuid', $event_uuid)->first();
    }

    public function findEventLatestRec($eventUuid) {
        return EventSingleRecurrence::where('event_uuid', $eventUuid)->orderBy('recurrence_count', 'desc')->first();
    }

    /**
     * @inheritDoc
     */
    public function updateEvent(Event $event, array $data) {
        Event::where('event_uuid', $event->event_uuid)->update($data);
        EventSingleRecurrence::where('event_uuid', $event->event_uuid)
            ->where('recurrence_date', $event->start_time)->update(['recurrence_date' => $data['start_time']]);
        return $event->refresh();
    }

    /**
     * @inheritDoc
     */
    public function getEvents($tense = null, $limit = 10, $eventUuid = null, $isPaginated = false, $groupId = 1) {
        $today = Carbon::now();
        // if tense is future operator is greater so event with date {{GREATER}} than today will be fetched
        $tenseOperator = $tense == 'past' ? '<' : '>=';
        if ($eventUuid) { // fetching only draft events
            $builder = Event::with(['organiser', 'createdBy'])
                ->where(function ($q) use ($tenseOperator, $today) {
                    $q->where('end_time', $tenseOperator, $today->toDateTimeString());
                    $q->orWhereHas('eventRecurrenceData', function ($q) use ($tenseOperator, $today) {
                        $q->where('end_date', $tenseOperator, $today->toDateString());
                    });
                })
                ->where('event_type', '!=', Event::$eventType_all_day)
                ->whereIn('event_uuid', $eventUuid)
                ->orderBy('start_time', $tense == 'past' ? 'desc' : 'asc')
                ->limit($limit);
        } else {
            // else then fetch the future and past events
            $builder = Event::with(['organiser', 'createdBy'])
                ->whereDoesntHave('draft', function ($q) {
                    $q->where('event_status', 2);
                })
                ->whereHas('group', function ($q) use ($groupId) {
                    $q->where('group_id', $groupId);
                })
                ->where(function ($q) use ($tenseOperator, $today, $tense) {
                    $q->where('end_time', $tenseOperator, $today->toDateTimeString());
                    if ($tense === "future") {
                        $q->orWhereHas('eventRecurrenceData', function ($q) use ($tenseOperator, $today) {
                            $q->where('end_date', $tenseOperator, $today->toDateString());
                        });
                    } else {
                        $q->where(function ($q) use ($tenseOperator, $today) {
                            $q->whereDoesntHave('eventRecurrenceData');
                            $q->orWhereHas('eventRecurrenceData', function ($q) use ($tenseOperator, $today) {
                                $q->where('end_date', $tenseOperator, $today->toDateString());
                            });
                        });
                    }
                })
                ->where('event_type', '!=', Event::$eventType_all_day)
                ->orderBy('start_time', $tense == 'past' ? 'desc' : 'asc')
                ->limit($limit);
        }

        if ($isPaginated) {
            return $builder->paginate($limit);
        }
        return $builder->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllEvents($trashed = false) {
        return $trashed ? Event::withTrashed()->get() : Event::all();
    }

    /**
     * @inheritDoc
     */
    public function getGroupDraftEvents(int $limit = 10, bool $isPaginated = false, int $groupId = 1) {
        $data = Event::with(['organiser', 'createdBy'])
            ->whereHas('draft', function ($q) {
                $q->where('event_status', 2);
            })->whereHas('group', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })->limit($limit);
        if ($isPaginated) {
            return $data->paginate($limit);
        }
        return $data->get();
    }

    /**
     * @inheritDoc
     */
    public function getEventSpaces(string $eventUuid): Collection {
        return Space::where('event_uuid', $eventUuid)->orderBy('created_at')->get();
    }

    /**
     * @inheritDoc
     */
    public function updateSpaceHosts($space, $hosts) {
        if ($hosts && count($hosts) > 0) {
            Hostable::updateOrCreate([
                'hostable_uuid' => $space->space_uuid,
            ], [
                'host_id'       => $hosts[0],
                'hostable_type' => Space::class,
            ]);
        }
        $spaceHost = EventSpaceUser::where('space_uuid', $space->space_uuid)->where('role', 1)->first();
        if ($spaceHost->user_id != $hosts[0]) { // checking if old space host id and requested  host id is not same
            $oldSpaceHost = $spaceHost->user_id;
            $spaceHost->update(['user_id' => $hosts[0]]);
            // Adding old space host as a participants in the same space
            $this->addUserToEvent($space->event_uuid, $oldSpaceHost, $space->space_uuid, []);
        }
        $eventUser = EventUser::where([
            'event_uuid' => $space->event_uuid,
            'user_id'    => $hosts[0],
        ])->first();

        if (!$eventUser) {
            $this->addUserToEvent($space->event_uuid, $hosts[0], $space->space_uuid, ['is_host' => $hosts[0]]);
        }
    }

    public function removeAsUserFromSpace(Event $event, int $userId) {
        return EventSpaceUser::whereIn('space_uuid', $event->spaces->pluck('space_uuid'))
            ->where('role', EventSpaceUser::$ROLE_MEMBER)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function getEventUsers(?string $eventUuid, $role = null): Collection {
        if ($role == 'moderator' || $role == 'speaker') {
            return EventUser::with('user', 'user.company', 'user.unions')
                ->whereHas('eventUserRole', function ($q) use ($role) {
                    $q->where('role', $role == 'moderator'
                        ? EventUserRole::$role_moderator
                        : EventUserRole::$role_speaker
                    );
                })
                ->where('event_uuid', $eventUuid)
                ->get();
        }
        return EventUser::with('user', 'user.company', 'user.unions')
            ->where('event_uuid', $eventUuid)
            ->get();
    }

    /**
     * @param $eventUuid
     * @param $rowPerPage
     * @param $isPaginated
     * @param $key
     * @param $orderBy
     * @param $order
     * @return Builder
     */
    public function getEventUserInOrderBy($eventUuid, $rowPerPage, $isPaginated, $key, $orderBy, $order) {
        $eventUserQuery = function ($q) use ($eventUuid, $key) {
            $q->where('event_uuid', $eventUuid);
            $q->with(['eventUserJoinReport' => function ($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            }, 'isHost'                     => function ($q) use ($eventUuid) {
                $q->whereHas('space', function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                });
            }]);
            $q->whereDoesntHave('isHost', function ($q) use ($eventUuid) {
                $q->whereHas('space', function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                });
            });
            $q->whereNull('event_user_role')
                ->where('is_organiser', 0)
                ->whereDoesntHave('eventUserRole', function ($q) {
                    $q->whereIn('role', [EventUserRole::$role_moderator, EventUserRole::$role_speaker]);
                });
            $q->where(function ($q) use ($key) {
                $q->orWhereHas('user', function ($q) use ($key) {
                    $q->where('fname', 'like', "%$key%");
                    $q->orWhere('lname', 'like', "%$key%");
                    $q->orWhere('email', 'like', "%$key%");
                });
                $q->orWhereHas('user.company', function ($q) use ($key) {
                    $q->where('long_name', 'like', "%$key%");
                });
            });
        };
        return User::with(['company', 'eventUser' => $eventUserQuery])
            ->whereHas('eventUser', $eventUserQuery)->orderBy($orderBy, $order);
    }

    public function getEventUserInOrderByComp($eventUuid, $rowPerPage, $isPaginated, $key, $orderBy, $order) {
        Entity::whereHas()
//        with(['entityUsersRelation.entityUser','entityUsersRelation.entityUser.eventUser' => function ($q) use ($eventUuid,$key) {
//            $q->where('event_uuid', $eventUuid);
//            $q->with(['eventUserJoinReport' => function ($q) use ($eventUuid) {
//                $q->where('event_uuid', $eventUuid);
//            }]);
//            $q->whereDoesntHave('isHost', function ($q) use ($eventUuid) {
//                $q->whereHas('space', function ($q) use ($eventUuid) {
//                    $q->where('event_uuid', $eventUuid);
//                });
//            });
//            $q->whereNull('event_user_role')
//                ->where('is_organiser', 0)
//                ->whereDoesntHave('eventUserRole', function ($q) {
//                    $q->whereIn('role', [EventUserRole::$role_moderator, EventUserRole::$role_speaker]);
//                });
//            $q->where(function ($q) use ($key) {
//                $q->orWhereHas('user', function ($q) use ($key) {
//                    $q->where('fname', 'like', "%$key%");
//                    $q->orWhere('lname', 'like', "%$key%");
//                    $q->orWhere('email', 'like', "%$key%");
//                });
//                $q->orWhereHas('user.company', function ($q) use ($key){
//                    $q->where('long_name', 'like', "%$key%");
//                });
//            });
//        }])
//            ->whereHas('entityUsersRelation.entityUser.eventUser',

//        )
            ->where('entity_type_id', 1)
            ->orderBy('long_name', 'desc')->get()->dd();
    }

    /**
     * @inheritDoc
     */
    public function getParticipantUsers(string $eventUuid, $rowPerPage, $isPaginated, $key, $orderBy, $order) {
        return EventUser::with(['eventUserJoinReport' => function ($q) use ($eventUuid) {
            $q->where('event_uuid', $eventUuid);
        }])
            ->where('event_uuid', $eventUuid)
            ->whereDoesntHave('isHost', function ($q) use ($eventUuid) {
                $q->whereHas('space', function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                });
            })
            ->whereNull('event_user_role')
            ->where('is_organiser', 0)
            ->whereDoesntHave('eventUserRole', function ($q) {
                $q->whereIn('role', [EventUserRole::$role_moderator, EventUserRole::$role_speaker]);
            })->where(function ($q) use ($key) {
                $q->orWhereHas('user', function ($q) use ($key) {
                    $q->where('fname', 'like', "%$key%");
                    $q->orWhere('lname', 'like', "%$key%");
                    $q->orWhere('email', 'like', "%$key%");
                });
                $q->orWhereHas('user.company', function ($q) use ($key) {
                    $q->where('long_name', 'like', "%$key%");
                });
            })->orderBy($orderBy, $order);
//        if ($isPaginated) {
//            return $userData->paginate($rowPerPage);
//        }
//        return $userData->get();
    }

    /**
     * @inheritDoc
     */
    public function getEventTeamUsers(string $eventUuid, $rowPerPage, $isPaginated, $key) {
        return EventUser::with(['isHost' => function ($q) use ($eventUuid) {
            $q->whereHas('space', function ($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            });
        }, 'eventUserRole'               => function ($q) {
            $q->whereIn('role', [EventUserRole::$role_moderator, EventUserRole::$role_speaker]);
        }, 'eventUserJoinReport'         => function ($q) use ($eventUuid) {
            $q->where('event_uuid', $eventUuid);
            $q->selectRaw('event_uuid, user_id, DATE(created_at)');
            $q->groupBy(DB::raw('event_uuid, user_id, DATE(created_at)'));
        }])
            ->where('event_uuid', $eventUuid)
            ->where(function ($q) use ($eventUuid) {
                //fetching space host
                $q->orWhereHas('isHost', function ($q) use ($eventUuid) {
                    $q->whereHas('space', function ($q) use ($eventUuid) {
                        $q->where('event_uuid', $eventUuid);
                    });
                });
                $q->orWhereIn('event_user_role', [EventUser::$team_member, EventUser::$expert_member]);
                $q->orWhereHas('eventUserRole', function ($q) {
                    $q->whereIn('role', [EventUserRole::$role_moderator, EventUserRole::$role_speaker]);
                });
                $q->orWhere('is_organiser', 1);
            })->where(function ($q) use ($key) {
                $q->orWhereHas('user', function ($q) use ($key) {
                    $q->where('fname', 'like', "%$key%");
                    $q->orWhere('lname', 'like', "%$key%");
                    $q->orWhere('email', 'like', "%$key%");
                });
                $q->orWhereHas('user.company', function ($q) use ($key) {
                    $q->where('long_name', 'like', "%$key%");
                });
            });
//        if ($isPaginated) {
//            return $userData->paginate($rowPerPage);
//        }
//        return $userData->get();
    }

    /**
     * @inheritDoc
     */
    public function isDuplicateEvent($start_time, $title): ?Event {
        return Event::where('start_time', ' = ', $start_time)->where('title', $title)->first();
    }

    /**
     * @inheritDoc
     */
    public function getDummyUsers(): Collection {
        return DummyUser::all();
    }

    /**
     * @inheritDoc
     */
    public function addDummyUser($dummyId, $eventUuid, $spaceUuid): ?EventDummyUser {
        return EventDummyUser::create([
            'event_uuid'    => $eventUuid,
            'space_uuid'    => $spaceUuid,
            'dummy_user_id' => $dummyId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findDummyRelationBySpaceUuid($spaceUuid, $dummyId) {
        return EventDummyUser::with('conversation')
            ->where('space_uuid', $spaceUuid)
            ->where('dummy_user_id', $dummyId)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function findDummyRelation($eventUuid, $dummyId) {
        return EventDummyUser::with('conversation')
            ->where('event_uuid', $eventUuid)
            ->where('dummy_user_id', $dummyId)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function makeEventAsDraft($param) {
        return EventMeta::create($param);
    }

    /**
     * @inheritDoc
     */
    public function getDraftEvents() {
        return EventMeta::where(function ($q) {
            $q->where('event_status', 2);
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function findDraftEvent($event_uuid) {
        return EventMeta::where('event_uuid', $event_uuid)->where('event_status', 2)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreateDraft($eventUuid, $dataToUpdate) {
        return EventMeta::UpdateOrCreate(
            ['event_uuid' => $eventUuid],
            $dataToUpdate
        );
    }

    /**
     * @inheritDoc
     */
    public function storeEventAutoKeyMoment($value, $eventUuid): bool {
        $event = $this->findByEventUuid($eventUuid);
        $eventSetting = $event->event_settings;
        $eventSetting['is_auto_key_moment_event'] = $value;
        return $event->update(['event_settings' => $eventSetting]);
    }

    /**
     * @inheritDoc
     */
    public function getEventTeamMembersId($event) {
        return EventUser::whereEventUuid($event->event_uuid)
            ->whereEventUserRole(EventUser::$team_member)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEventExpertMembersId($event) {
        return EventUser::whereEventUuid($event->event_uuid)
            ->whereEventUserRole(EventUser::$expert_member)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEventSpeakerId($event) {
        return EventUser::whereEventUuid($event->event_uuid)->whereIsPresenter(1)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEventModeratorId($event) {
        return EventUser::whereEventUuid($event->event_uuid)->whereIsModerator(1)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEventParticipantsId($event) {
        return EventUser::whereEventUuid($event->event_uuid)->whereEventUserRole(NULL)
            ->whereIsVip(0)->whereIsPresenter(0)->whereIsModerator(0)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEventVIPMembersId($event) {
        return EventUser::whereEventUuid($event->event_uuid)->whereIsVip(1)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function makeEventRecurring($param) {
        return EventRecurrences::create($param);
    }

    /**
     * @inheritDoc
     */
    public function getEventByJoinCode($joinCode, array $excludingEventsId = [], $allowPast = false) {
        return Event::where('join_code', $joinCode)
            ->where(function ($q) use ($allowPast) {
                if (!$allowPast) {
                    $q->orWhere('end_time', '>=', Carbon::now());
                    $q->orWhereHas('eventRecurrenceData', function ($q) {
                        $q->where('end_date', '>=', Carbon::now()->toDateString());
                    });
                }
            })
            ->whereNotIn('event_uuid', $excludingEventsId)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateEventRecurringData($param, $eventUuid) {
        return EventRecurrences::updateOrCreate(['event_uuid' => $eventUuid,], $param);
    }

    /**
     * @inheritDoc
     */
    public function findEventGroup($eventUuid) {
        $event = $this->findByEventUuid($eventUuid);
        $groupEvent = $event->load('group');
        return $groupEvent->group;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Builder method to prepare the builder by adding the search key related queries
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Builder $q
     * @param $key // Key to search
     */
    private function analyticsSearchBuilder(Builder &$q, $key) {
        $q->where(function (Builder $q) use ($key) {

            // call back method to prepare the query by checking if date is well formatted and compare the prepared
            // date with provided expected format and put search like expected format prepared data
            $when = function ($checkFormat, $expectedFormat, $whereCheck = false) use ($key, &$q) {
                $q->when($this->isTimeValid($checkFormat, $key), function ($q) use ($key, $checkFormat, $expectedFormat, $whereCheck) {
                    switch ($whereCheck) {
                        case 'month':
                            $carbonDateYear = $this->getDateFormat($checkFormat, $key, $expectedFormat);
                            $q->orWhereMonth('recurrence_date', '=', $carbonDateYear);
                            break;
                        case 'day':
                            $q->orWhereDay('recurrence_date', '=', $key);
                            break;
                        case 'year':
                            $q->orWhereYear('recurrence_date', '=', $key);
                            break;
                        default:
                            $carbonDateYear = $this->getDateFormat($checkFormat, $key, $expectedFormat);
                            $q->orWhere('recurrence_date', 'like', "%$carbonDateYear%");
                    }
                });
            };

            // for "9 sept 2022" (date month year)
            $when('j M Y', 'Y-m-d');
            $when('j M', 'm-d');
            $when('M', 'm', 'month');
            $when('m', null, 'day');
            $when('m', null, 'year');

            // This query is used for event title and event type
            $q->orWhereHas('event', function ($q) use ($key) {
                $type = $this->adminServices()->analyticsService->getEventTypeBySearchKey($key);
                $q->where('title', 'like', "%$key%");
                $q->orWhere('event_type', 'like', $type);
            });
        });
    }

    /**
     * @inheritDoc
     */
    public function getEventFromRecurrence($groupIds, $startDate, $endDate, ?string $key, $allowAllDay = false) {
        // convert the key into the event type
        return EventSingleRecurrence::with(['userJoinReports', 'actionLog', 'event.moments', 'event.spaces.conversations' => function ($q) use ($key) {
            $q->withoutGlobalScopes();
            // This relation is used for engagement (users count->2, 3, 4, total duration, count of conversation)
        }, 'eventConversationLog.conversationSubState'                                                                    => function ($q) {
            $q->selectRaw('users_count,COUNT(id) AS convo_count,SUM(duration) AS convo_duration,convo_log_id');
            $q->groupBy(DB::raw('users_count,convo_log_id'));
            $q->where('duration', '>', 10);
            // This relation is used for average duration
        }, 'eventConversationLog.conversationSubStateForDuration'                                                         => function ($q) {
            $q->where('duration', '>', 10);
            // This relation is used for space host conversation with respect to users(2, 3,4)
        }, 'eventConversationLog.conversation'                                                                            => function ($q) {
            $q->withoutGlobalScopes();
            // This relation is used for attendance for the event users
        }, 'event.eventJoinedReport'])
            ->whereHas('actionLog', function ($q) use ($groupIds) {
                if ($groupIds) {
                    $q->whereIn('group_id', $groupIds);
                }
            })
            // where use for event according to start and end date
            ->where(function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereDate('recurrence_date', '>=', $startDate);
                    $q->whereDate('recurrence_date', '<=', $endDate);
                }
            })

            // where use for event according to searching
            ->when($key, function (Builder $q) use ($key) {
                $this->analyticsSearchBuilder($q, $key);
            })
            ->whereDate('recurrence_date', '<=', Carbon::today()->toDateString())
            ->whereHas('event', function ($q) use ($allowAllDay) {
                if (!$allowAllDay) {
                    $q->where('event_type', '!=', Event::$eventType_all_day);
                }
            });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the date format for searching the date
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $format
     * @param $key
     * @param $expectedFormat
     * @return string
     */
    public function getDateFormat($format, $key, $expectedFormat): string {
        return Carbon::createFromFormat($format, $key)
            ? Carbon::createFromFormat($format, $key)->format($expectedFormat)
            : '';
    }

    public function findByType(int $type) {
        return Event::where('event_type', $type)->first();
    }
}
