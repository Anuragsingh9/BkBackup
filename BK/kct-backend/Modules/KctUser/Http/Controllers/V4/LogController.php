<?php

namespace Modules\KctUser\Http\Controllers\V4;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\KctAdmin\Entities\EventSingleRecurrence;
use Modules\KctUser\Entities\LogEventContent;
use Modules\KctUser\Exceptions\EventLeaveAgainException;
use Modules\KctUser\Http\Controllers\V1\BaseController;
use Modules\KctUser\Traits\KctHelper;

class LogController extends BaseController {
    use KctHelper;

    public function createAttendanceLog(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => [
                    'required',
                    Rule::exists('tenant.events', 'event_uuid')
                        ->where(function ($q) {
                            $q->where('start_time', '<=', Carbon::now());
                            $q->where('end_time', '>', Carbon::now());
                        }),
                ],
                'user_id'    => [
                    'required',
                    Rule::exists('tenant.event_users', 'user_id')
                        ->where('event_uuid', $request->input('event_uuid'))
                ],
                'leave_on'   => 'nullable|boolean',
            ]);
            $validator->validate();

            $this->repo->eventRepository->createEventAttendLog(
                $request->input('event_uuid'),
                $request->input('user_id'),
                $request->input('leave_on')
            );

            return response()->json(true);
        } catch (ValidationException $e) {
            return $e->errors();
        } catch (EventLeaveAgainException $e) {
            return 'User trying to leave again';
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }

    public function createPilotContentLog(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid'   => [
                    'required',
                    Rule::exists('tenant.events', 'event_uuid')
                        ->where(function ($q) {
                            $q->where('start_time', '<=', Carbon::now());
                            $q->where('end_time', '>', Carbon::now());
                        }),
                ],
                'pilot_id'     => [
                    'required',
                    Rule::exists('tenant.event_users', 'user_id')
                        ->where('event_uuid', $request->input('event_uuid'))
                ],
                'action'  => 'required|in:2,3', // 2 for video, 3 for image
                'action_state' => 'required|string',
            ]);
            $validator->validate();

            $recurrence = EventSingleRecurrence::where('event_uuid', $request->event_uuid)->whereDate('recurrence_date', Carbon::today()->toDateString())->first();
            LogEventContent::create([
                'recurrence_uuid' => $recurrence->recurrence_uuid,
                'action'          => $request->action,
                'action_state'    => $request->action_state,
                'start_time'      => Carbon::now(),
                'duration'        => 0,
            ]);

            return response()->json(true);
        } catch (ValidationException $e) {
            return $e->errors();
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }
}
