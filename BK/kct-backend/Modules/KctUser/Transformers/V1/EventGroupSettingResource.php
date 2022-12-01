<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EventGroupSettingResource",
 *  description="Return Settings of group",
 *  @OA\Property(property="label_setting",type="object",description="Array of Label Settings",example="Expert"),
 *  @OA\Property(property="design_setting",type="object",description="Array of Design Settings",example="main_color_1"),
 * )
 *
 */
class EventGroupSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'label_setting'  => $this->group->labelSetting,
            'design_setting'  => $this->group->toArray()['settings'],
        ];
    }
}
