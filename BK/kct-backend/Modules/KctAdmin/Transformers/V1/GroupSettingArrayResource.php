<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: GroupSettingArrayResource",
 *  description="This resource contains the group setting array resource",
 *  @OA\Property(property="enabled",type="integer",description="This is shows the group enabled or not",example="0"),
 *  @OA\Property( property="licenses",type="string",description="Licenses attteched in the group",example="zoom licenses"),
 *  @OA\Property( property="number_of_licenses", type="integer", description="Number of licenses atteched in group",
 *      example="3"
 *  ),
 *  @OA\Property( property="is_assigned", type="integer", description="If the zoom data present or not", example="0"),
 *  @OA\Property(property="authenticate_url",type="string",description="zoom webinar url",
 *     example="https://explore.zoom.us/en/products/webinar/"
 *  ),
 *  @OA\Property( property="is_visible", type="integer", description="Current technical setting present or not",
 *      example="1"
 *  ),
 *  @OA\Property( property="user_can_update", type="integer", description="User can update or not technical setting",
 *      example="1"
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return group setting array resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupSettingArrayResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class GroupSettingArrayResource extends JsonResource {
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
        $data = $this->resource->setting_value;
        if ($this->resource->setting_key == 'default_zoom_settings') {
            if (Auth::user()->can('check-default-zoom-webinar')) {
                return [
                    'enabled'            => $data['enabled'] ?? 0,
                    'licenses'           => $this->getLicenses($data),
                    'number_of_licenses' => $data['available_license'] ?? 0,
                    'is_assigned'        => $data['is_assigned'] ?? 0,
                    'authenticate_url'   => $this->adminServices()->zoomWebinarService->getOAuthLoginUrl('zoom_default_webinar_settings'),
                    'is_visible'         => 1,
                    'user_can_update'    => 1,
                ];
            } else {
                return [
                    'enabled'         => (int)(($data['enabled'] ?? 0) && ($data['is_assigned'] ?? 0)),
                    'user_can_update' => 0,
                    'is_assigned'     => $data['is_assigned'] ?? 0,
                ];
            }
        } else if ($this->resource->setting_key == 'custom_zoom_settings'
            || $this->resource->setting_key == 'zoom_meeting_settings') {
            return [
                'enabled'            => $data['enabled'] ?? null,
                'licenses'           => $this->getLicenses($data),
                'number_of_licenses' => $data['available_license'] ?? null,
                'user_can_update'    => 1,
                'authenticate_url'   => $this->adminServices()->zoomMeetingService->getOAuthLoginUrl('custom_zoom_settings'),
                'is_assigned'        => $data['is_assigned'] ?? 0,
            ];
        }
        return $this->resource->setting_value ?? [];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get how many licenses attached with group.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return array|AnonymousResourceCollection
     */
    public function getLicenses($data) {
        if (count($data['licenses'] ?? [])) {
            return HostResource::collection($this->adminServices()->userService->getUsersById($data['licenses']));
        }
        return [];
    }
}
