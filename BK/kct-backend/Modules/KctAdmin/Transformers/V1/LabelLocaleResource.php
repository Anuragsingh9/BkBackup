<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: LabelLocaleResource",
 *  description="Return Label Locale Resource",
 *  @OA\Property(property="value",type="string",description="Value of label for current locale",example="Space Host"),
 *  @OA\Property(property="locale",type="string",description="Locale name in small letters { en, fr }",example="en"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to label with their locale.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class LabelLocaleResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class LabelLocaleResource extends JsonResource {

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
            'value'  => $this->resource->value,
            'locale' => $this->resource->locale,
        ];
    }
}
