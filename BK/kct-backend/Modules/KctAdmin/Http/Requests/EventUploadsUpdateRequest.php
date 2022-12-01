<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\LiveEventRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Live Page Setting Data Request Validation",
 *  description="Validates the request body for creating a live page setting data for auto-created event ",
 *  type="object",
 *  required={"event_uuid"},
 *  @OA\Property(property="event_uuid",type="string",
 *     description="Event uuid of the event",example="20bc75d0-b016-11ec-8b98-b82a72a009b4"),
 *  @OA\Property(property="old_event_uuid",type="string",
 *     description="Old event id of the event",example="20bc75d0-b016-11ec-8b98-b82a72a009b4"),
 *  @OA\Property(property="event_live_image",type="file",
 *     description="This field require when the image is uploading"),
 *  @OA\Property(property="event_live_video_link",type="string",
 *     description="Video link is require when the video is uploading",
 *     example="https://www.youtube.com/watch?v=xIApzP4mWyA&t=4580s"
 *  ),
 *  @OA\Property(property="video_type",type="number",
 *     description="This filed is require when the video is uploading. 1 for youtube, 2 for veimo"),
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request for uploading live event data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventUploadsUpdateRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class EventUploadsUpdateRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'event_uuid'            => ["required", new LiveEventRule($this->event_uuid)],
            'old_event_uuid'        => ["nullable",
                "exists:tenant.events,event_uuid"],
            'event_live_image'      => [
                "nullable",
                "image",
                "max:10240",
                "mimes:png,jpg,jpeg",
                new LiveEventRule($this->event_uuid)
            ],
            'event_live_video_link' => ["nullable", "string", new LiveEventRule($this->event_uuid)],
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
