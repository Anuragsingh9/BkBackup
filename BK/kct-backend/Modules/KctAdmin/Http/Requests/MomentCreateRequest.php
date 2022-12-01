<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\EventTimeRule;
use Modules\KctAdmin\Rules\MomentRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Create multiple moments",
 *     description= "Validates the request for adding multiple moments",
 *     type= "object",
 *     required= {"moments", "moments.moment_start","moments.moment_end","moments.moment_type","moments.name",
 *     "moments.description","event_uuid",},
 *     @OA\Property(property="moments",
 *          type="array",
 *          description="moments",
 *          @OA\Items(
 *             @OA\Property( property="moment_start",type="string",description="Moment start time",example="10:01:00"),
 *             @OA\Property( property="moment_end",type="string",description="Moment end tim",example="10:15:00"),
 *             @OA\Property( property="moment_type",type="integer",
 *     description="Type of moment (1.networking), (2.default_zoom_webinar), (3.zoom_webinar), (4.zoom_meeting),
 * (5.youtube_pre_recorded), (6.vimeo_pre_recorded)",example="1"),
 * @OA\Property( property="name",type="string",description="Name of the moment",example="Share Ideas"),
 * @OA\Property( property="description",type="string",
 *     description="Moment description",example="Share all plans with members"),
 * @OA\Property( property="video_url",type="string",
 *     description="URL of the pre-recorded video",example="www.abc.com"),
 * @OA\Property( property="speakers",type="array",description="Moment speakers",
 *                  @OA\Items(type="integer",example="1"),
 *              ),
 * @OA\Property( property="moderator",type="email",
 *     description="Moment moderator",example="abc@mailinator.com"),
 *          )
 *     ),
 * @OA\Property( property="event_uuid",type="string",
 *     description="Event uuid for related moment",example="2856e2d0-24d9-11ec-a244-74867a0dc41b"),
 * @OA\Property( property="is_auto_key_moment_event",type="integer",
 *     description="Whether moments need to automatically created or not",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate all request data for moment creation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class MomentCreateRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class MomentCreateRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            "event_uuid"               => [
                "required",
                new EventRule('moment_update'),
                new EventTimeRule(0, 0, 1)
            ],
            "moments"                  => ["required", "array", new EventRule],
            "moments.*.id"             => ["nullable"],
            "moments.*.moment_start"   => ["required", new MomentRule],
            "moments.*.moment_end"     => ["required", new MomentRule],
            //1. networking 2 default zoom webinar, 3. zoom webinar, 4. zoom meetings,
            // 5. youtube pre-recorded, 6. Vimeo pre-recorded
            "moments.*.moment_type"    => "required|in:1,2,3,4,5,6",
            "moments.*.name"           => "nullable|max:255",
            "moments.*.description"    => "nullable|max:255",
            "moments.*.video_url"      => "required_if:moments.*.moment_type,5,6",
            "moments.*.speakers"       => "nullable|array|exists:tenant.users,id",
            "moments.*.moderator"      => ["nullable", 'required_if:moments.*.moment_type,2,3,4', new MomentRule],
            "is_auto_key_moment_event" => "nullable|in:0,1"
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
