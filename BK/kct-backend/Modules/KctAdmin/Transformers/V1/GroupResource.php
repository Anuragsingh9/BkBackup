<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\FavouriteGroup;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: GroupResource",
 *  description="This resource contains single Group data",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property( property="group_type",type="string",description="Group Type",example="Super Group, Function Group, Topic Group"),
 *  @OA\Property( property="group_key",type="string",description="Group Key",example="grp123"),
 *  @OA\Property( property="group_name",type="string",description="Group Name",example="Humann Connect"),
 *  @OA\Property( property="description",type="string",description="Group Description"),
 *  @OA\Property( property="pilots",type="array",description="Pilots",@OA\Items(ref="#/components/schemas/UserMinResource")),
 *  @OA\Property( property="type_value",type="string",description="Group Type Value",example="Location of group, function value, topic value"),
 *  @OA\Property( property="allow_user",type="integer",description="Allow user toggle button"),
 *  @OA\Property( property="allow_manage_pilots_owner",type="integer",description="Allow Manage pilots and owner toggle button"),
 *  @OA\Property( property="allow_design_setting",type="integer",description="Allow Design setting toggle button"),
 *  @OA\Property( property="is_super_group",type="integer",description="It retuns the super group or not"),
 *  @OA\Property( property="is_fav_group",type="integer",description="It retuns the group fav or not"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will contain the group related data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class GroupResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {

        $isFavGroup = $this->adminRepo()->groupUserRepository->isFavouriteGroup($this->resource->id);
        return [
            'id'                        => $this->resource->id,
            'group_type'                => $this->resource->groupType->group_type ?? null,
            'group_key'                 => $this->resource->group_key,
            'group_name'                => $this->resource->name,
            'description'               => $this->resource->description,
            'pilots'                    => $this->whenLoaded('pilots',
                $this->relationLoaded('pilots')
                    ? UserMinResource::collection($this->resource->pilots)
                    : []
            ),
            'co_pilots' => $this->whenLoaded('coPilots',
                $this->relationLoaded('coPilots')
                    ? UserMinResource::collection($this->resource->coPilots)
                    : []
            ),
            'type_value'                => $this->resource->mainSetting->setting_value['type_value'] ?? "",
            'allow_user'                => $this->resource->mainSetting->setting_value['allow_user'] ?? 1,
            'allow_manage_pilots_owner' => $this->resource->is_default ?: $this->resource->mainSetting->setting_value['allow_manage_pilots_owner'] ?? 1,
            'allow_design_setting'      => $this->resource->is_default ?: $this->resource->mainSetting->setting_value['allow_design_setting'] ?? 1,
            'is_super_group'            => isset($this->resource->is_default) && $this->resource->is_default ?1:  0,
            'is_fav_group'              => isset($isFavGroup) ? 1 : 0,
        ];
    }
}
