<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: LabelResource",
 *  description="Return Label Resource",
 *  @OA\Property(property="name",type="string",description="Name of label, currently availble names are {space_host,business_team,expert,vip,moderator,speaker,participants,}",example="space_host"),
 *  @OA\Property(property="locales",type="array",description="array of labels", @OA\Items(ref="#/components/schemas/LabelLocaleResource")),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to a labels and their locale
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class LabelResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class LabelResource extends JsonResource {
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
            'name'    => $this->resource->name,
            'locales' => LabelLocaleResource::collection($this->resource->locales)
        ];
    }
}
