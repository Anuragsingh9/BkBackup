<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Http\Requests\V1\CreateGroupRequest;
use Modules\KctAdmin\Http\Requests\V1\SettingUpdateRequest;
use Modules\KctAdmin\Http\Requests\V1\UpdateGroupRequest;
use Modules\KctAdmin\Rules\GroupDeleteRule;
use Modules\KctAdmin\Rules\GroupRule;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\V1\GroupResource;
use Modules\KctAdmin\Transformers\V1\GroupSettingResource;
use Modules\KctAdmin\Transformers\V1\GroupsListResource;
use Modules\KctAdmin\Transformers\V1\GroupUserResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage all group related logics and functionalities
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupController
 * @package Modules\KctAdmin\Http\Controllers\V1;
 */
class GroupController extends BaseController {
    use KctHelper;

    /**
     * @OA\Post (
     *  path="/api/v1/admin/groups",
     *  operationId="createGroup",
     *  tags={"Group"},
     *  summary="To create a group",
     *  description="This will create the group",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/CreateGroupRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Group created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="",
     *              @OA\Items(ref="#/components/schemas/GroupResource")
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create new group and save group related settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateGroupRequest $request
     * @return JsonResponse|GroupResource
     */
    public function createGroup(CreateGroupRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $groupData = [
                'group_key'   => 'temp',                         // put the temp value for get the id of the group
                'name'        => $request->input('group_name'),
                'description' => $request->input('description'),
            ];
            // preparing group related settings data
            $mainSetting = [
                'allow_user'                => 1,
                'allow_manage_pilots_owner' => $request->input('allow_manage_pilots_owner'),
                'allow_design_setting'      => $request->input('allow_design_setting'),
                'type_value'                => $request->input('type_value'),
            ];
            $group = $this->repo->groupRepository->createGroup(
                $groupData,
                $request->input('group_type'),
                $mainSetting
            );
            // Prepare the groupKey
            $groupKey = $this->services->groupService->prepareGroupKey($group);
            // Update the temp groupKey to name + id groupKey(tt0001)
            $this->repo->groupRepository->updateGroupKey($group, $groupKey);
            $this->services->groupService->syncGroupSettings($group->id);
            $this->services->groupService->syncLabels($group);
            $this->repo->groupUserRepository->addUserAsPilot($group->id, $request->pilot);
            // adding auth user as co-pilot to group created group(if not added user cannot see the created group)
            $coPilots = $request->input('co_pilot', []);
            in_array(Auth::id(), $request->pilot) ?: array_push($coPilots, Auth::user()->id);
            $this->repo->groupUserRepository->addUserAsCoPilot($group->id, $coPilots);
            $group->load('mainSetting', 'pilots', 'groupType', 'coPilots');
            // copying all organiser tags from super group into current group
            $this->services->groupService->copyGroupTags(1, $group->id); // 1 is the id of super group
            //send email to group creation users
            $this->services->emailService->sendGroupCreationEmail($request, $coPilots);
            DB::connection('tenant')->commit();
            return (new GroupResource($group))->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage(), 'msg' => $e->getTrace()], 500);
        }
    }

     /**
     * @OA\Get(
     *  path="/api/v1/admin/groups",
     *  operationId="getGroup",
     *  tags={"Group"},
     *  summary="To get the group",
     *  description="This will provide the group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="id",in="query",description="Id of group",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Group Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="aaaa",
     *     @OA\Items(ref="#/components/schemas/GroupResource")),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch the data of a group by group key.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|GroupResource
      */
    public function getGroup(Request $request, $groupKey) {
        try {
            $validator = Validator::make($request->route()->parameters(),
                ['groupKey' => "required|exists:tenant.groups,group_key"]);
            if ($validator->fails()) {
                return $this->send422(implode(
                    ',',
                    $validator->errors()->all()),
                    $validator->errors()
                );
            }
            $group = $this->repo->groupRepository->findByGroupKey($groupKey);
            if ($group) {
                // if the group is found then update the last visited group value
                $this->repo->groupUserRepository->updateUserLastVisitedGroup($group->id);
            }
            $group->load('mainSetting', 'pilots', 'groupType', 'coPilots');
            return (new GroupResource($group))->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put (
     *  path="/api/v1/admin/groups",
     *  operationId="updateGroup",
     *  tags={"Group"},
     *  summary="To update a group",
     *  description="This will update the group",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UpdateGroupRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Group created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="",@OA\Items(ref="#/components/schemas/GroupResource")),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to update the group related data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateGroupRequest $request
     * @return JsonResponse|GroupResource
     */
    public function updateGroup(UpdateGroupRequest $request) {
        try {
            $group = $this->repo->groupRepository->findByGroupKey($request->group_key);
            $param = $this->services->dataFactory->prepareGroupUpdateData($request, $group);
            $this->repo->groupRepository->updateGroup($param, $group);
            if ($request->has('pilot')) { // if FR request modify the pilot of the group
                $this->repo->groupUserRepository->updateGroupFirstPilot($group->id, $request->pilot[0]);
            }
            if ($request->has('co_pilot')) { // updating co-pilots
                $this->repo->groupUserRepository->addUserAsCoPilot($group->id, $request->co_pilot);
            }
            // Add or remove user's favourite group
            if ($request->has('is_favourite')) {
                $this->repo->groupUserRepository->updateUserFavGroups($group->id, $request->is_favourite);
            }
            $group->refresh();
            $group->load('mainSetting', 'pilots', 'groupType', 'coPilots');
            // Send email to new pilot and created by pilot
            $this->services->emailService->sendGroupModificationEmail($request);
            return (new GroupResource($group))->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Delete (
     *  path="/api/v1/admin/groups",
     *  operationId="deleteGroup",
     *  tags={"Group"},
     *  summary="To delete a group",
     *  description="This will be used for delete the group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="query",description="Key of group",required=true),
     *  @OA\Parameter(name="delete_mode",in="query",description="Mode of delete(1 or 2) 1 = export to user in default group
     *      and delete the group, 2 = take one more input confirmation_email of group pilot and delete the group permanently",
     *      required=true
     *  ),
     *  @OA\Parameter(name="confirmation_email",in="query",description="Confirmation email of group's pilot",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Group deleted",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the group
     * if FR request have delete_methed value have 1 it export to user in default group and delete the group
     * if FR request have delete_method value 2 it take one more input confirmation_email value
     * and validate the value with group pilot and delete the group permanently
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteGroup(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'groupKey'           => ['required', 'exists:tenant.groups,group_key', new GroupDeleteRule()],
                'delete_mode'        => "required|in:1,2",
                'confirmation_email' => 'nullable|string'
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(
                    ',',
                    $validator->errors()->all()),
                    $validator->errors()
                );
            }
            if ($request->input('delete_mode') == 1) {
                // Get the default group information
                $defaultGroup = $this->repo->groupRepository->getDefaultGroup();
                $users = $this->repo->groupRepository->getGroupUserRelation($defaultGroup->id);
                $defaultGroupUserIds = $users->pluck('user_id');
                // Get the current group information
                $currentGroup = $this->repo->groupRepository->getGroupByGroupKey($request->groupKey);
                $currentGroupUser = $this->repo->groupRepository->getGroupUserRelation($currentGroup->id);
                $currentGroupUserIds = $currentGroupUser->pluck('user_id');
                // Find user id to export in default group
                $exportUser = array_diff($currentGroupUserIds->toArray(), $defaultGroupUserIds->toArray());
                foreach ($currentGroupUser as $user) {
                    if (in_array($user->user_id, $exportUser)) {
                        $user->group_id = $defaultGroup->id;
                        $user->update();
                    }
                }
                $currentGroup->delete();
            } elseif ($request->input('delete_mode') == 2 && $request->has('confirmation_email')) {
                $group = $this->repo->groupRepository->getGroupByGroupKey($request->groupKey);
                if (!$group) {
                    return response()->json(['status' => true, 'msg' => 'Invalid Group Id'], 200);
                }
                $pilot = $this->repo->groupUserRepository->getGroupUsers($group->id, [2]);
                $userData = $this->services->userService->findById($pilot->user_id);
                if ($request->input('confirmation_email') == $userData->email) {
                    $group->delete();
                }
            } else {
                return response()->json(['status' => false, 'error' => 'Please give valid input']);
            }
            return response()->json(['status' => true, 'msg' => 'Group Deleted'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/groups/fetch/list",
     *  operationId="getAllGroups",
     *  tags={"Group"},
     *  summary="To get the all the group list",
     *  description="This will provide all the group list",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="Group list Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="",@OA\Items(ref="#/components/schemas/GroupsListResource")),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method fetch all the group list
     * This method provide paginated, group limit and group type data
     * Also provide the meta data for we can create group and limit of the group we can create
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getAllGroups(Request $request) {
        try {
            $order = $request->input('order', 'asc'); //ascending,descending
            $orderBy = $request->order_by == 'group_name' ? 'name' : $request->input('order_by', 'name');
            Validator::make($request->all(), [
                'group_type' => 'nullable|array',
                'type.*'     => 'nullable|in:super_group,local_group,function_group,topic_group,
                                    head_quarters_group,spontaneous_group,water_fountain_group',
            ]);
            $groups = $this->repo->groupRepository->getCurrentUserGroups(
                $order,
                $orderBy,
                $request->has('isPaginate'),
                $request->input('key'),
                $request->input('group_limit'),
                $request->input('group_type'),
                $request->input('filter'),
            );
            if ($orderBy != 'next_event') {
                // load the groups with pilots with company and group type
                $groups->load([
                    'pilots' => function ($q) {
                        $q->with('company');
                    },
                    'groupType',
                ]);
            }
            $currentGroup = $this->services->groupService->getUserCurrentGroup(Auth::user()->id);

            $defaultGroup = $this->repo->groupRepository->getDefaultGroup();
            //check the user is admin or not for creating a group
            $isAdmin = $this->services->groupService->isUserGroupAdmin($defaultGroup, Auth::user()->id);
            //check the current user is super admin or not
            $isSuperAdmin = $this->repo->groupUserRepository->isUserSuperPilotOrOwner();
            // Get the account settings for the group for meta data
            $accountSetting = $this->repo->groupRepository->getAccountSettings();
            $meta = [
                'group_settings' => [
                    'can_create_group'   => $accountSetting['setting_value']['allow_multi_group']
                        && $accountSetting['setting_value']['max_group_limit'] + 1 > count($groups)
                        && $isAdmin,
                    'group_create_limit' => $accountSetting['setting_value']['max_group_limit'] + 1,
                ],
                'current_group'  => new GroupResource($currentGroup),
            ];
            if($isSuperAdmin){
                $meta['default_group'] = $defaultGroup;
            }

            return GroupsListResource::collection($groups)->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => $e->getTrace()
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *  path="/api/v1/admin/groups/users/{groupKey}",
     *  operationId="getGroupUsers",
     *  tags={"Group"},
     *  summary="To get the all users of a group",
     *  description="This will provide group users list",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Key of group",required=true),
     *  @OA\Parameter(name="type[]",in="query",
     *     description="type of user. 1 for user,2 for pilot, 3 for owners.",
     *      @OA\Schema(type="array",
     *          @OA\Items(type="enum",enum={1,2,3}),
     *     ),
     * ),
     *  @OA\Parameter(name="order_by",in="query",description="Sort by fname and lname",required=false),
     *  @OA\Parameter(name="order",in="query",description="Decide order is ascending or descendeing",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Group Data Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="",
     *     @OA\Items(ref="#/components/schemas/UserFullResource")),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch users from a specific group according to group role.
     * @note Group roles are:- 1. User 2. Organiser
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getGroupUsers(Request $request, $groupKey) {
        try {
            $request->merge(['groupKey' => $groupKey]);
            $validator = Validator::make($request->all(), [
                'groupKey'     => ['required', new GroupRule],
                'type'         => 'nullable|array',
                'type.*'       => 'nullable|in:1,2,3,4',// 1. Group users, 2. Group main pilot, 3. Group owners, 4. Group co-pilots
                'order_by'     => 'nullable|in:lname,email',
                'order'        => 'nullable|in:asc,desc',
                'row_per_page' => 'nullable|integer' // 1. User 2. Organiser
            ]);
            $reqFilter = $request->input('added', 0);
            $orderBy = $request->input('order_by', 'lname');
            $order = $request->input('order', 'asc');
            $rowPerPage = $request->input('row_per_page', 10);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all())
                    , $validator->errors());
            }
            $groupId = $this->repo->groupRepository->getGroupIdByKey($groupKey);
            $userRole = $this->repo->groupRepository->getGroupUserRole(
                $groupId,
                $request->input('type', []),
                true,
                $reqFilter);
            // fetching users id to make user order by on users table before pagination
            $usersId = [];
            foreach ($userRole as $user) {
                $usersId[] = $user->user->id;
            }
            $users = $this->services->dataFactory->applyPaginationOnUsers($usersId, $orderBy, $order,
                $request->pagination, $rowPerPage);
            $users->load(['group' => function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            }]);
            return GroupUserResource::collection($users)->additional([
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/groups/settings/{groupKey}",
     *  operationId="getGroupSettings",
     *  tags={"Group"},
     *  summary="To get the all settings of a group",
     *  description="This will provide the data to for all the settings of group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Key of group",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Group Data Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="",@OA\Items(ref="#/components/schemas/GroupSettingResource")),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method  will be used for fetching the group related settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     * @throws Exception
     */
    public function getGroupSettings(Request $request, $groupKey) {
        $validator = Validator::make($request->route()->parameters(), [
            'groupKey' => 'required|exists:tenant.groups,group_key',
        ]);
        if ($validator->fails()) {
            return $this->send422(implode(',', $validator->errors()->all()));
        }
        $group = $this->repo->groupRepository->findByGroupKey($groupKey);
        $targetGroup = $group;
        // checking if any setting key missing for this group and if any found then synchronize it
        if ($group->allSettings->count() != count($this->getGraphicKeys())) {
            $this->services->groupService->syncGroupSettings($group->id);
            $group->load(['allSettings' => function ($q) {
                $q->whereIn('setting_key', $this->getGraphicKeys());
            }]);
        }
        $defaultGroup = $this->repo->groupRepository->getDefaultGroup();

        $hasOwnCustomization = $this->repo->settingRepository->getSettingByKey(
            'group_has_own_customization',
            $group->id
        );
        if ($group->mainSetting->setting_value['allow_design_setting'] && // checking design setting allowed on group creation
            $hasOwnCustomization->setting_value['group_has_own_customization']) { // checking group has own customization
            $groupKey = $group->group_key;
        } else {
            // default group settings
            $group = $defaultGroup;
            $groupKey = $group->group_key;
        }
        $group = $this->repo->groupRepository->getGroupByGroupKey($groupKey);
        $group->load(['allSettings' => function ($q) {
            $q->whereIn('setting_key', $this->getGraphicKeys());
        }]);

        if ($group->id !== $defaultGroup->id) {
            $group->allSettings->map(function ($setting) use ($defaultGroup) {
                if ($setting->setting_key == 'group_logo' && !$setting->setting_value['group_logo']) {
                    $setting->setting_value = $defaultGroup->setting()->where('setting_key', 'group_logo')
                        ->first()->setting_value;
                    $setting->isDefault = 1;
                }
                return $setting;
            });
        }
        return GroupSettingResource::collection($group->allSettings)->additional([
            'status' => true,
            'meta'   => [
                'allow_design_setting' => $targetGroup->mainSetting->setting_value['allow_design_setting']
            ]
        ]);
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/groups/settings",
     *  operationId="updateSettings",
     *  tags={"Group"},
     *  summary="To update any set of settings for the group",
     *  description="This will provide the data to for all the settings of group",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/SettingUpdateRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Group Data Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *              description="The data key which contains the response of api related",
     *              @OA\Items(
     *                  @OA\Property(property="group_logo",type="string",description="Path of group logo",
     *                      example="https://s3.eu-west-2.amazonaws.com/kct-dev/general/group_logo/default.png"),
     *                  @OA\Property(property="is_default",type="integer",
     *                      description="To indicate if group logo is default logo ",example="1")
     *              ),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating the group settings
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param SettingUpdateRequest $request
     * @return JsonResponse
     */
    public function updateSettings(SettingUpdateRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $groupSetting = config('kctadmin.default.group_settings');
            $return = [];
            // synchronizing the settings to assure no setting keys are missing
            $designSetting = [];
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->group_key);
            $groupId = $group->id;
            $this->services->groupService->syncGroupSettings($groupId);
            foreach ($request->all()['settings'] as $i => $setting) {
                // as return type is {"field_name": "field value"};
                $return[$setting['field']] = $setting['value'] ?? null;
                // validating colors
                if (in_array($setting['field'], array_keys($groupSetting['colors']))) {
                    // as from front the color is coded in json encode so decoding to array and storing in value
                    $setting['value'] = json_decode($setting['value'], JSON_OBJECT_AS_ARRAY);
                    $returnValue = $setting['value']; // return value is rgba
                    $value = $this->rgbaToHex($setting['value']); // storing value is hex
                } else if (in_array($setting['field'], array_keys($groupSetting['images']))) {
                    // as the target setting field is image file so handling the image settings key
                    if ($setting['value'] ?? false) {
                        if (in_array($setting['field'], config("kctadmin.constants.icons"))) {
                            $image = $this->resizeIcon($setting['value'])->stream();
                            $originalName = $setting['value']->getClientOriginalName();
                            $imagePath = config("kctadmin.constants.storage_paths.{$setting['field']}")
                                            . '/' . $originalName;
                            $this->services->fileService->storeFile($image->__toString(), $imagePath);
                        } else {
                            // uploading image and getting image path
                            $imagePath = $this->services->fileService->storeFile(
                                $setting['value'],
                                config("kctadmin.constants.storage_paths.{$setting['field']}")
                            );
                        }
                        // as return value is image full path
                        $returnValue = $this->services->fileService->getFileUrl($imagePath);
                        if ($setting['field'] == 'group_logo') {
                            // getting colors from the image
                            $colors = $this->services->colorExtService->getMainColors($setting['value']);
                            // handling the condition for main color as when image is upload main color needs to set
                            if (isset($colors[0]) && !in_array('main_color_1', $request->settings)) {
                                $this->repo->settingRepository->setSetting(
                                    $groupId,
                                    'main_color_1',
                                    ['main_color_1' => $this->rgbaToHex($colors[0])]
                                );
                                $return['main_color_1'] = $colors[0];
                            }
                            if (isset($colors[1]) && !in_array('main_color_2', $request->settings)) {
                                $this->repo->settingRepository->setSetting(
                                    $groupId,
                                    'main_color_2',
                                    ['main_color_2' => $this->rgbaToHex($colors[1])]
                                );
                                $return['main_color_2'] = $colors[1];
                            }
                        }
                        // and save value is just image path
                        $value = $imagePath;
                    } else {
                        // Here deleting the value
                        if ($setting['field'] == 'video_explainer_alternative_image') {
                            // event image is being deleted so set to default
                            $imagePath = $value = $this->services->superAdminService->getUserGridImage();
                            $returnValue = $this->services->fileService->getFileUrl($imagePath, false);
                            $return['is_default'] = 1;
                        } elseif ($setting['field'] == 'event_image') {
                            // event image is being deleted so set to default
                            $imagePath = $value = config('kctadmin.constants.event_default_image_path');
                            $returnValue = $this->services->fileService->getFileUrl($imagePath, false);
                            $return['is_default'] = 1;
                        } else if ($setting['field'] === 'group_logo') { // group logo is being deleted so set to default
                            $imagePath = $value = config('kctadmin.constants.group_logo_default_image');
                            $returnValue = $this->services->fileService->getFileUrl($imagePath, false);
                            $return['is_default'] = 1;
                            $this->repo->settingRepository->setSetting(
                                $groupId,
                                'main_color_1',
                                ['main_color_1' => config('kctadmin.default.group_settings.colors.main_color_1')]
                            );
                            $this->repo->settingRepository->setSetting(
                                $groupId,
                                'main_color_2',
                                ['main_color_2' => config('kctadmin.default.group_settings.colors.main_color_2')]
                            );
                            $return['main_color_1'] = $this->hexToRgba(config('kctadmin.default.group_settings.colors.main_color_1'));
                            $return['main_color_2'] = $this->hexToRgba(config('kctadmin.default.group_settings.colors.main_color_2'));
                        } elseif ($setting['field'] == 'group_logo') {
                            $imagePath = $value = config('kctadmin.constants.group_logo_default_image');
                            $returnValue = $this->services->fileService->getFileUrl($imagePath, false);
                            $return['is_default'] = 1;
                        } else {
                            // image key present but value is null so delete that image
                            $returnValue = $value = null;
                        }
                    }
                } else if (in_array($setting['field'], array_keys($groupSetting['checkboxes']))) {
                    // FR request for checkbox value(0,1)
                    $value = $returnValue = (int)$setting['value'];
                    if ($setting['field'] === 'group_has_own_customization') {
                        $applyCustomisation = $this->repo->settingRepository->getSettingByKey(
                            'apply_customisation',
                            $group->id
                        );
                        if ($value && $group->mainSetting->setting_value['allow_design_setting']
                            && $applyCustomisation->setting_value['apply_customisation']) {
                            $setting['value'] = [$setting['field'] => $value];
                            $this->repo->settingRepository->setSetting(
                                $groupId, $setting['field'], $setting['value']
                            );
                            $group->load(['allSettings' => function ($q) {
                                $q->whereIn('setting_key', $this->getGraphicKeys());
                            }]);
                            $designSetting = $group->allSettings;
                        } else {
                            $designSetting = $this->repo->settingRepository->getDefaultGroupSettings();
                        }
                    }
                } else if (in_array($setting['field'], array_keys($groupSetting['arrays']))) {
                    // FR request for arrays
                    $d = $this->services->groupService->updateArraySettings(
                        $groupId, $setting['value'], $setting['field']
                    );
                    $returnValue = $d['returnValue'];
                    $value = $rawInsert = $d['value'];
                } else {
                    $value = $returnValue = $setting['value'];
                }
                $setting['value'] = $rawInsert ?? [$setting['field'] => $value];
                $return[$setting['field']] = $returnValue;
                if (isset($colors)) {
                    $return['main_colors'] = $colors;
                }
                $data[] = $this->repo->settingRepository->setSetting(
                    $groupId, $setting['field'], $setting['value']
                );
            }
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => $return,
                'meta'   => [
                    'design_settings' => GroupSettingResource::collection($designSetting)
                ]
            ], 201);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/handler/zoom/oAuth/{groupKey}",
     *  operationId="handleZoomAuth",
     *  tags={"Zoom"},
     *  summary="To validate Zoom user",
     *  description="This will handle the login on zoom",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="query",description="Group key",required=true,@OA\Schema (type="string")),
     *  @OA\Response(
     *      response=200,
     *      description="Group Data Fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="string",description="Redirect url to the platform"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the zoom auth when the zoom authentication is completed this api/method will be called
     * back to update the zoom settings to database
     *
     * @algo
     * 1. Sync settings with default value if not created
     * 2. Fetching setting with respect to type of setting (default Zoom account or account specific Zoom account)
     * 3. As zoom provides the code in api request, and we need token to call further api so fetch token by code
     * 4. Get zoom current user data
     * 5. Get the plan and account details and store them for both webinar and meeting.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function handleZoomOAuth(Request $request, $groupKey) {
        try {
            DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->route()->parameters(),
                ['groupKey' => "required|exists:tenant.groups,group_key"]);
            if ($validator->fails()) {
                return $this->send422(implode(
                    ',',
                    $validator->errors()->all()),
                    $validator->errors()
                );
            }

            $code = $request->input('code');
            $type = $request->input('type');

            // to check and add the settings for zoom related if not
            $groupId = $this->repo->groupRepository->getGroupIdByKey($groupKey);
            $this->services->groupService->syncBroadcastingSettings($groupId);
            $this->services->zoomService->setEnvironment($type == 'default_zoom_settings' ? 2 : 1);


            if ($this->isZoomKey($type)) {
                // getting token from the request code by zoom
                $token = $this->services->zoomService->getTokenFromCode($code, $type, $groupKey);
                // fetching zoom user for account related data
                $zoomUser = $this->services->zoomService->getUserByToken($token['access_token']);
                if (!isset($zoomUser['account_number'])) {
                    throw new Exception('Invalid Code');
                }

                // storing token to respective key of db
                $setting = $this->services->zoomService->storeToken(
                    $token,
                    $type,
                    $zoomUser['account_number']
                );

                // plan details for hosts count
                $plan = $this->services->zoomService->getPlan($zoomUser['account_number']);

                // adding webinar data
                $hosts = $this->services->zoomService->getWebinarHosts();

                // syncing users of zoom with current system
                $users = $this->services->zoomService->syncUser($hosts);
                $webinarData = [
                    'available_license' => $plan['plan_webinar'][0]['hosts'] ?? 0,
                    'hosts'             => $users,
                ];

                // adding meeting data
                $hosts = $this->services->zoomService->getMeetingHosts();
                $users = $this->services->zoomService->syncUser($hosts);
                $meetingData = [
                    // as meeting host = total hosts - webinar hosts
                    'available_license' => ($plan['plan_base']['hosts'] ?? 0) - ($plan['plan_webinar'][0]['hosts'] ?? 0),
                    'hosts'             => $users,
                ];
                $previous = $setting->setting_value;
                $previous['webinar_data'] = $webinarData;
                $previous['meeting_data'] = $meetingData;
                $previous['enabled'] = 1;
                $previous['is_assigned'] = 1;
                $setting->setting_value = $previous;
                $setting->update();

                // if current enable disable other
                // only one at a time can be enabled;
                $this->services->zoomService->toggleSettings($setting->setting_key);

                DB::connection('tenant')->commit();
                return redirect(env("HOST_TYPE") . $this->tenant->hostname()->fqdn . "/oit/{$groupKey}/event-setting/technical-setting");
            }
            return response()->json(['status' => false], 500);
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }


}
