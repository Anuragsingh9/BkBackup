<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EntityResource;

/**
 * @OA\Schema(
 *  title="APIResource: UserFullResource",
 *  description="This resource contains records of multiple user created",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property( property="fname",type="string",description="First Name",example="First"),
 *  @OA\Property( property="lname", type="string", description="Last Name", example="Last" ),
 *  @OA\Property( property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property( property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property( property="phone_code", type="string", description="Primary Mobile Code For User", example="+91"),
 *  @OA\Property( property="phone_number", type="string", description="Primary Mobile Number of User", example="9876543210"),
 *  @OA\Property( property="mobile_code", type="string", description="Primary Mobile Code For User", example="+91"),
 *  @OA\Property( property="mobile_number", type="string", description="Primary Mobile Number of User", example="9876543210"),
 *  @OA\Property( property="mobiles",type="array",description="User all Mobile numbers",@OA\Items(ref="#/components/schemas/UserMobileResource")),
 *  @OA\Property( property="phones",type="array",description="User all landline numbers",@OA\Items(ref="#/components/schemas/UserMobileResource")),
 *  @OA\Property( property="setting",type="object",description="User personal settings like language setting",example={"lang":"fr"},
 *     @OA\Property( property="lang",type="string",description="Current Language",example="en"),
 *  ),
 *  @OA\Property( property="current_group",type="object",description="Current Group Object",ref="#/components/schemas/GroupResource"),
 *  @OA\Property( property="is_organiser",type="integer",description="If orgniser of group",example="0"),
 *  @OA\Property( property="role",type="string",description="Role of user",example="pilot"),
 *  @OA\Property( property="city",type="string",description="city of user",example="City Name"),
 *  @OA\Property( property="country",type="string",description="country of user",example="Country Name"),
 *  @OA\Property( property="address",type="string",description="address of user",example="Full Address"),
 *  @OA\Property( property="postal",type="string",description="postal code of user",example="305060"),
 *  @OA\Property( property="company",type="object",description="Company of user",ref="#/components/schemas/EntityResource"),
 *  @OA\Property( property="unoins",type="array",description="Unoins of user",@OA\Items(ref="#/components/schemas/EntityResource")),
 *  @OA\Property( property="internal_id",type="string",description="User Internal Id",example="1234ABCD"),
 *  @OA\Property( property="is_self",type="integer",description="If current user id and user id is same than return 1",example="1"),
 *  @OA\Property( property="login_count",type="integer",description="Login count of the group",example="10"),
 *  @OA\Property( property="current_group_key",type="string",description="Current group key",example="mainsett"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class is used to return all user related data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserFullResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class UserFullResource extends JsonResource {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $group = $this->when(Auth::check() && Auth::user()->id == $this->resource->id,
            $this->adminServices()->groupService->getUserCurrentGroup(Auth::user()->id),
            new GroupResource($this->resource->group));
        $isOrganiser = $this->adminRepo()->groupUserRepository->isOrganiser($this->resource->id) ? 1 :
            ((int)$this->resource->hasRole('super_admin') ? 1 : 0);
        return [
            'id'                => $this->resource->id,
            'fname'             => $this->resource->fname,
            'lname'             => $this->resource->lname,
            'email'             => $this->resource->email,
            'avatar'            => $this->adminServices()->fileService->getFileUrl($this->resource->avatar),
            // user contact details
            'phone_code'        => $this->resource->primaryPhone->country_code ?? null,
            'phone_number'      => $this->resource->primaryPhone->number ?? null,
            'mobile_code'       => $this->resource->primaryMobile->country_code ?? null,
            'mobile_number'     => $this->resource->primaryMobile->number ?? null,
            'mobiles'           => UserMobileResource::collection($this->resource->mobiles),
            'phones'            => UserMobileResource::collection($this->resource->phones),
            // user settings
            'setting'           => [
                'lang' => $this->resource->setting['lang'] ?? null,
            ],
            // user group details
            'current_group'     => $group,
            'is_organiser'      => $isOrganiser,
            'role'              => $this->getUserRole(),
            // user personal info section
            'city'              => $this->resource->personalInfo->city ?? null,
            'country'           => $this->resource->personalInfo->country ?? null,
            'address'           => $this->resource->personalInfo->address ?? null,
            'postal'            => $this->resource->personalInfo->postal ?? null,
            'company'           => $this->whenLoaded('company', new EntityResource($this->resource->company)),
            'unions'            => $this->whenLoaded('unions', EntityResource::collection($this->resource->unions)),
            'internal_id'       => $this->resource->internal_id,
            'is_self'           => $this->when(Auth::check() && Auth::user()->id == $this->resource->id, 1),
            'login_count'       => $this->resource->login_count,
            'current_group_key' => $group->group_key,
            'gender'            => $this->resource->gender,
            'grade'             => $this->getUserGradeRole($this->resource),
        ];
    }

    private function getUserRole() {
        $groupId = $this->resource->group->id;
        return $this->adminRepo()->groupUserRepository->getUserGroupRole($groupId, $this->resource->id);
    }
}
