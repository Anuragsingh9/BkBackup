<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: UserResource",
 *  description="This resource contains records of multiple user created",
 *     @OA\Property(property="field",type="string",description="Key of setting",example="enabled",
 *         enum={"enabled","is_assigned","webinar_data","meeting_data","authenticate_url","is_visible","user_can_update"},
 *     ),
 *     @OA\Property(property="value",type="string",
 *         description="Value will depend on the field type
 *         NOTE: when sending form data don't send the setting array from react directly to here because from react if you send object/array to form data key it will stringify it
 *             color type -> {r:3,g:3,b:1,a:0.3}
 *             text based send simple string
 *             for logo/image there will full url",example=""),
 * )
 */
class TechnicalSettingResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $data = $this->resource->setting_value;
        if ($this->resource->setting_key == 'default_zoom_settings') {
            if (Auth::user()->can('check-default-zoom-webinar')) {
                $data =  [
                    'enabled'          => $data['enabled'] ?? 0,
                    'is_assigned'      => $data['is_assigned'] ?? 0,
                    'webinar_data'     => [
                        'available_license' => $data['webinar_data']['available_license'] ?? 0,
                        'licenses'          => isset($data['webinar_data']['hosts'])
                            ? BroadcastHostResource::collection($data['webinar_data']['hosts'])
                            : [],
                    ],
                    'meeting_data'     => [
                        'available_license' => $data['meeting_data']['available_license'] ?? 0,
                        'licenses'          => isset($data['meeting_data']['hosts'])
                            ? BroadcastHostResource::collection($data['meeting_data']['hosts'])
                            : [],
                    ],
                    'authenticate_url' => $this->adminServices()->zoomService->getOAuthLoginUrl('default_zoom_settings'),
                    'is_visible'       => 1,
                    'user_can_update'  => 1,
                ];
            } else {
                $data =  [
                    'enabled'         => (int)(($data['enabled'] ?? 0) && ($data['is_assigned'] ?? 0)),
                    'user_can_update' => 0,
                    'is_assigned'     => $data['is_assigned'] ?? 0,
                ];
            }
        } else if ($this->resource->setting_key == 'custom_zoom_settings'
            || $this->resource->setting_key == 'zoom_meeting_settings') {
            $data =  [
                'enabled'          => $data['enabled'] ?? 0,
                'is_assigned'      => $data['is_assigned'] ?? 0,
                'webinar_data'     => [
                    'available_license' => $data['webinar_data']['available_license'] ?? 0,
                    'licenses'          => isset($data['webinar_data']['hosts'])
                        ? BroadcastHostResource::collection($data['webinar_data']['hosts'])
                        : [],
                ],
                'meeting_data'     => [
                    'available_license' => $data['meeting_data']['available_license'] ?? 0,
                    'licenses'          => isset($data['meeting_data']['hosts'])
                        ? BroadcastHostResource::collection($data['meeting_data']['hosts'])
                        : [],
                ],
                'authenticate_url' => $this->adminServices()->zoomService->getOAuthLoginUrl(),
                'is_visible'       => 1,
                'user_can_update'  => 1,
            ];
        }
        return [
            'field' => $this->resource->setting_key,
            'value' => $data,
        ];
    }

    public function getLicenses($data) {
        if (count($data['licenses'] ?? [])) {
            return HostResource::collection($this->adminServices()->userService->getUsersById($data['licenses']));
        }
        return [];
    }
}
