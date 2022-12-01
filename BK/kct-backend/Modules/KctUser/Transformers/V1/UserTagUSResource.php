<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

/**
 * @OA\Schema(
 *  title="APIResource: UserBadgeResource",
 *  description="Return User Badge Data",
 *  @OA\Property(property="id",type="integer",description="ID of tag",example="1"),
 *  @OA\Property(property="name",type="string",description="Name of user",example="hello"),
 *  @OA\Property(property="is_moderated",type="integer",description="Is moderated",example="1"),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class used for user tag resource
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UserTagUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class UserTagUSResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $locale = strtolower(App::getLocale());
        return [
            "id"           => $this->resource->id,
            "name"         => $this->resource->locales->where('locale', $locale)->first()
                ? $this->resource->locales->where('locale', $locale)->first()->value
                : null,
            "is_moderated" => $this->resource->status == 1 ? 1 : 0,
            'status'       => $this->resource->status,
        ];
    }
}
