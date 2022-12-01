<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\KctConference;
use Modules\KctUser\Events\MomentStatusUpdated;
use Modules\KctUser\Events\ZoomMeetingActionUpdated;
use Modules\KctUser\Services\KctCoreService;
use Modules\KctUser\Traits\KctHelper;
use RTCFactory;

class WebHookController extends BaseController {
    use KctHelper;


    /**
     * @deprecated
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This api is deprecated. This was build for testing purpose only.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function handleZoomWebinar(Request $request) {
        $hostname = Hostname::find(1);
        $this->tenancy->hostname($hostname);
        DB::connection('tenant')->table('model_metas')->insert([
            'fields'     => json_encode($request->all()),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * WEBINAR RESPONSE
     *
     * {
     *      "event": "string",
     *      "event_ts": "long",
     *      "payload": {
     *          "account_id": "string",
     *          "object": {
     *              "id": "string",
     *              "uuid": "string",
     *              "host_id": "string",
     *              "topic": "string",
     *              "type": "integer",
     *              "start_time": "string [date-time]",
     *              "timezone": "string",
     *              "duration": "integer"
     *          }
     *      }
     * }
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for update the webinar status
     * This method handle the webinar and meeting states(end, start)
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function updateWebinarStatus(Request $request) {
        try {
            $data = $request->all();
            $this->repo->settingRepository->storeWebhooksLogs($data, 1);
            $id = $request->input('payload')['object']['id'];

            $hostResolve = $this->services->kctService->findAndSetHostnameByMeetingId($id);
            $moment = $hostResolve['moment'];
            $hostname = $hostResolve['hostname'];
            if (!$moment) {
                return;
            }
//            DB::connection('tenant')->beginTransaction();
            $data = false;
            $previous = $moment->is_live;
            switch ($request->input('event')) {
                case 'webinar.started':
                    // webinar has been started
                    $moment->is_live = 1;
                    break;
                case 'webinar.ended':
                    // webinar has been ended by moderator
                    $moment->is_live = 0;
                    break;
                case 'meeting.started':
                    // meeting started
                    $moment->is_live = 1;
                    break;
                case 'meeting.ended':
                    // meeting ended by host
                    $moment->is_live = 0;
                    break;
                default:
                    return;
            }
            $moment->update();
            $currentMoment = $this->services->kctService->getEventCurrentMoment($moment->event);
//            DB::connection('tenant')->commit();
            if (1 || $previous != $moment->is_live && $currentMoment && $moment->id == $currentMoment->id) {
                // moment status has changed from previous status so triggering the event to front end side
                // moment is live currently so emitting the event
                $embeddedUrl = $this->services->kctService->getEmbeddedUrl($moment->event);

                $result = [
                    'eventUuid' => $moment->event_uuid,
                    'namespace' => $this->services->kctService->getNamespaceFromHost($hostname),
                ];
                if ($embeddedUrl) {
                    $result['dataToSend'] = array_merge([
                        'embedded_url' => [
                            'embedded_url'    => $embeddedUrl,
                            'conf_user_name'  => "",
                            'conf_meeting_id' => $moment->moment_id,
                            'conf_user_email' => env("ZM_DEFAULT_APP_EMAIL"),
                            'conf_api_key'    => env('ZM_DEFAULT_APP_KEY'),
                        ],
                    ],
                        ['status' => $moment->is_live]);
                } else {
                    $result['dataToSend'] = ['status' => $moment->is_live];
                }
                event(new MomentStatusUpdated($result));
            }
            return response()->json(['status' => true, 'data' => $data], 201);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for handle during meeting update
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     */
    public function handleDuringMeetingUpdate(Request $request) {
        $this->repo->settingRepository->storeWebhooksLogs($request->all(), 1);
        if (isset($request->input('payload')['object']['id'])) {
            $hostRecord = $this->services->kctService->findAndSetHostnameByMeetingId(
                $request->input('payload')['object']['id']
            );
            event(new ZoomMeetingActionUpdated([
                'user_id'         => $request->input('payload')['object']['participant']['user_id'] ?? null,
                'event'           => $request->input('event'),
                'event_uuid'      => $hostRecord['moment']->event_uuid,
                'moment_id'       => $hostRecord['moment']->id,
                'zoom_meeting_id' => $request->input('payload')['object']['id'],
            ]));
        }
    }


}
