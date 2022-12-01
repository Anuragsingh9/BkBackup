<?php

namespace Modules\KctAdmin\Http\Requests\V4;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\KctAdmin\Rules\EventExistRule;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\GroupRule;
use Modules\KctAdmin\Rules\V4\EventV4Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Request Validation",
 *  description="Validates the request body for updating a virtual event ",
 *  type="object",
 *  required={"event_uuid","title", "date", "start_time", "end_time"},
 *  @OA\Property(property="event_uuid",type="string",description="Event uuid of the event",example="9bb6b0ec-2371-11ed-8246-f8cab8612f99"),
 *  @OA\Property(property="event_title",type="string",description="Title Of Event",example="Update title Of event"),
 *  @OA\Property(property="event_start_date",type="date",description="Date of event",example="2022-12-31"),
 *  @OA\Property(property="event_start_time",type="time",description="Start time of event",example="12:12:59"),
 *  @OA\Property(property="event_end_time",type="time",description="End time of event",example="12:59:58"),
 *  @OA\Property(property="event_is_demo",type="integer",
 *     description="To indicate if event follows dummy user",example="1", enum={"0", "1"}),
 *  @OA\Property(property="group_key",type="string",description="Group key",example="default"),
 *  @OA\Property(property="join_code",type="string",description="Unique join code for the event",example="cafeteriaev"),
 *  @OA\Property(property="event_space_host",type="integer",description="Id of the space host",example="2"),
 *  @OA\Property(property="event_space_max_capacity",type="integer",description="Maximum capacity of the event",example="222"),
 *  @OA\Property(property="event_recurrence",type="object",description="Event recurrence data",
 *      @OA\Property( property="rec_type", type="integer",
 *           description="Country Code For User", example="1"),
 *      @OA\Property( property="rec_end_date", type="string",
 *           description="Recurrence end date", example="2023-10-20"),
 *      @OA\Property( property="rec_weekdays",type="integer",
 *          description="Recurrence weekdays",example="1"),
 *     @OA\Property( property="rec_month_date",type="integer",
 *          description="Recurrence month date",example="1"),
 *     @OA\Property( property="rec_interval",type="integer",
 *          description="Recurrence interval",example="1"),
 *     @OA\Property( property="rec_month_type",type="integer",
 *          description="Recurrence monthly type",example="1"),
 *     @OA\Property( property="rec_on_month_week",type="string",
 *          description="To indicate the current number is primary",example="1"),
 *     @OA\Property( property="rec_on_month_week_day",type="string",
 *          description="To indicate the current number is primary",example="Monday"),
 *     ),
 *     ),
 * ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for updating an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateEventV4Request
 *
 * @package Modules\KctAdmin\Http\Requests\V4
 */
class UpdateEventV4Request extends FormRequest {

    use ServicesAndRepo;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->event_uuid);
        $validation = config('kctadmin.modelConstants.events.validations');
        $spaceDefault = config('kctadmin.modelConstants.spaces.defaults');
        $defaultMin = config('kctadmin.modelConstants.default_text_min');
        $weekDayNames = join(
            ',',
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        );

        $spaceValidation = config('kctadmin.modelConstants.spaces.validations');

        return [
            'event_uuid'       => ["required", new EventRule],
            'event_title'      => "required|string|min:$defaultMin|max:{$validation['title_max']}",
            'event_start_date' => [
                "required",
                new EventV4Rule,
                new EventExistRule(
                    $this->input('event_start_date'),
                    $this->input('event_start_time'),
                    $this->input('event_end_time'),
                    $this->input('event_title')
                )
            ],

            'event_start_time' => ['required', new EventV4Rule],
            'event_end_time'   => ['required', new EventV4Rule],

            'event_custom_link'          => [
                "nullable",
                "regex:/^[a-zA-Z0-9-]+$/",
                "max:{$validation['join_code_max']}",
                "min:{$validation['join_code_min']}",
                Rule::unique('tenant.events', 'join_code')->ignore($event ? $event->event_uuid : '', 'event_uuid')
            ],
            'event_description'  => 'nullable|string',
            'event_is_demo'      => 'nullable|in:1,0',
            'event_is_published' => ["nullable", "in:0,1"],
            'group_key'          => ["required", new GroupRule],

            'event_recurrence'                       => 'nullable|array',
            'event_recurrence.rec_type'              => 'nullable|integer|in:0,1,3,5', // none, daily, weekly, monthly
            'event_recurrence.rec_end_date'          => 'nullable|date',
            'event_recurrence.rec_weekdays'          => 'nullable|integer',
            'event_recurrence.rec_month_date'        => 'nullable|integer',
            'event_recurrence.rec_interval'          => 'nullable|integer',
            'event_recurrence.rec_month_type'        => 'nullable|integer',
            'event_recurrence.rec_on_month_week'     => 'nullable|integer',
            'event_recurrence.rec_on_month_week_day' => "nullable|string|in:$weekDayNames",

            'event_scenery'       => "nullable|integer",
            'event_scenery_asset' => "nullable|integer",
            'event_top_bg_color'  => "nullable",
            'event_component_op'  => "nullable",

            'event_spaces'                      => 'required|min:1',
            'event_spaces.0.space_is_vip'       => 'nullable|in:0',
            'event_spaces.*.space_line_1'       => "required|string|min:{$spaceDefault['default_min']}|max:{$spaceValidation['space_line_1']}",
            'event_spaces.*.space_line_2'       => "nullable|string|max:{$spaceValidation['space_line_2']}",
            'event_spaces.*.space_host'         => ['required', 'exists:tenant.users,id'],
            'event_spaces.*.space_max_capacity' => [
                "required",
                "integer",
                "min:{$spaceDefault['min_capacity']}",
                "max:{$spaceDefault['max_capacity']}",
            ],
            'event_spaces.*.space_is_vip'       => 'nullable|in:1,0',
            'event_spaces.*.space_uuid'         => [
                'nullable',
                Rule::exists('tenant.event_spaces', 'space_uuid')
                    ->where('event_uuid', $this->input('event_uuid'))
            ],
            'event_conv_limit' => 'nullable|in:4,8',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
