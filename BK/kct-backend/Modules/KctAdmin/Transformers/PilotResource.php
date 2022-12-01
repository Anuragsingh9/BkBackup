<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: PilotResource",
 *  description="Return pilot Data",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="lname",type="string",description="Last name of user",example="hello"),
 *  @OA\Property(property="fname",type="string",description="First name of user",example="hello"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="company_name",type="object",description="Company Name",example="KCT"),
 *  @OA\Property(property="company_position",type="object",description="Unoins of user",example="IT"),
 * )
 *
 * Class PilotResource
 * @package Modules\KctAdmin\Transformers
 */
class PilotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
          'id'  => $this->resource->id,
          'fname' => $this->resource->fname,
          'lname' => $this->resource->lname,
          'email' => $this->resource->email,
          'company_name' => $this->resource->company['long_name'] ?? null,
          'company_position' => $this->resource->company['position'] ?? null,

        ];
    }
}
