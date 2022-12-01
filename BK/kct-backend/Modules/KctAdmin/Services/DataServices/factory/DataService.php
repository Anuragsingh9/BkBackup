<?php

namespace Modules\KctAdmin\Services\DataServices\factory;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Entities\GroupType;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Services\DataServices\IDataService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Entities\DemoLiveAsset;
use Modules\UserManagement\Entities\Entity;
use Modules\UserManagement\Entities\UserMobile;
use Ramsey\Uuid\Uuid;
use When\InvalidStartDate;
use When\Valid;
use When\When;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage data preparation of specific purpose
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class DataService
 * @package Modules\KctAdmin\Services\DataServices\factory
 */
class DataService implements IDataService {
    use KctHelper;
    use ServicesAndRepo;
    use \Modules\SuperAdmin\Traits\ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;

    private ?Carbon $todayCarbon;

    /**
     * @inheritDoc
     */
    public function prepareEventCreateData($request, $groupId, ?string $imageUrl = null): array {
        $organiser = Auth::user();
        if (!$organiser) {
            throw new Exception('No organiser found');
        }

        $start = $this->getCarbonByDateTime(
            $request->input('date'), $request->input('start_time')
        )->toDateTimeString();
        $end = $this->getCarbonByDateTime(
            $request->input('date'), $request->input('end_time')
        )->toDateTimeString();

        return [
            'title'              => $request->input('title'),
            'start_time'         => $start,
            'end_time'           => $end,
            'image'              => $imageUrl ?: null,
            'created_by_user_id' => Auth::user()->id,
            'security_atr_id'    => 1,
            'join_code'          => $request->input('join_code') ? $request->input('join_code') : $this->autoGenerateJoinCode(),
            'manual_opening'     => 0,
            'description'        => $request->input('description'),
            'type'               => $request->input('type', 1),
            'event_settings'     => [
                'is_dummy_event'     => $request->input('is_dummy_event', 0) ? 1 : 0,
                'is_self_header'     => $request->input('is_self_header', 0) ? 1 : 0,
                'manual_access_code' => Uuid::uuid4(),
            ],
            'group_id'           => $groupId,
            'organiser'          => $organiser,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the data for the event moment for creating moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $start
     * @param $end
     * @return array[]
     */
    public function prepareKeyMomentsForV4Event($request, $start, $end): array {
        $momentNameSuffix = $start->toTimeString() . "-" . $end->toTimeString();

        $moments = [[
            'moment_name'        => $request->input('event_title') . "($momentNameSuffix)",
            'moment_description' => $request->input('event_title') . "($momentNameSuffix)",
            'moment_type'        => Moment::$momentType_networking,
            'start_time'         => $start,
            'end_time'           => $end,
        ]];

        if (($request->event_type == 2 || $request->event_type == 3) && $request->event_broadcasting) {
            $moments[] = [
                'moment_name'        => $request->input('event_title') . "($momentNameSuffix)",
                'moment_description' => $request->input('event_title') . "($momentNameSuffix)",
                'moment_type'        => $request->event_broadcasting == 1
                    ? Moment::$momentType_meeting
                    : Moment::$momentType_webinar,
                'start_time'         => $start,
                'end_time'           => $end,
                'moderator'          => $request->event_moderator,
            ];
        }
        return $moments;
    }

    /**
     * @inheritDoc
     */
    public function prepareEventV4Param($request, $groupId): array {
        $start = $this->getCarbonByDateTime(
            $request->input('event_start_date'), $request->input('event_start_time')
        );
        $end = $this->getCarbonByDateTime(
            $request->input('event_start_date'), $request->input('event_end_time')
        );
        $keyMoments = $this->prepareKeyMomentsForV4Event($request, $start, $end);

        $spaces = [];

        //Preparing the spaces(single or multiple spaces data as per request) data to create space.
        foreach ($request->input('event_spaces') as $space) {
            $spaces[] = [
                'space_name'       => $space['space_line_1'],
                'space_short_name' => Arr::exists($space, 'space_line_2') ? $space['space_line_2'] : '',
                'space_mood'       => __('kctadmin::messages.space_default_name'),
                'max_capacity'     => $space['space_max_capacity'],
                'is_vip_space'     => $space['space_is_vip'] ?? 0,
                'is_duo_space'     => 0,
                'hosts'            => $space['space_host'],
                'order_id'         => config('kctadmin.modelConstants.spaces.defaults.start_order'),
            ];
        }

        $data = [
            'event'   => [
                'title'              => $request->input('event_title'),
                'start_time'         => $start->toDateTimeString(),
                'end_time'           => $end->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id,
                'join_code'          => $request->input('event_custom_link') ? $request->input('event_custom_link') : $this->autoGenerateJoinCode(),
                'description'        => $request->input('event_description'),
                'type'               => $request->input('type', 1),
                'event_settings'     => [
                    'is_dummy_event'           => $request->input('event_is_demo', 0) ? 1 : 0,
                    'is_self_header'           => 0,
                    'manual_access_code'       => Uuid::uuid4(),
                    'is_auto_key_moment_event' => 1,
                    'event_conv_limit'         => $request->input('event_conv_limit', 4),
                    'event_grid_rows'          => $request->input('event_grid_rows', 4),
                ],
                'group_id'           => $groupId,
                'is_mono_type'       => $request->input('event_type') == 1,
                'event_type'         => $request->input('event_type', 1),
            ],
            'spaces'  => $spaces,
            'draft'   => [
                'reg_start_time' => Carbon::now(),
                'reg_end_time'   => $end,
                'event_status'   => $request->input('event_is_published') ?
                    EventMeta::$eventStatus_live : EventMeta::$eventStatus_draft, // 1 for live. 2 for draft
                'share_agenda'   => 0,
                'is_reg_open'    => $request->input('event_is_published') ?
                    EventMeta::$event_regIsOpen : EventMeta::$event_regIsClose,  // 0 for close , 1 for open
            ],
            'moments' => $keyMoments,
        ];
        if ($request->has('event_recurrence')) {
            $recurrence = $request->input('event_recurrence');
            if ($recurrence && $recurrence['rec_type']) {
                $data['recurrence'] = [
                    'start_date'           => $start->toDateString(),
                    'end_date'             => $recurrence['rec_end_date'],
                    'recurrence_type'      => $recurrence['rec_type'],
                    'recurrences_settings' => [
                        'weekdays'                     => $recurrence['rec_weekdays'] ?? 0,
                        'month_date'                   => $recurrence['rec_month_date'] ?? 1,
                        'repeat_interval'              => $recurrence['rec_interval'] ?? 1,
                        'recurrence_month_type'        => $recurrence['rec_month_type'] ?? 1,
                        'recurrence_on_month_week'     => $recurrence['rec_on_month_week'] ?? 1,
                        'recurrence_on_month_week_day' => $recurrence['rec_on_month_week_day'] ?? 'Monday',
                    ],
                ];
                $data['draft']['reg_end_time'] = $recurrence['rec_end_date'] . ' ' . $end->toTimeString();
            }
        }
        return $data;
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to generate the join code string
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string
     */
    public function autoGenerateJoinCode(): string {
        $events = $this->adminRepo()->eventRepository->getAllEvents(true);
        return (count($events) + 1) . $this->generateRandomString(1) . '-' . $this->generateRandomString(6, false);
    }


    /**
     * @inheritDoc
     */
    public function prepareMomentData(Request $request): array {
        $moments = [];
        $start = $this->getCarbonByDateTime(
            $request->input('date'), $request->input('start_time')
        )->toDateTimeString();
        $end = $this->getCarbonByDateTime(
            $request->input('date'), $request->input('end_time')
        )->toDateTimeString();
        if ($request->input('type') == 1) {
            // networking event so creating a moment from start to end
            $moments[] = [
                'moment_name'        => $request->input('title'),
                'moment_description' => $request->input('description'),
                'moment_type'        => Moment::$momentType_networking,
                'start_time'         => $start,
                'end_time'           => $end,
            ];
        }
        return $moments;
    }

    /**
     * @inheritDoc
     */
    public function prepareDummyUsers($space): array {
        $dummyUsers = $this->adminRepo()->eventRepository->getDummyUsers();
        return $this->addDummyUserToSpace($space, $dummyUsers);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the provided number of users in space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $dummies
     * @return array
     */
    public function addDummyUserToSpace($space, $dummies): array {
        $data = [];
        foreach ($dummies as $dummyUser) {
            $data[] = $this->adminRepo()->eventRepository->addDummyUser($dummyUser->id, $space->event_uuid, $space->space_uuid);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function prepareDefaultSpace($request, Event $event, $groupId): array {
        $groupFirstPilot = $this->adminRepo()->groupRepository->getGroupFirstPilot($groupId);
        if (!$groupFirstPilot) {
            throw new Exception('No organiser found');
        }
        return [
            'space_name'       => __('kctadmin::messages.space_default_name'),
            'space_short_name' => null,
            'space_mood'       => __('kctadmin::messages.space_default_name'),
            'max_capacity'     => $request->max_capacity ?? config('kctadmin.modelConstants.spaces.defaults.default_capacity'),
            'is_vip_space'     => 0,
            'is_duo_space'     => 0,
//            'is_mono_space'    => 0, // current event follows only mono space,
            'event_uuid'       => $event->event_uuid,
            'hosts'            => $request->space_host ?? $groupFirstPilot->id,
            // order id to keep space sorted and for default at top for first time
            'order_id'         => config('kctadmin.modelConstants.spaces.defaults.start_order'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareEventUpdateData(Request $request, Event $event, ?string $imageUrl): array {
        $setting = $event->event_settings;

        if ($request->has('is_self_header')) {
            $setting['is_self_header'] = $request->input('is_self_header');
        }
        if ($request->has('is_dummy_event')) {
            $setting['is_dummy_event'] = $request->input('is_dummy_event');
        }
        $draftData = $event->draft;
        $return['event'] = [
            'title'          => $request->input('title'),
            'start_time'     => $this->getCarbonByDateTime($request->input('date'), $request->input('start_time'))->toDateTimeString(),
            'end_time'       => $this->getCarbonByDateTime($request->input('date'), $request->input('end_time'))->toDateTimeString(),
            'manual_opening' => $request->input('manual_opening'),
            'event_settings' => $setting,
            'header_text'    => $request->input('header_text'),
            'header_line_1'  => $request->input('header_line_1', $event->header_line_1),
            'header_line_2'  => $request->input('header_line_2', $event->header_line_2),
            'description'    => $request->input('description'),
            'is_mono_type'   => $request->input('is_mono_event', $event->is_mono_type),
            'join_code'      => $request->input('join_code', $event->join_code)
        ];
        $return['draft'] = [
            'event_status' => isset($draftData) ? $draftData['event_status'] : 1, // 1 for live 2 for draft
            'share_agenda' => isset($draftData) ? $draftData['share_agenda'] : 0,
            'is_reg_open'  => isset($draftData) ? $draftData['is_reg_open'] : 0,
        ];
        return $return;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for space while updating
     * This method will find which space needs to be deleted and added newly and update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return array
     */
    public function prepareV4SpaceData($request, $event) {
        $spaceToCreate = [];
        $spaceToDelete = [];
        $spaceToUpdate = [];

        $defaultSpace = $event->spaces->first();

        $spaceInRequest = [];

        foreach ($request->input('event_spaces') as $space) {
            if (isset($space['space_uuid'])) { // space uuid present to it will be counted as to update
                $temp = [
                    'space_name'       => $space['space_line_1'],
                    'space_short_name' => Arr::exists($space, 'space_line_2') ? $space['space_line_2'] : '',
                    'max_capacity'     => $space['space_max_capacity'],
                    'hosts'            => $space['space_host'],
                    'space_uuid'       => $space['space_uuid'],
                ];

                if ($space['space_uuid'] != $defaultSpace->space_uuid) {
                    $temp['is_vip_space'] = $space['space_is_vip'] ?? 0;
                }

                $spaceInRequest[] = $space['space_uuid'];

                $spaceToUpdate[] = $temp;
            } else {
                $spaceToCreate[] = [
                    'event_uuid'       => $event->event_uuid,
                    'space_name'       => $space['space_line_1'],
                    'space_short_name' => Arr::exists($space, 'space_line_2') ? $space['space_line_2'] : '',
                    'space_mood'       => __('kctadmin::messages.space_default_name'),
                    'max_capacity'     => $space['space_max_capacity'],
                    'is_vip_space'     => $space['space_is_vip'] ?? 0,
                    'is_duo_space'     => 0,
                    'hosts'            => $space['space_host'],
                    'order_id'         => config('kctadmin.modelConstants.spaces.defaults.start_order'),
                ];
            }
        }

        $eventSpaces = $event->spaces->pluck('space_uuid')->toArray();
        $spaceToDelete = array_values(
            array_diff($eventSpaces, [...$spaceInRequest, $defaultSpace->space_uuid])
        );

        return [
            'spaceToCreate' => $spaceToCreate,
            'spaceToDelete' => $spaceToDelete,
            'spaceToUpdate' => $spaceToUpdate
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareV4EventUpdateData(Request $request, Event $event, ?string $imageUrl): array {
        $setting = $event->event_settings;
        $setting = $request->has('event_scenery') ? $this->prepareParamForV4EventScenery($request, $event) : $setting;
        $start = $this->getCarbonByDateTime(
            $request->input('event_start_date'), $request->input('event_start_time')
        );
        $end = $this->getCarbonByDateTime(
            $request->input('event_start_date'), $request->input('event_end_time')
        );
        $momentNameSuffix = $start->toTimeString() . "-" . $end->toTimeString();
        $draftData = $event->draft;
        $endTime = $event->event_type == Event::$eventType_all_day ? Carbon::make($event->end_time) : $this->getCarbonByDateTime(
            $request->input('event_start_date'),
            $request->input('event_end_time')
        );

        $setting['event_conv_limit'] = $request->input('event_conv_limit', $setting['event_conv_limit'] ?? 4);

        if ($request->has('event_is_demo')) {
            $setting['is_dummy_event'] = $request->input('event_is_demo');
        }

        $setting['event_grid_rows'] = $request->input('event_grid_rows', $setting['event_grid_rows'] ?? 4);

        $spaces = $this->prepareV4SpaceData($request, $event);


        // As per the requirement is_dummy_event(which includes dummy users in event) not editable.
//        $setting['is_dummy_event'] = $request->input('event_is_demo', $setting['is_dummy_event']);

        // preparing event data
        $return['event'] = [
            'title'          => $request->input('event_title', $event->title),
            'start_time'     => $request->has('event_start_time') && $event->type != Event::$eventType_all_day
                ? $this->getCarbonByDateTime(
                    $request->input('event_start_date'),
                    $request->input('event_start_time')
                )->toDateTimeString()
                : $event->start_time,
            'end_time'       => $request->has('event_end_time') && $event->type != Event::$eventType_all_day
                ? $endTime->toDateTimeString()
                : $event->end_time,
            'description'    => $request->input('event_description', $event->description),
            'join_code'      => (
                $draftData->event_status === EventMeta::$eventStatus_draft
                || $event->event_type === Event::$eventType_all_day
            )
                ? $request->input('event_custom_link', $event->join_code)
                : $event->join_code,
            'event_settings' => $setting,
        ];
        //prepare scenery data
        $return['event_scenery'] = $setting;
        //prepare recurrence data
        $return['recurrence'] = $this->prepareRecurringUpdateData($request, $event);
        // prepare space data
        $return['spaces'] = $spaces;
        //prepare moment data
        $return['moment'] = [
            'moment_description' => $request->input('event_title') . "($momentNameSuffix)",
            'moment_name'        => $request->input('event_title') . "($momentNameSuffix)",
        ];
        // prepare draft event data
        // if event is already published then updating the time only
        if ($draftData['event_status'] == 1) {
            $return['draft'] = [
                'reg_end_time' => $event->eventRecurrenceData && $event->eventRecurrenceData->recurrence_type
                    ? $event->eventRecurrenceData->end_date . " " . $endTime->toTimeString()
                    : $endTime,
            ];
        } else {
            $return['draft'] = [
                'event_status'   => $request->input('event_is_published') ? EventMeta::$eventStatus_live :
                    EventMeta::$eventStatus_draft,
                'share_agenda'   => 0,
                'is_reg_open'    => $request->input('event_is_published', $draftData['is_reg_open']),
                'reg_start_time' => Carbon::now(),
                'reg_end_time'   => $event->eventRecurrenceData && $event->eventRecurrenceData->recurrence_type
                    ? $event->eventRecurrenceData->end_date . " " . $endTime->toTimeString()
                    : $endTime,
            ];

        }
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function spaceCreateParam(Request $request): array {
        return [
            'space_name'                => $request->input('space_name'),
            'space_short_name'          => $request->input('space_short_name'),
            'space_mood'                => $request->input('space_mood'),
            'max_capacity'              => $request->input('max_capacity'),
            'is_vip_space'              => $request->input('space_type')
            == config('kctadmin.modelConstants.spaces.values.space_type_vip')
                ? 1
                : 0,
            'is_duo_space'              => 0,
//            'is_mono_space'             => $request->input('space_type')
//                                              == config('kctadmin.modelConstants.spaces.values.space_type_mono')
//                                              ? 1
//                                              : 0,
            'event_uuid'                => $request->input('event_uuid'),
            'follow_main_opening_hours' => 1,
            'order_id'                  => 'b',
            'opening_hours'             => [
                'after'  => $request->input('opening_hours_after'),
                'before' => $request->input('opening_hours_before'),
                'during' => $request->input('opening_hours_during', 1),
            ],
            'hosts'                     => $this->mainHostForEvent($request),
        ];
    }

    /**
     * @inheritDoc
     */
    public function spaceUpdateParam($request, $space): array {
        return [
            'spaceData'  => [
                'space_name'       => $request->space_name ?: $space->space_name,
                'space_short_name' => $request->space_short_name,
                'space_mood'       => $request->space_mood ?: $space->space_mood,
                'max_capacity'     => $request->max_capacity ?: $space->max_capacity,
                'is_vip_space'     => $request->input('space_type') == config('kctadmin.modelConstants.spaces.values.space_type_vip') ? 1 : 0,
            ],
            'spaceHosts' => $request->input('hosts'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareGroupCreateData($request): array {
        return [
            'name'        => $request->name,
            'short_name'  => $request->short_name,
            'description' => $request->description,
            'settings'    => [],
            'is_default'  => 0,
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareUserCreateData(array $inputUser): array {
        $userData = [
            'fname' => $inputUser['fname'],
            'lname' => $inputUser['lname'],
            'email' => $inputUser['email'],

            'city'              => $inputUser['city'] ?? null,
            'country'           => $inputUser['country'] ?? null,
            'address'           => $inputUser['address'] ?? null,
            'postal'            => $inputUser['postal'] ?? null,
            'internal_id'       => $inputUser['internal_id'] ?? null,
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'gender'            => $inputUser['gender'] ?? null ? strtolower($inputUser['gender'][0]) : null,
        ];

        if (isset($inputUser['company']) || isset($inputUser['company_id'])) {
            if (isset($inputUser['company_id'])) {
                $userData['company'] = [
                    'id'       => $inputUser['company_id'],
                    'position' => $inputUser['company_position'],
                ];
            } else if (isset($inputUser['company'])) {
                $userData['company'] = [
                    'long_name'      => $inputUser['company'],
                    'entity_type_id' => Entity::$type_companyType,
                    'position'       => $inputUser['company_position'],
                ];
            }
        }
        if (isset($inputUser['union'])) {
            if (isset($inputUser['union_id'])) {
                $userData['union'] = [
                    'id'       => $inputUser['union_id'],
                    'position' => $inputUser['union_position'],
                ];
            } else if (isset($inputUser['union'])) {
                $userData['union'] = [
                    'long_name'      => $inputUser['union'],
                    'entity_type_id' => Entity::$type_unionType,
                    'position'       => $inputUser['union_position'],
                ];
            }
        }

        if (isset($inputUser['phones'])) {
            foreach ($inputUser['phones'] as $phone) {
                $userData['phones'][] = [
                    'country_code' => $phone['country_code'] ?? null,
                    'number'       => $phone['number'],
                ];
            }
        }
        if (isset($inputUser['mobiles'])) {
            foreach ($inputUser['mobiles'] as $phone) {
                $userData['mobiles'][] = [
                    'country_code' => $phone['country_code'] ?? null,
                    'number'       => $phone['number'],
                ];
            }
        }
        return $userData;
    }

    /**
     * @inheritDoc
     */
    public function prepareUserUpdateData($request): array {
        return [
            'user'         => [
                'fname'       => $request->fname,
                'lname'       => $request->lname,
                'internal_id' => $request->input('internal_id'),
                'email'       => $request->email,
                'gender'      => $request->gender ? strtolower($request->gender[0]) : null,
            ],
            'user_phones'  => [
                'country_code' => $request->input('phone_code'),
                'number'       => $request->input('phone_number'),
                'is_primary'   => 1,
                'type'         => UserMobile::$type_landLine,
            ],
            'user_mobiles' => [
                'country_code' => $request->input('mobile_code'),
                'number'       => $request->input('mobile_number'),
                'type'         => UserMobile::$type_mobile,
                'is_primary'   => 1,
            ],
            'company'      => $this->prepareCompanyData($request),
            'unions'       => $this->prepareUnionsData($request),
            'grade'        => $request->grade ? $request->grade : null,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data to insert for the company
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array|null
     */
    private function prepareCompanyData(Request $request): ?array {
        $data = null;
        if ($request->input('company_id')) { // request has company_id means company exists
            $data = [
                'id'       => $request->input('company_id'),
                'position' => $request->input('c_position'),
            ];
        } else if ($request->input('company_name')) { // request has company_name means user wants to add a new one
            $data = [
                'long_name'      => $request->input('company_name'),
                'entity_type_id' => Entity::$type_companyType,
                'position'       => $request->input('c_position'),
            ];
        }
        return $data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data to insert for the unions
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array|null
     */
    private function prepareUnionsData(Request $request): ?array {
        if ($request->input('unions')) {
            $unions = [];
            foreach ($request->input('unions') as $union) {
                $u = null;
                if (isset($union['union_id'])) {
                    $u = ['id' => $union['union_id']];
                } else if (isset($union['union_name'])) {
                    $u = [
                        'long_name'      => $union['union_name'],
                        'entity_type_id' => Entity::$type_unionType,
                    ];
                }
                if (isset($union['union_old_id']) && $u) { // added $u check to handle if user only send old id
                    $u['old_entity_id'] = $union['union_old_id'];
                }
                if ($u) {
                    $u['position'] = $union['position'] ?? null;
                    $unions[] = $u;
                }
            }
            return $unions;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function prepareUserRoleUpdate($request): array {
        $role = null;
        $userRoleData = [
            'user_id'  => $request->user_id,
            'presence' => 2,
        ];
        if ($request->role == 'team') {
            $role = 1;
        } elseif ($request->role == 'expert') {
            $role = 2;
        }
        $userRoleData['event_user_role'] = $role;
        $role = $request->input('role') == 'vip' ? 1 : 0;
        $userRoleData['is_vip'] = $role;
        return $userRoleData;
    }

    /**
     * @inheritDoc
     */
    public function prepareMultiUserRoleUpdate($userId, $inputRole): array {
        $role = null;
        $userRoleData = [
            'user_id'  => $userId,
            'presence' => 2,
        ];
        if ($inputRole == 1) { // checking if team
            $role = 1;
        } elseif ($inputRole == 2) { // checking if expert
            $role = 2;
        }
        $userRoleData['event_user_role'] = $role;
        $role = $inputRole == 3 ? 1 : 0; // checking if vip
        $userRoleData['is_vip'] = $role;
        return $userRoleData;
    }

    /**
     * @inheritDoc
     */
    public function mainHostForEvent($request) {
        return $this->adminServices()->userService->getUsersById($request->input('hosts', []));
    }

    /**
     * @inheritDoc
     */
    public function filterParticipantsByKey($request, $users) {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($request->event_uuid);
        switch ($request->key) {
            case "vip":
                return $this->filterVIPUsers($users);
            case "team":
                return $this->filterTeamUsers($users);
            case "expert":
                return $this->filterExpertUsers($users);
            case "space_host":
                return $this->filterSHUsers($users, $event);
            default :
                return $this->filterAttendee($users, $request, $event);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To filter the temporary user from event user list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $users
     * @return array
     */
    public function filterTeamUsers($users): array {
        return collect($users)->where('event_user_role', 1)->all();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To filter the expert user from event user list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $users
     * @return array
     */
    public function filterExpertUsers($users): array {
        return collect($users)->where('event_user_role', 2)->all();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To filter the VIP user from event user list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $users
     * @return array
     */
    public function filterVIPUsers($users): array {
        return collect($users)->where('is_vip', 1)->all();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To filter the attendee user from event user list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $users
     * @param $request
     * @param $event
     * @return array
     */
    public function filterAttendee($users, $request, $event): array {
        $moderators = $this->adminRepo()->eventRepository->getEventUsers($request->event_uuid, 'moderator')->pluck('user_id')->toArray();
        $speakers = $this->adminRepo()->eventRepository->getEventUsers($request->event_uuid, 'speaker')->pluck('user_id')->toArray();
        $hosts = $this->getSpaceHosts($event);
        return collect($users)
            ->where('event_user_role', null)
            ->where('is_vip', 0)
            ->whereNotIn('user_id', $speakers)
            ->whereNotIn('user_id', $moderators)
            ->whereNotIn('user_id', $hosts)
            ->all();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To filter space hosts from event user list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $users
     * @param $event
     * @return mixed
     */
    public function filterSHUsers($users, $event) {
        $spaces = $event->spaces->pluck('space_uuid');
        $hostUsersId = $this->getSpaceHosts($event);
        return $users->load(['hostSpaces' => function ($q) use ($spaces, $hostUsersId) {
            $q->whereIn('hostable_uuid', $spaces);
        }])->whereIn('user_id', $hostUsersId);
    }

    /**
     * @inheritDoc
     */
    public function prepareDraftEventData($event, $draft): array {
        // if request->draft is equal to 0 means event is networking type & needs to be published directly,
        $eventStatus = $draft ? 2 : 1;
        return [
            'event_uuid'     => $event->event_uuid,
            'reg_start_time' => $event->start_time,
            'reg_end_time'   => $event->end_time,
            'event_status'   => $eventStatus, // 1 for live. 2 for draft
            'share_agenda'   => 0,
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareDraftEventUpdateData($request, ?EventMeta $draftStatus = null, $event): array {
        if ($request->has('auto_manage')) { // To auto open the registration window and make event publish for V4
            $currentDate = Carbon::now();
            // get the registration start date
            $regStart = $this->getCarbonByDateTime($currentDate->toDateString(), $currentDate->toTimeString())->toDateTimeString();
            // get the registration end date
            $regEnd = $event->end_time;
            $draftStatus->is_reg_open = 1;
        } else {
            // get the registration start date
            $regStart = $this->getCarbonByDateTime(
                $request->input('reg_start_date'), $request->input('reg_start_time')
            )->toDateTimeString();
            // get the registration end date
            $regEnd = $this->getCarbonByDateTime(
                $request->input('reg_end_date'), $request->input('reg_end_time')
            )->toDateTimeString();
        }
        return [
            'reg_start_time' => $regStart,
            'reg_end_time'   => $regEnd,
            // check in request if not found check in draft if not found set default as draft
            'event_status'   => $request->input('event_status', $draftStatus
                ? $draftStatus->event_status
                : EventMeta::$eventStatus_draft),
            'share_agenda'   => $request->input('share_agenda', $draftStatus ? $draftStatus->share_agenda : 0),
            'is_reg_open'    => $request->input('is_reg_open', $draftStatus ? $draftStatus->is_reg_open : 0),
        ];
    }

    /**
     * @inheritDoc
     */
    public function publishEventData($request, $event) {
        $currentDate = Carbon::now();
        // get the registration start date
        $regStart = $this->getCarbonByDateTime($currentDate->toDateString(), $currentDate->toTimeString())->toDateTimeString();
        // get the registration end date
        $regEnd = $event->end_time;
        return [
            'event_uuid'     => $event->event_uuid,
            'reg_start_time' => $regStart,
            'reg_end_time'   => $regEnd,
            'share_agenda'   => $request->input('share_agenda') ?? 0,
            'event_status'   => 1,
            'is_reg_open'    => 1,
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareGroupUpdateData($request, $group): array {

        if (!$group->mainSetting && $group->is_default) { // here setting the main settings for default group(super_group)
            $mainSetting = $this->prepareMainSettingForDefaultGrp();
            $this->adminRepo()->settingRepository->setSetting($group->id, 'main_setting', $mainSetting);
            $groupTypeId = $this->adminRepo()->groupRepository->getGroupTypeId('super_group');
            $this->adminRepo()->groupRepository->createGroupTypeRelation($group->id, $groupTypeId);
        }
        // load the main setting, group type and pilots data with group data
        $groupData = $group->load('mainSetting', 'groupType', 'pilots');
        $groupSetting = $groupData->mainSetting->setting_value;
        $oldGroupType = GroupType::where('group_type', $groupData->groupType['group_type'])->first();
        $currentGroupType = GroupType::where('group_type', $request->input('group_type'))->first();
        $dataToUpdate['group'] = [
            'name'        => $request->has('group_name') ? $request->input('group_name') : $group->name,
            'description' => $request->has('description') ? $request->input('description') : $group->description,
            'group_key'   => $request->has('new_group_key') ? $request->input('new_group_key') : $group->group_key,
        ];
        $dataToUpdate['main_settings'] = [
            'allow_user'                => $request->input('allow_user', $groupSetting['allow_user']),
            'allow_manage_pilots_owner' => $request->input('allow_manage_pilots_owner', $groupSetting['allow_manage_pilots_owner']),
            'allow_design_setting'      => $request->input('allow_design_setting', $groupSetting['allow_design_setting']),
            'type_value'                => $request->input('type_value', $groupSetting['type_value'])
        ];

        $dataToUpdate['group_type_id'] = $request->has('group_type') ? $currentGroupType->id : $oldGroupType->id;

        $dataToUpdate['group_pilots'] = $request->input('pilot_id');

        return $dataToUpdate;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This group is used to prepare main setting for default group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function prepareMainSettingForDefaultGrp(): array {
        return [
            'allow_user'                => 1,
            'allow_design_setting'      => 1,
            'allow_manage_pilots_owner' => 1,
            'type_value'                => null
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareDataForInviteEmail($request, $event): array {
        $eventTeam = [];
        $attendeeAndVIP = [];
        // if the request have event team member
        if ($request->event_team) {
            if ($request->space_host) {
                $event->load('spaces', 'spaces.hosts');
                // preparing space hosts data
                foreach ($event->spaces as $space) {
                    $spaceHosts[] = $space->hosts[0]['id'];
                }
                $eventTeam = array_merge($eventTeam, $spaceHosts);
            }
            if ($request->team_member) {
                $teamMembers = $this->adminRepo()->eventRepository->getEventTeamMembersId($event)->toArray();
                $eventTeam = array_merge($eventTeam, $teamMembers);
            }
            if ($request->expert_member) {
                $expertMembers = $this->adminRepo()->eventRepository->getEventExpertMembersId($event)->toArray();
                $eventTeam = array_merge($eventTeam, $expertMembers);
            }
            if ($request->speaker) {
                $speaker = $this->adminRepo()->eventRepository->getEventSpeakerId($event)->toArray();
                $eventTeam = array_merge($eventTeam, $speaker);
            }
            if ($request->moderator) {
                $moderator = $this->adminRepo()->eventRepository->getEventModeratorId($event)->toArray();
                $eventTeam = array_merge($eventTeam, $moderator);
            }
        }
        if ($request->attendee) {
            if ($request->participant) {
                $participants = $this->adminRepo()->eventRepository->getEventParticipantsId($event)->toArray();
                $attendeeAndVIP = array_merge($attendeeAndVIP, $participants);
            }
            if ($request->vip_member) {
                $vip = $this->adminRepo()->eventRepository->getEventVIPMembersId($event)->toArray();
                $attendeeAndVIP = array_merge($attendeeAndVIP, $vip);
            }
        }

        return [
            'event_team'       => $eventTeam,
            // excluding the space host as they are already included in event_team member above
            'attendee_and_vip' => array_diff($attendeeAndVIP, $eventTeam),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEventLivePageData($oldEvent, $targetEvent) {
        $settings = $targetEvent->event_settings;
        $oldSettings = $oldEvent->event_settings;
        $oldImages = $oldSettings['event_images'] ?? [];
        $oldVideo = $oldSettings['event_video_links'] ?? [];
        $settings['event_images'] = $oldImages;
        $settings['event_video_links'] = $oldVideo;
        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataForEventScenery($request, $event) {
        $settings = $event->event_settings;
        $bgColor = $this->rgbaToHex(json_decode($request->top_background_color, JSON_OBJECT_AS_ARRAY));
        $newSceneryData = [
            'asset_id'          => $request->asset_id,
            'category_type_id'  => $request->category_type_id,
            'top_bg_color'      => $bgColor,
            'component_opacity' => $request->component_opacity,
        ];
        if ($request->has('asset_color')) {
            $newSceneryData['asset_color'] =
                $this->rgbaToHex(json_decode($request->input('asset_color'), JSON_OBJECT_AS_ARRAY));
        }
        $settings['event_scenery'] = array_merge($settings['event_scenery'] ?? [], $newSceneryData);
        return $settings;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will prepare the data for event scenery section(V4)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return mixed
     */
    public function prepareParamForV4EventScenery($request, $event) {
        $settings = $event->event_settings;

        $bgColor = $this->rgbaToHex(json_decode($request->input('event_top_bg_color') ?? [], JSON_OBJECT_AS_ARRAY));
        $newSceneryData = [
            'asset_id'          => $request->input('event_scenery') ? ($request->input('event_scenery') == 1 ? 1 : ($request->input('event_scenery_asset') ?? null)) : null,
            'category_type_id'  => $request->input('event_scenery') ?? null,
            'top_bg_color'      => $bgColor,
            'component_opacity' => $request->input('event_component_op') ?? null,
        ];
        if (isset($sceneryData['asset_color'])) {
            $newSceneryData['asset_color'] =
                $this->rgbaToHex(json_decode($request->input['asset_color'], JSON_OBJECT_AS_ARRAY));
        }
        $settings['event_scenery'] = array_merge($settings['event_scenery'] ?? [], $newSceneryData);
        return $settings;
    }


    public function prepareParamForEventScenery($request, $event) {
        $sceneryData = $request->input('scenery_data', []);
        $settings = $event->event_settings;
        $settings['is_self_header'] = $request->input('is_self_header', $settings['is_self_header']);

        $bgColor = $this->rgbaToHex(json_decode($sceneryData['top_background_color'] ?? [], JSON_OBJECT_AS_ARRAY));
        $newSceneryData = [
            'asset_id'          => $sceneryData['asset_id'] ?? null,
            'category_type_id'  => $sceneryData['category_type_id'] ?? null,
            'top_bg_color'      => $bgColor,
            'component_opacity' => $sceneryData['component_opacity'] ?? null,
        ];
        if (isset($sceneryData['asset_color'])) {
            $newSceneryData['asset_color'] =
                $this->rgbaToHex(json_decode($sceneryData['asset_color'], JSON_OBJECT_AS_ARRAY));
        }
        $settings['event_scenery'] = array_merge($settings['event_scenery'] ?? [], $newSceneryData);
        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function fetchEventSceneryData($eventUuid, bool $sendAssetUrl = false): array {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($eventUuid);
        $settings = $event->event_settings;
        $eventScenery = $settings['event_scenery'] ?? [];
        $result = [];
        // if the setting have event scenery then prepare the scenery data
        // Like asset id, colors, opacity, asset color, asset path, scenery category
        if ($eventScenery) {
            $asset = $this->adminServices()->superAdminService->getEventSceneryData($eventScenery['asset_id']);
            $bgColor = $this->hexToRgba($eventScenery['top_bg_color']);
            $result = [
                'asset_id'             => $asset ? $asset->id : null,
                'top_background_color' => $bgColor,
                'component_opacity'    => $eventScenery['component_opacity'],
            ];
            if ($color = $eventScenery['asset_color'] ?? null) {
                $result['asset_color'] = $color ? $this->hexToRgba($color) : null;
            }
            if ($sendAssetUrl) { // checking if image(asset) url needs to be send or not.
                $result['asset_path'] = $asset && $asset->asset_path
                    ? $this->adminServices()->fileService->getFileUrl($asset->asset_path)
                    : null;
                $result['asset_color'] = $bgColor;
                $result['category_type'] = $asset ? $asset->asset_type : 0;
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function applyPaginationOnUsers($userIds, ?string $orderBy, ?string $order, $isPaginated, $rowPerPage) {
        $userData = $this->adminServices()->userService->getUsersInOrder($userIds, $orderBy, $order);
        if ($isPaginated) {
            return $userData->paginate($rowPerPage);
        }
        return $userData->get();
    }

    /**
     * @inheritDoc
     */
    public function prepareRecurringEventData($request, $event): array {
        $startDate = $this->getCarbonByDateTime($event->start_time);
        $eventRecurrence = $request->input('event_recurrence', []);
        $data = [
            'event_uuid'      => $event->event_uuid,
            'start_date'      => $startDate->toDateString(),
            'end_date'        => $request->recurrence_end_date,
            'recurrence_type' => $eventRecurrence['recurrence_type'] ?? 0,
        ];
        $data['recurrences_settings']['weekdays'] = $eventRecurrence['rec_weekdays'] ?? 0;
        $data['recurrences_settings']['month_date'] = isset($eventRecurrence['recurrence_ondays'])
            ? [$eventRecurrence['recurrence_ondays']]
            : [];
        $data['recurrences_settings']['repeat_interval'] = $eventRecurrence['recurrence_interval'] ?? 1;
        $data['recurrences_settings']['recurrence_month_type'] = $eventRecurrence['recurrence_month_type'] ?? 1;
        $data['recurrences_settings']['recurrence_on_month_week'] = $eventRecurrence['recurrence_on_month_week'] ?? 1;
        $data['recurrences_settings']['recurrence_on_month_week_day'] = $eventRecurrence['recurrence_on_month_week_day'] ?? 'Monday';
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function prepareRecurringUpdateData($request, $event): array {
        $startDate = $this->getCarbonByDateTime($event->start_time);
        $recurrence = $event->load('eventRecurrenceData');
        $recurrenceData = $recurrence->eventRecurrenceData;
        $eventRecurrence = $request->input('event_recurrence', []);
        $data = [
            'event_uuid'      => $event->event_uuid,
            'start_date'      => $startDate->toDateString(),
            'end_date'        => $eventRecurrence['rec_end_date'] ?? $recurrence->eventRecurrenceData->end_date,
            // for water fountain event no recurrence is allowed
            'recurrence_type' => $event->type == Event::$eventType_all_day ? 0 : ($eventRecurrence['rec_type'] ?? 0),
        ];

        $recurrenceSetting = $recurrenceData->recurrences_settings ?? [
                'weekdays'                     => 0,
                'month_date'                   => 1,
                'repeat_interval'              => 1,
                'recurrence_month_type'        => 1,
                'recurrence_on_month_week'     => 1,
                'recurrence_on_month_week_day' => 'Monday',
            ];

        if ($request->input('event_recurrence') != null) {

            $data['recurrences_settings']['weekdays'] = $eventRecurrence['rec_weekdays']
                ?? $recurrenceSetting['weekdays'];
            $data['recurrences_settings']['month_date'] = $eventRecurrence['rec_month_date']
                ?? $recurrenceSetting['month_date'];
            $data['recurrences_settings']['repeat_interval'] = $eventRecurrence['rec_interval']
                ?? $recurrenceSetting['repeat_interval'];
            $data['recurrences_settings']['recurrence_month_type'] = $eventRecurrence['rec_month_type']
                ?? ($recurrenceSetting['recurrence_month_type'] ?? 1);
            $data['recurrences_settings']['recurrence_on_month_week'] = $eventRecurrence['rec_on_month_week']
                ?? ($recurrenceSetting['recurrence_on_month_week'] ?? 1);
            $data['recurrences_settings']['recurrence_on_month_week_day'] = $eventRecurrence['rec_on_month_week_day']
                ?? ($recurrenceSetting['recurrence_on_month_week_day'] ?? 'Monday');
        } else {
            $data['recurrences_settings'] = $recurrenceSetting;
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function copyDemoLiveImages($event): array {
        $settings = $event->event_settings;
        $demoAssets = DemoLiveAsset::whereAssetType(3)->get(); // 3. image(asset type)
        $settings['event_images'] = []; // making event_images empty so that only demo images are stored
        foreach ($demoAssets as $asset) {
            $uuid = $this->generateUuid();
            $imgUrl = $this->suServices()->fileService->getFileUrl($asset->asset_path);
            $imgUrlInfo = pathinfo($imgUrl);
            $originalName = $imgUrlInfo['filename'];
            $filename = $originalName . '.' . $imgUrlInfo['extension'];
            $thumbnailFile = $originalName . '_thumb.jpg';
            $folder = config('kctadmin.constants.storage_paths.live_event_image');
            $imageValidation = config('kctadmin.modelConstants.event_live_image');
            // path of the actual image
            $path = "$folder/$event->event_uuid/$filename";
            $thumbnailPath = "$folder/$event->event_uuid/$thumbnailFile";
            // resizing the actual image
            $image = Image::make($imgUrl)
                ->resize($imageValidation['max_width'], $imageValidation['max_height'])->stream();
            $this->adminServices()->fileService->storeFile($image->__toString(), $path);
            // resizing the image for thumbnail image
            $image = Image::make($imgUrl)
                ->resize($imageValidation['thumbnail_max_width'], $imageValidation['thumbnail_max_height'])->stream();
            $this->adminServices()->fileService->storeFile($image->__toString(), $thumbnailPath);
            $settings['event_images'][] = [
                'key'            => $uuid,
                'path'           => $path,
                'thumbnail_path' => $thumbnailPath,
            ];
        }
        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function copyDemoLiveVideos($event): array {
        $settings = $event->event_settings;
        $demoAssets = DemoLiveAsset::whereIn('asset_type', [1, 2])->get(); // 1. YouTube link 2. Vimeo link
        $settings['event_video_links'] = []; // making event_video_links empty so that only demo videos are stored
        foreach ($demoAssets as $asset) {
            $uuid = $this->generateUuid();
            $path = $asset->asset_path; // video link
            $targetId = $asset->asset_type == 1 ?
                $this->getYoutubeIdByUrl($asset->asset_path) : $this->getVimeoIdByUrl($asset->asset_path);
            $folder = config('kctadmin.constants.storage_paths.live_event_video_thumbnails');
            $fqdn = $this->umServices()->tenantService->getFqdn();
            $uploadPath = "$fqdn/$folder/$event->event_uuid/$uuid.jpg";
            $thumbnailUrl = $this->getVideoThumbnailUrl($asset->asset_type, $targetId);
            $this->adminServices()->fileService->uploadImageByUrl($thumbnailUrl, $uploadPath);
            $settings['event_video_links'][] = [
                'key'            => $uuid,
                'value'          => $path,
                'video_type'     => $asset->asset_type,
                'thumbnail_path' => $uploadPath,
            ];
        }
        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function getEventCount($event, $eventUuid) {
        $this->todayCarbon = Carbon::now();

        $recStart = Carbon::make($event->eventRecurrenceData->start_date);
        $recEnd = Carbon::make($event->eventRecurrenceData->end_date);
        $eventEndTime = $this->getEventDateTime($event->eventRecurrenceData->event, 'end_time');

        //Count the event recurrence based on event recurrence type
        switch ($event->eventRecurrenceData->recurrence_type) {
            case 1: // Daily
                $eventCount = $this->handleDaily($event->eventRecurrenceData, $recStart, $recEnd, $eventEndTime);
                break;
            case 2: // Weekdays
            case 3: // Weekly
                $eventCount = $this->handleWeekly($event->eventRecurrenceData, $recStart, $recEnd, $eventEndTime);
                break;
            case 5: // Monthly
                $eventCount = $this->handleMonthly($event->eventRecurrenceData, $recStart, $recEnd, $eventEndTime);
                break;
            default:
                $eventCount = $this->todayCarbon;
        }
        return $eventCount;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event recurrence count
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $r
     * @param $eventEndTime
     * @return int
     */
    public function getEventRecurrenceCount($r, $eventEndTime): int {
        $count = 0;
        if (count($r->occurrences)) {
            foreach ($r->occurrences as $occurrence) {
                $occurrenceDateTime = $this->getCarbonByDateTime($occurrence->toDateString(), $eventEndTime);
                // If current time is less than the event today occurrence time then increase the event count
                if ($occurrenceDateTime < $this->todayCarbon) {
                    $count += 1;
                }
            }
        }
        return $count;
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
     * @return int
     * @throws InvalidStartDate
     */
    public function handleDaily($recurrence, $recStart, $recEnd, $eventEndTime): int {
        $r = new When();
        $r->startDate($recStart)
            ->freq("daily")
            ->interval($recurrence->recurrences_settings['repeat_interval'])
            ->until($recEnd)
            ->generateOccurrences();

        return $this->getEventRecurrenceCount($r, $eventEndTime);
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
     * @return int
     * @throws InvalidStartDate
     */
    public function handleWeekly($recurrence, $recStart, $recEnd, $eventEndTime): int {
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
        return $this->getEventRecurrenceCount($r, $eventEndTime);
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
     * @return int
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
            return $this->getEventRecurrenceCount($r, $eventEndTime);
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

            return $this->getEventRecurrenceCount($r, $eventEndTime);
        }
    }
}
