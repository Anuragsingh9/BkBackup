<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="APIResource: MomentResource",
 *  description="This resource contains records of multiple moments created",
 *  @OA\Property(property="id",type="integer",description="ID of moment",example="1"),
 *  @OA\Property( property="moment_name",type="string",description="Moment Name",example="First"),
 *  @OA\Property( property="moment_description", type="string", description="Moment description", example="First moment" ),
 *  @OA\Property( property="start_time",type="string",description="Start time of the moment",example="10:01:00"),
 *  @OA\Property( property="end_time",type="string",description="End time of the moment",example="10:11:00"),
 *  @OA\Property( property="moment_setting",type="object",description="Moment setting",example={"pre_recorded_url":"www.abc.com"},
 *     @OA\Property( property="pre_recorded_url",type="string",description="Video url",example="www.abc.com"),
 *  ),
 *  @OA\Property( property="moment_type",type="integer",description="Current moment type",example="1"),
 *  @OA\Property( property="event_uuid",type="string",description="Event uuid to which moment is related",example="2856e2d0-24d9-11ec-a244-74867a0dc41b"),
 *  @OA\Property(property="moderator",type="object",description="Moderator of moment",ref="#/components/schemas/HostResource"),
 *  @OA\Property(property="speakers",type="array",description="Speakers for the moment",@OA\Items(ref="#/components/schemas/HostResource")),
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This method will return all data related to moments of event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class MomentResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class MomentResource extends JsonResource {
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id'                 => $this->resource->id,
            'moment_name'        => $this->resource->moment_name,
            'moment_description' => $this->resource->moment_description,
            'start_time'         => $this->getCarbonByDateTime($this->resource->start_time)->toTimeString(),
            'end_time'           => $this->getCarbonByDateTime($this->resource->end_time)->toTimeString(),
            'moment_type'        => $this->resource->moment_type,
            'moderator'          => $this->resource->moderator ? new HostResource($this->resource->moderator->user) : null,
            'speakers'           => $this->resource->speakers->count() ? HostResource::collection($this->resource->speakers->pluck('user')) : [],
            'video_url'          => $this->when(in_array($this->resource->moment_type, [5, 6]), $this->resource->moment_settings['pre_recorded_url'] ?? null),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will convert date time to time.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $dateTime
     * @return mixed|string
     */
    public function convertToTime($dateTime) {
        $dateTime = explode(" ", $dateTime);
        return $dateTime[1];
    }
}
