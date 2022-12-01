<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="Resource: VirtualEventResource",
 *  description="Virtual Event Resource",
 *  @OA\Property( property="status", type="boolean", description="To indicate server processed request properly", example="true" ),
 *  @OA\Property(
 *      property="data",
 *      type="object",
 *      description="Virtual Event Resource",
 *      @OA\Property( property="id", type="integer", example="1"),
 *      @OA\Property( property="fname",type="string",description="First Name",example="Title Of Event"),
 *      @OA\Property( property="lname", type="string", description="Last Name", example="Last Name" ),
 *      @OA\Property( property="email", type="string", description="example@example.com", example="example@example.com"),
 *      @OA\Property( property="access_token", type="string", description="Access Token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1Ni...." ),
 *  )
 * )
 *
 */
class UserTokenResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'status' => true,
            'data'   => [
                'id'           => $this->resource->id,
                'fname'        => $this->resource->fname,
                'lname'        => $this->resource->lname,
                'email'        => $this->resource->email,
                'access_token' => $this->resource->createToken('check')->accessToken,
            ]
        ];
    }
}
