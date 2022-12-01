<?php

namespace Modules\KctAdmin\Http\Controllers\V4;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Http\Controllers\V1\BaseController;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\V4\EventUsersResource;

class UserV4Controller extends BaseController {
    use ServicesAndRepo;

    /**
     * @OA\Get(
     *  path="/api/v4/admin/users",
     *  operationId="getEventUsers",
     *  tags={"V4 Event"},
     *  summary="To fetch events users list(V4)",
     *  description="To fetch event users list",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Parameter(name="event_participants",in="query",
     *     description="This key is used for get event participants<br>
     *          if send This key blank then it fetches the event team users(team,expert,space_host,speaker, moderator, organisers)<br>
     *          if send in this key 1, then it fetches the event participant users",
     *     required=false),
     * @OA\Response(
     *      response=200,
     *      description="Event participants details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",ref="#/components/schemas/ParticipantResource"),
     *      ),
     *   ),
     * @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     * @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     * @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will fetch the event users according to the requested data.
     * 1. By default, this fetch the event participant user(VIP user, participant user)
     * 2. If request have event_team key then it fetches the event team users(speaker, moderator, organisers, expert, team)
     * 3. If request has key and it has any value then it will search event user according to the key given.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getEventUsers(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => ['required', new EventRule],
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            $rowPerPage = $request->input('row_per_page', 10);
            if ($request->has('event_participants')) {// if request have to fetch event team users
                if ($request->order_by == 'lname' || $request->order_by == 'email') {
                    $users = $this->repo->eventRepository->getEventUserInOrderBy(
                        $request->input('event_uuid'),
                        $rowPerPage,
                        $request->pagination,
                        $request->key,
                        $request->order_by,
                        $request->order
                    );
                } elseif ($request->order_by == 'company') {
                    $this->repo->eventRepository->getEventUserInOrderByComp(
                        $request->input('event_uuid'),
                        $rowPerPage,
                        $request->pagination,
                        $request->key,
                        $request->order_by,
                        $request->order
                    );
                } elseif ($request->order_by == 'registration') {
                    $users = $this->repo->eventRepository->getParticipantUsers(
                        $request->input('event_uuid'),
                        $rowPerPage,
                        $request->pagination,
                        $request->key,
                        'is_joined_after_reg',
                        $request->order
                    );
                }
//                $users = $this->repo->eventRepository->getParticipantUsers(
//                    $request->input('event_uuid'),
//                    $rowPerPage,
//                    $request->pagination,
//                    $request->key,
//                );
            } else {
                $users = $this->repo->eventRepository->getEventTeamUsers(
                    $request->input('event_uuid'),
                    $rowPerPage,
                    $request->pagination,
                    $request->key,
                );
            }

            $event = $this->adminRepo()->eventRepository->findByEventUuid($request->input('event_uuid'));
            $event->load('eventRecurrenceData');
            $eventCount = 0;
            // Check if the event is recurrence event or not, if yes then count the event recurrence
            if($event->eventRecurrenceData){
                $eventCount = $this->services->dataFactory->getEventCount($event,$request->input('event_uuid'));
            }
            $meta['events_count']  = $eventCount;
            $usersData = $this->handleDataPagination($users,$request->pagination,$rowPerPage);
            return EventUsersResource::collection($usersData)->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

}
