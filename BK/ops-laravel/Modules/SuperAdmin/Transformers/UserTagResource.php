<?php

namespace Modules\SuperAdmin\Transformers;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @OA\Schema(
 *  title="APIResource: UserTagResource",
 *  description="Return User User Tag Data",
 *  @OA\Property(
 *      property="tag_id",
 *      type="integer",
 *      description="Tag ID",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="tag_EN",
 *      type="string",
 *      description="Tag's English Name",
 *      example="Test"
 *  ),
 *  @OA\Property(
 *      property="tag_FR",
 *      type="string",
 *      description="Tag's French Name",
 *      example="Test"
 *  ),
 *  @OA\Property(
 *      property="tag_type",
 *      type="integer",
 *      description="Type of Tag",
 *      example="1"
 *  )
 * )
 *
 * Class UserTagResource
 * @package Modules\SuperAdmin\Transformers
 */
class UserTagResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'tag_id'   => $this->id,
            'tag_EN'   => $this->tag_EN,
            'tag_FR'   => $this->tag_FR,
            'tag_type'   => $this->tag_type,
        ];
        
        return $result;
    }
}
