<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;


/**
 * @OA\Schema(
 *  title="APIResource: EventUserResource",
 *  description="Event participant resource contains user basic data",
 *  @OA\Property(
 *      property="user_id",
 *      type="integer",
 *      description="ID of user",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="user_name",
 *      type="string",
 *      description="Full Name of user",
 *      example="Someone User"
 *  ),
 *  @OA\Property(
 *      property="is_presenter",
 *      type="integer",
 *      description="To indicate user is as presenter or not in event",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="is_moderator",
 *      type="integer",
 *      description="To indicate user is as moderator or not in event",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="is_host",
 *      type="integer",
 *      description="To indicate user is as is_host or not in event",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="is_secretory",
 *      type="integer",
 *      description="To indicate user is as secretory or not in event workshop",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="is_deputy",
 *      type="integer",
 *      description="To indicate user is as deputy or not in event workshop",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="presence_status",
 *      type="enum",
 *      description="The presense status for the user in that event workshop meeting, which basically presents user presense in event as there is only single meeting in event workshop",
 *      enum={"placed", "approved", "delivered"},
 *      example="P",
 *  ),
 *  @OA\Property(
 *      property="role",
 *      type="enum",
 *      description="Possible role of user in OPS",
 *      enum={"placed", "approved", "delivered"}
 *  ),
 * )
 *
 * Class EventUserResource
 * @package Modules\Cocktail\Transformers\AdminSide
 */
class EventUserResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        
        $isSec = ($this->relationLoaded('isSecretory') && $this->isSecretory->count()) ? 1 : 0;
        $isDep = ($this->relationLoaded('isDeputy') && $this->isDeputy->count()) ? 1 : 0;
        $presenceStatus = $this->presenceStatus ? $this->presenceStatus->presence_status : null;
        $role = $this->getRole($isSec, $isDep);
        return [
            'user_id'         => $this->user_id,
            'user_name'       => $this->user->fname . ' ' . $this->user->lname,
            'unions'          => EntityResource::collection($this->user->unions),
            'company'         => new EntityResource($this->user->companies->first()),
            'is_presenter'    => $this->is_presenter,
            'is_moderator'    => $this->is_moderator,
            'is_host'         => $this->isHost->count() ? 1 : 0,
            'is_secretory'    => $isSec,
            'is_deputy'       => $isDep,
            'presence_status' => $presenceStatus,
            'role'            => $role,
            'role_commision'  => $this->user->role_commision,
        ];
    }
    
    private function getRole($isSec, $isDep) {
        $role = [];
        if ($isSec) {
            $role[] = 1;
        }
        if ($isDep) {
            $role[] = 2;
        }
        if (!count($role)) {
            $role[] = 0;
        }
        return $role;
    }
    
}
