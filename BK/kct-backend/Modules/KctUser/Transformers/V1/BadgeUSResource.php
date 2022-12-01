<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="APIResource: BadgeUSResource",
 *  description="Return User Badge Data",
 *  @OA\Property(property="user_id",type="integer",description="User ID",example="12"),
 *  @OA\Property(property="user_fname",type="string",description="User First Name",example="Test"),
 *  @OA\Property(property="user_lname",type="string",description="User Last Name",example="Test"),
 *  @OA\Property(property="email",type="email",description="User Email",example="test@test.com"),
 *  @OA\Property(property="user_avatar",type="string",description="User Image",
 *     example="https://[bucket_name].s3.amazonaws.com/a/b.png"
 *  ),
 *  @OA\Property(property="unions",type="array",description="Different Unions in which user is constituted",
 *     @OA\Items(ref="#/components/schemas/EntityUSResource")
 *  ),
 *  @OA\Property(property="company",type="object",description="Company in which user is constituted",
 *     ref="#/components/schemas/EntityUSResource"
 *  ),
 *  @OA\Property(property="visibility",type="object",description="User's tags",
 *     @OA\Property(property="user_lname", type="string", format="text", example="1"),
 *     @OA\Property(property="company", type="string", format="text", example="1"),
 *     @OA\Property(property="unions", type="string", format="text", example="1")
 *  ),
 *  @OA\Property(property="tags_data",type="object",description="User's tags data",
 *      @OA\Property(property="used_tag", type="array", format="text", example="[]",
 *          @OA\Items(
 *              @OA\Property(property="id", type="integer", format="text", example="1"),
 *              @OA\Property(property="name", type="string", format="text", example="tag"),
 *          ),
 *      ),
 *      @OA\Property(property="unused_tag",type="array",format="text",
 *          @OA\Items(
 *               @OA\Property(property="id", type="integer", format="text", example="1"),
 *               @OA\Property(property="name", type="string", format="text", example="tag"),
 *          ),
 *      )
 *  ),
 *  @OA\Property(property="is_dummy",type="integer",description="If User is dummy",example="1"),
 *  @OA\Property(property="dummy_video_url",type="string",description="URL of dummy video",
 *     example="https://www.youtube.com/watch?v=abcd"
 *  ),
 *  @OA\Property(property="active_state",type="integer",description="If event user relation loaded then show state",
 *     example="1"
 *  ),
 *  @OA\Property(property="event_role",type="integer",description="If event user relation loaded then show event role",
 *     example="1"
 *  ),
 *  @OA\Property(property="is_self",type="integer",description="To show if user is self",example="1"),
 *  @OA\Property(property="is_space_host",type="integer",description="To show if user is space host",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be used for managing the user related data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BadgeUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class BadgeUSResource extends JsonResource {
    use Services;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        // to find the visible columns
        $v = $this->resource->userVisibility;
        $result = [
            'user_id'           => $this->resource->id,
            'user_fname'        => $this->resource->fname,
            'user_lname'        => $this->resource->lname,
            'user_email'        => $this->resource->email,
            'user_setting'      => $this->resource->setting,
            'user_avatar'       => $this->userServices()->fileService->getFileUrl(
                $this->resource->avatar,
                !$this->is_dummy
            ),
            'unions'            => EntityUSResource::collection($this->resource->unions),
            'company'           => $this->resource->company
                ? new EntityUSResource($this->resource->company)
                : null,
            'visibility'        => [
                // if value is not present in fields that means it never updated so by default show that and send 1
                'user_lname'        => $v->fields['user_lname'] ?? 1,
                'company'           => $v->fields['company'] ?? 1,
                'unions'            => $v->fields['unions'] ?? 1,
                "p_field_1"         => $v->fields['p_field_1'] ?? 1,
                "p_field_2"         => $v->fields['p_field_2'] ?? 1,
                "p_field_3"         => $v->fields['p_field_3'] ?? 1,
                "personal_tags"     => $v->fields['personal_tags'] ?? 1,
                "professional_tags" => $v->fields['professional_tags'] ?? 1,
            ],
            'tags_data'         => $this->resource->tag,
            'is_mute'           => 0,
            'personal_info'     => [
                "field_1" => $this->resource->personalInfo->fields['field_1'] ?? null,
                "field_2" => $this->resource->personalInfo->fields['field_2'] ?? null,
                "field_3" => $this->resource->personalInfo->fields['field_3'] ?? null,
            ],
            'personal_tags'     => [],
            'professional_tags' => [],
            'social_links'      => [
                'facebook'  => $this->facebookUrl->url ?? null,
                'twitter'   => $this->twitterUrl->url ?? null,
                'instagram' => $this->instagramUrl->url ?? null,
                'linkedin'  => $this->linkedinUrl->url ?? null,
            ],
        ];

        if ($this->is_dummy) {
            $result['is_dummy'] = 1;
            $result['dummy_video_url'] = Storage::disk('kct_video')->url($this->video_url);
            $result['event_role'] = 0;
            $result['is_vip'] = 0;
        } else {
            if ($this->resource->personalTags) {
                $result['personal_tags'] = $this->resource->personalTags
                    ? UserTagUSResource::collection($this->resource->personalTags)
                    : null;
                $result['professional_tags'] = $this->resource->professionalTags
                    ? UserTagUSResource::collection($this->resource->professionalTags)
                    : null;
            }
            if ($this->id != Auth::user()->id) {
                // as current user resource is not self so hide the columns data which are hidden by the respective user
                // after this the data will not send which is hidden by the current resource user
                // either the visible record is not set or value is set to 1 then show that field
                $result['user_lname'] = !isset($v->fields['user_lname'])
                || $v->fields['user_lname'] ? $result['user_lname'] : '';
                $result['company'] = !isset($v->fields['company']) || $v->fields['company'] ? $result['company'] : '';
                $result['unions'] = !isset($v->fields['unions']) || $v->fields['unions'] ? $result['unions'] : [];

                $result['p_field_1'] = !isset($v->fields['p_field_1'])
                || $v->fields['p_field_1'] ? $result['personal_info']['field_1'] : null;
                $result['p_field_2'] = !isset($v->fields['p_field_2'])
                || $v->fields['p_field_2'] ? $result['personal_info']['field_2'] : null;
                $result['p_field_3'] = !isset($v->fields['p_field_3'])
                || $v->fields['p_field_3'] ? $result['personal_info']['field_3'] : null;

                $result['personal_tags'] = !isset($v->fields['personal_tags'])
                || $v->fields['personal_tags'] ? $result['personal_tags'] : [];
                $result['professional_tags'] = !isset($v->fields['professional_tags'])
                || $v->fields['professional_tags'] ? $result['professional_tags'] : [];
            }
            if ($this->resource->relationLoaded('eventUser')) {
                $result['active_state'] = $this->eventUser->state == 1 ? 1 : 2;
                $result['event_role'] = (int)($this->resource->eventUser->event_user_role ?? 0);
                $result['is_vip'] = $this->eventUser->is_vip ? 1 : 0;
            }
            if ($this->resource->id == Auth::user()->id) {
                $result['is_self'] = 1;
            }
        }
        // to show if the current user is space host or not
        if (isset($this->resource->is_space_host)) {
            $result['is_space_host'] = $this->resource->is_space_host ? 1 : 0;
        }
        return $result;
    }


}
