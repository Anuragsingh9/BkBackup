<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\EventExistRule;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\GroupRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Request Validation",
 *  description="Validates the request body for creating a virtual event ",
 *  type="object",
 *  required={"title", "date", "start_time", "end_time"},
 *  @OA\Property(property="title",type="string",description="Title Of Event",example="Title Of Event"),
 *  @OA\Property(property="date",type="date",description="Date of event",example="2022-12-31"),
 *  @OA\Property(property="start_time",type="time",description="Start time of event",example="12:12:59"),
 *  @OA\Property(property="end_time",type="time",description="End time of event",example="12:59:58"),
 *  @OA\Property(property="is_dummy_event",type="integer",
 *     description="To indicate if event follows dummy user",example="1", enum={"0", "1"}),
 *  @OA\Property(property="type",type="integer",
 *     description="To indicate if event follows conference 1=Networking Event, 2=Conference Event",example="1", enum={"1", "2"}),
 *  @OA\Property(property="image",type="image",description="Event Image",example="Header Text"),
 *  @OA\Property(property="group_id",type="int",description="Group Id for the tag",example="1"),
 *  @OA\Property(property="is_self_header",type="integer",
 *     description="To indicate if event has own header",example="1", enum={"0", "1"}),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for creating an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class CreateEventRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class CreateEventRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $validation = config('kctadmin.modelConstants.events.validations');
        $defaultMin = config('kctadmin.modelConstants.default_text_min');
        $recurrence_type_array = config('kctadmin.modelConstants.event_recurrence');

        return [
            'title'          => "required|string|min:$defaultMin|max:{$validation['title_max']}",
            'date'           => ["required", new EventRule, new EventExistRule($this->date, $this->start_time, $this->end_time, $this->title)],
            'start_time'     => ['required', new EventRule],
            'end_time'       => ['required', new EventRule],
            'is_dummy_event' => 'nullable|in:1,0',
            'type'           => 'nullable|in:1,2',
            'description'    => 'nullable|string',
//            'image'          => ['nullable', 'image', "$dimension"],
            'group_key'      => ["required", new GroupRule],
            'is_self_header' => 'nullable|in:0,1',
            'join_code'      => ["nullable", "regex:/^[a-zA-Z0-9-]+$/", "max:{$validation['join_code_max']}",
                "min:{$validation['join_code_min']}", "unique:tenant.events,join_code"],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
