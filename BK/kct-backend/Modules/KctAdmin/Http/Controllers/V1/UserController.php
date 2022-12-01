<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Http\Requests\UserBulkRoleRemove;
use Modules\KctAdmin\Http\Requests\UserBulkRoleUpdate;
use Modules\KctAdmin\Http\Requests\V1\EntityUserDeleteRequest;
use Modules\KctAdmin\Http\Requests\V1\ImportUserStep1Request;
use Modules\KctAdmin\Http\Requests\V1\ImportUserStep2Request;
use Modules\KctAdmin\Http\Requests\V1\UpdateUserRequest;
use Modules\KctAdmin\Http\Requests\V1\UserBulkCreateRequest;
use Modules\KctAdmin\Http\Requests\V1\UserBulkDeleteRequest;
use Modules\KctAdmin\Http\Requests\V1\UserFieldUpdateRequest;
use Modules\KctAdmin\Http\Requests\V1\UserRoleUpdateRequest;
use Modules\KctAdmin\Imports\UsersImport;
use Modules\KctAdmin\Rules\GroupRule;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\EntityResource;
use Modules\KctAdmin\Transformers\V1\EventUserResource;
use Modules\KctAdmin\Transformers\V1\HostResource;
use Modules\KctAdmin\Transformers\V1\LabelResource;
use Modules\KctAdmin\Transformers\V1\UserFullResource;
use Modules\KctAdmin\Transformers\V1\UserResource;
use Modules\SuperAdmin\Entities\User;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage all user related operations and functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UserController
 * @package Modules\KctAdmin\Http\Controllers\V1
 */
class UserController extends BaseController {
    use KctHelper;

    /**
     ** @OA\Post(
     *  path="/api/v1/admin/logout",
     *  tags={"Authenticate"},
     *  summary="To logout a user from admin panel",
     *  description="To logout a user from admin panel",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="User Logged out",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate user logged out successfully",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *------------------------------------------------------------------------------------------------------------------
     * @descripiton To make user log out.
     *------------------------------------------------------------------------------------------------------------------
     * @return JsonResponse
     */
    public function logout(): JsonResponse {
        Auth::user()->token()->revoke();
        return response()->json([
            'status' => true,
            'data'   => true,
        ]);
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/users/field",
     *  tags={"User"},
     *  summary="To update user profile single field",
     *  description="To update the possible profile fields for the user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/UserFieldUpdateRequest"),
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Users added",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/UserResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update user profile fields like password,lang and avatar.
     * It will take the field value from request and accordingly update the user's profile data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserFieldUpdateRequest $request
     * @return UserResource|JsonResponse
     */
    public function updateProfileByField(UserFieldUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userService->findById($request->input('user_id'));
            switch ($request->input('field')) {
                case 'lang':
                    $this->services->userService->updateUserLang($user->id, $request->input('value'));
                    break;
                case 'password':
                    if (!Hash::check($request->input('current_password'), $user->password)) {
                        return $this->send422(__("kctadmin::messages.invalid_password"));
                    } else {
                        $this->services->userService->updateUserById($user->id, [
                            'password' => Hash::make($request->input('value')),
                        ]);
                        $user->refresh();
                    }
                    break;
                case 'avatar':
                    if (!$request->hasFile('avatar') && $user->avatar) { // if user wants to delete profile pic
                        $this->services->fileService->deleteFile($user->avatar);
                        $this->services->userService->updateUserById($user->id, [
                            'avatar' => null,
                        ]);
                    } else { // if user wants to update profile pic by uploading from the system
                        $this->services->userService
                            ->uploadUserAvatar($user->id, $request->file('avatar'));
                    }
            }
            $user->refresh();
            DB::connection('tenant')->commit();
            return (new UserResource($user))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/users",
     *  tags={"User"},
     *  summary="To get the user by id or auth user",
     *  description="To get the user if id passed that user will return else auth user will return",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="id",in="query",description="ID of user",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Users Fetched",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/UserFullResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch an user data by user's Id.
     * @note If user Id in request is not sent then Auth user data will be returned.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|UserFullResource
     */
    public function getUserById(Request $request) {
        $labels = null;
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'nullable|exists:tenant.users,id',
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all()));
            }
            $user = $this->services->userService->findById($request->input("id") ?: Auth::user()->id);
            $user->load('group', 'group.organiser');
            $meta = [];
            if ($request->input('send_labels') == 1) {
                // Sending all the labels related to a specific group
                $labels = $this->repo->labelRepository->getAll($user->group->id ?? null);
                $meta['labels'] = LabelResource::collection($labels)->additional(['status' => true]);
                $meta['labels'] = $labels;
            }

            if ($user->id == Auth::user()->id) {
                $defaultGroup = $this->repo->groupRepository->getDefaultGroup();
                $currentGroup = $this->services->groupService->getUserCurrentGroup(Auth::id());
                // For super pilot and owner
                $isSuperPilot = $this->repo->groupUserRepository->isUserPilotOfGroup($defaultGroup->id);
                $isSuperOwner = $this->repo->groupUserRepository->isUserOwnerOfGroup($defaultGroup->id);
                // For pilot, co-pilot and owner
                $isPilot = $this->repo->groupUserRepository->isUserPilotOfGroup($currentGroup->id);
                $isCoPilot = $this->repo->groupUserRepository->isUserCopilotOfGroup($currentGroup->id);
                $isOwner = $this->repo->groupUserRepository->isUserOwnerOfGroup($currentGroup->id);

                $meta['is_super_pilot'] = $isSuperPilot ? 1 : 0;
                $meta['is_super_owner'] = $isSuperOwner ? 1 : 0;
                $meta['is_pilot']  = $isPilot ? 1 : 0;
                $meta['is_co_pilot'] =  $isCoPilot ? 1 : 0;
                $meta['is_owner'] =  $isOwner ? 1 : 0;
            }
            $accountSettings = $this->repo->settingRepository->getAccountSetting();
            $meta['is_multi_group_enable'] = $accountSettings['allow_multi_group'] ? 1 : 0;
            $meta['is_acc_analytics_enabled'] = array_key_exists('acc_analytics',$accountSettings)
                ? $accountSettings['acc_analytics']
                : 0;

            //Get setting data for managing pilot and owner and design setting
            $group = $this->services->groupService->getUserCurrentGroup(Auth::id());
            $mainSetting = $this->repo->settingRepository->getSettingByKey('main_setting', $group->id);
            $meta['allow_manage_pilots_owner'] = $this->services->groupService->isSuperPilotOrOwner() ? 1
                                                    : $mainSetting->setting_value['allow_manage_pilots_owner'];
            $meta['allow_design_setting']      = $this->services->groupService->isSuperPilotOrOwner() ? 1
                                                    : $mainSetting->setting_value['allow_design_setting'];

            //Get setting data for label customized
            $settingLabel = $this->repo->settingRepository->getSettingByKey('label_customized');

            $meta['organisation_name'] = $this->services->superAdminService->getOrganisation()->name_org;
            $allDayEvent = $this->services->eventService->getWaterFountainEvent();
            $accountSetting = $this->repo->settingRepository->getAccountSetting();
            $meta['all_day_event_enabled'] = (bool)($accountSetting['all_day_event_enabled'] ?? null) ? 1 : 0;
            if($allDayEvent) {
                $meta['all_day_event'] = [
                    'event_uuid' => $allDayEvent->event_uuid,
                ];
            }

            if($labels) {
                $meta['labels'] =  $labels;
                $meta['label_customized'] = $settingLabel->setting_value['label_customized'];
            }
            return (new UserFullResource($user))->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/users/multi",
     *  tags={"User"},
     *  summary="To add multiple users",
     *  description="To add multiple users at one click",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UserBulkCreateRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Users added",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/UserFullResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will create or update multiple users at a time and returns the total count of newly
     * created and updated users.
     * @note If email already exist in the account then the user's data will be updated
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserBulkCreateRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function addMultipleUser(UserBulkCreateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $inputUsers = $request->input('user');
            $users = new Collection();
            $group = $this->repo->groupRepository->getGroupIdByKey($request->input('group_key'));
            $groupRole = $request->input('group_role');
            $emails = Arr::pluck($request->user, 'email');
            $uniqueEmails = array_unique($emails);
            if (count($uniqueEmails) != count($emails)) { // checking if request has duplicate emails
                $duplicates = array_values(array_diff_assoc($emails, $uniqueEmails));
                $errors = [];
                foreach ($duplicates as $d) {
                    $indexes = array_values(array_keys($emails, $d));
                    foreach ($indexes as $i) {
                        $errors["user.$i.email"][] = __("validation.distinct", ['attribute' => 'email']);
                    }
                }
                return $this->send422(
                    __("validation.distinct", ['attribute' => 'email']), $errors
                );
            }

            // extracting existing users from requested users data
            $existingUsers = $this->services->userService
                ->getUserByEmail(array_column($inputUsers, 'email'), true);

            foreach ($inputUsers as $inputUser) {
                if ($user = $existingUsers->where('email', $inputUser['email'])->first()) {
                    // if user is deleted and request has that user's email then restoring the user
                    if ($user->trashed()) {
                        $user->restore();
                    }
                    if (!isset($request->event_uuid)) {
                        // method updateUser will update all user info like fname,city,mobile etc
                        $user = $this->services->userService->updateUser($user->email, $inputUser);
                    }
                    $this->repo->groupUserRepository->updateUserGroupRole($group, $groupRole, $user->id);
                    $current = $user;
                    $users->push($user);
                } else {
                    // preparing each user data to insert
                    $userData = $this->services->dataFactory->prepareUserCreateData($inputUser);
                    $current = $this->services->userService
                        ->createUser($userData, $group, $groupRole);
                    if (isset($userData['company'])) {
                        $this->services->userService->updateUserEntity($userData['company'], $current->id);
                    }
                    if (isset($userData['union'])) {
                        $this->services->userService->updateUserEntity($userData['union'], $current->id);
                    }
                    if(isset($inputUser['grade']) && $inputUser['grade']) {
                        $current->assignRole(strtolower($inputUser['grade']));
                    }
                    $users->push(
                        $current
                    );
                }
                if ($eventUuid = $request->input('event_uuid')) {
                    $this->repo->eventRepository->addUserToEvent($eventUuid, $current->id);
                }
            }

            DB::connection('tenant')->commit();
            return UserFullResource::collection($users)->additional([
                'status' => true,
                'meta'   => [
                    'created_count' => count($inputUsers) - $existingUsers->count(),
                    'updated_count' => $existingUsers->count(),
                ]
            ]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/admin/users/multi",
     *  tags={"User"},
     *  summary="To delete multiple users",
     *  description="To delete multiple users at one click",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UserBulkDeleteRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Users added",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="integer",description="Count of users deleted",example="1"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete multiple users at a time.
     * @note Users will be soft deleted from the account.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserBulkDeleteRequest $request
     * @return JsonResponse
     */
    public function removeMultipleUser(UserBulkDeleteRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = collect($request->user);
            $userId = $user->pluck('id');
            $data = $this->services->userService->removeUsers($userId);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/users",
     *  tags={"User"},
     *  summary="To update user profile fields",
     *  description="To update the user profiles",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/UserFullResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update user profile info like name,email,phone,company and union.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateUserRequest $request
     * @return UserFullResource|JsonResponse
     */
    public function updateUserProfile(UpdateUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userService->findById($request->input("id") ?: Auth::user()->id);
            $param = $this->services->dataFactory->prepareUserUpdateData($request);
            $this->services->userService->updateUserById($user->id, $param['user']);
            if ($user->hasRole(User::$userRoles)){
                $this->removeRoles(User::$userRoles,$user);
            }
            if($param['grade']) {
                $user->assignRole($param['grade']);
            }
            if ($param['company']) {
                $this->services->userService->updateUserEntity($param['company'], $user->id);
            }
            if ($param['unions']) {
                foreach ($param['unions'] as $union) {
                    $this->services->userService->updateUserEntity($union, $user->id);
                }
            }
            if ($request->has('email')) {
                // to update user's email if user is pilot of any group
                $data = $this->services->userService->getUserByEmail([$request->input('email')])->first();
                $userGroups = $this->services->userService->fetchUserGroups($data);
                $check = $this->services->userService->isUserPitotOfGroups($userGroups->pluck('group_id'));
                // Pilot can change the user's email until user has not verified his/her email
                if (count($check) && $data['email_verified_at'] != null) {
                    $data->email = $request->input('email');
                    $data->password = Hash::make($request->input('email'));
                    $data->update();
                }
            }

            $this->services->userService->updateUserMobile($user->id, $param, true);
            $user->refresh();
            $user->load(['primaryMobile', 'primaryPhone', 'company', 'unions', 'group',]);
            DB::connection('tenant')->commit();
            return new UserFullResource($user);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/users/import/step1",
     *  tags={"User"},
     *  summary="To import the users ",
     *  description="To upload the excel file and get the headers keys available in excel",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/ImportUserStep1Request"),
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *              @OA\Property(property="headings",type="array",
     *     description="String array of detected headings",@OA\Items(type="string", example="FirstName")),
     *              @OA\Property(property="multi_sheets_found",type="integer",
     *     description="To indicate if multiple sheets were found",example="0"),
     *              @OA\Property(property="file_name",type="string",
     *     description="Name of file saved",example="file_name.xlsx"),
     *              @OA\Property(property="match_template",type="integer",
     *     description="To indicate if headings matched with template",example="1"),
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is the first part of the user import feature.
     * It will upload csv file and extract the headings from csv file.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ImportUserStep1Request $request
     * @return JsonResponse
     */
    public function importStep1(ImportUserStep1Request $request): JsonResponse {
        try {
            $user = Auth::user();
            $file = $request->file('file');
            $fileName = Carbon::now()->timestamp . "_{$file->getClientOriginalName()}";
            $path = public_path() . "/temp_uploads/$user->id/users";
            $file->move($path, $fileName);
            $headings = (new HeadingRowImport)->toArray("$path/$fileName");
            $headingTemplate = config('kctadmin.user_import_heading');
            $users = null;
            if ($headingTemplate == $headings[0][0]) {
                // extracting the headings from file
                $user = Auth::user();
                $path = public_path() . "/temp_uploads/$user->id/users";
                $filePath = "$path/$fileName";
                $headings = (new HeadingRowImport)->toArray($filePath);
                // creating the aliases from file headings
                $aliases = array_combine($headingTemplate, $headingTemplate);
                $import = new UsersImport($headings[0][0] ?? [], $aliases);
                Excel::import($import, $filePath);
                $users = $import->getData();
            }
            return response()->json([
                'status' => true,
                'data'   => [
                    'headings'           => $headings[0][0] ?? [],
                    'multi_sheets_found' => count($headings) > 1 ? 1 : 0, // to indicate the worksheet has multi sheets
                    'file_name'          => $fileName,
                    'match_template'     => ($headingTemplate == $headings[0][0] ?? []) ? 1 : 0, // to show heading match template
                    'users'              => $users
                ],
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::connection('tenant')->rollBack();
            $errors = $e->failures();
            foreach ($errors as $error) {
                $errorMessage[] = $error->toArray();
            }
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'errors' => $errorMessage,
            ]);

        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/users/import/step2",
     *  tags={"User"},
     *  summary="To import the users ",
     *  description="To upload the excel file and get the headers keys available in excel",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/ImportUserStep2Request")),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422MaatwebResource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for matching the file headings aliases with system headings and prepare
     * the data to be inserted.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ImportUserStep2Request $request
     * @return JsonResponse
     */
    public function importStep2(ImportUserStep2Request $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = Auth::user();
            $path = public_path() . "/temp_uploads/$user->id/users";
            $filePath = "$path/{$request->input('file_name')}";
            // extracting headings from file
            $headings = (new HeadingRowImport)->toArray($filePath);
            $import = new UsersImport($headings[0][0] ?? [], $request->input('aliases'));
            Excel::import($import, $filePath);
            $data = $import->getData();
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => $data,
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // throwing error validation error in key value paired
            DB::connection('tenant')->rollBack();
            $errors = $e->failures();
            foreach ($errors as $error) {
                $errorMessage[] = $error->toArray();
            }
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'errors' => $errorMessage,
            ]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/entities/search",
     *  tags={"User"},
     *  summary="To Search the entity",
     *  description="To Search the entity",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="key",in="query",description="Key To Search",required=false),
     *  @OA\Parameter(name="type",in="query",description="Type of entity: 1 Company, 2 Union",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *              @OA\Items(ref="#/components/schemas/EntityResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422MaatwebResource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To search entity by entity type and name. Entity types are:- 1.Company 2.Union
     * @note If entered value or key is greater than two characters then only search will initiate.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchEntity(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => "nullable|integer",
            'key'  => "nullable|string",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()->all()], 422);
        }

        if (strlen($request->input('key')) > 2) { // if input key is greater then two characters
            return EntityResource::collection(
                $this->services->userService->searchEntity(
                    $request->input('type'),
                    $request->input("key")
                ))->additional([
                'status' => true,
            ]);
        }
        return response()->json([
            'status' => true,
            'data'   => true,
        ]);
    }
    /**
     * @OA\Delete (
     *  path="/api/v1/admin/users/entities",
     *  tags={"User"},
     *  summary="To detach an entity",
     *  description="To detach an entity from user's profile",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="user_id",in="query",
     *     description="User's id from which entity need to be detach",required=true),
     *  @OA\Parameter(name="entity_id",in="query",description="Entity id which need to be detached",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Entity detached successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          ),
     *      ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422MaatwebResource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for deleting an entity(company or union) from an user's profile.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EntityUserDeleteRequest $request
     * @return JsonResponse
     */
    public function detachEntity(EntityUserDeleteRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->services->userService->deleteUserEntity(
                $request->input('user_id'),
                $request->input('entity_id')
            );
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => true,
            ]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/users/search/{groupKey?}",
     *  operationId="searchUser",
     *  tags={"User"},
     *  summary="To search user",
     *  description="To search an user",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="groupKey",required=false),
     *  @OA\Parameter(name="key",in="query",description="Key to search",required=true),
     *  @OA\Parameter(name="mode",in="query",description="User data {'simple', 'extended'}",required=false),
     *  @OA\Parameter(name="filter",in="query",
     *     description="Type of filter, currently supporting : space_host, regular,group_user, group_organiser,add_participant",required=false),
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=false),
     *  @OA\Parameter(name="search",in="query",description="Type of search, currently supporting : fname, lname, email, union, position_union, company, position_company"),
     *  @OA\Response(
     *      response=200,
     *      description="List of Users",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *     description="To indicate server processed request properly",
     *     @OA\Items(ref="#/components/schemas/UserFullResource"),
     *     @OA\Items(ref="#/components/schemas/HostResource")),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for searching and filtering users through out the account.
     * It takes different filter parameters like space_host,organiser,group_user and add_participants and accordingly
     * it returns the data.
     * @note
     * 1. If request has filter add_participants then only those users will be fetched which are not added in a given
     * event.
     * 2. If request has group key then users will be fetched from that group only.
     * 3. Else users will be fetched from entire account.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param string|null $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchUser(Request $request, string $groupKey = null) {
        try {
            $request->merge(['groupKey' => $request->route('groupKey')]);
            $validator = Validator::make($request->all(), [
                'key'      => 'nullable|min:2',
                'mode'     => 'nullable|in:simple,extended',
                'filter'   => 'nullable|in:space_host,regular,group_user,group_organiser,add_participant',
//                'event_uuid' => ['nullable', 'required_if:filter,space_host', new EventRule],
                'groupKey' => ['nullable', 'required_if:filter,group_user,group_organiser', new GroupRule],
                'search'   => 'nullable|array',
                'search.*' => 'string|in:fname,lname,email,union,position_union,company,position_company',
                'row_per_page' => 'nullable|integer',
                'page'         => 'nullable|integer',
                'pagination'   => 'nullable|in:0,1',
            ]);
            $validator->validate();
            $isPaginated = $request->input('pagination');
            $limit = $request->input('row_per_page', 10);
            // if filter equal to add_participant then only those users will be fetched which are not
            // participants in the given event
            if ($request->input('filter') == 'add_participant') {
                $data = $this->services->userService->getUserNotInEvent($request->input('key'), $request->input('event_uuid'));
            } else {
                // here users will be fetched according to the group role for a specific group
                //if request have groupKey then we search in the group users
                if ($groupKey) {
                    $groupId = $this->repo->groupRepository->getGroupIdByKey($request->groupKey);
                    $data = $this->services->userService->getUserForSearch(
                        $request->input('key'),
                        $request->input('search', []),
                        [
                            'like'       => true,
                            'all_data'   => true,
                            'group_role' => $request->input('filter') == 'group_user' ? [GroupUser::$role_User] :
                                ($request->input('filter') == 'group_organiser' ? [GroupUser::$role_Organiser, GroupUser::$role_owner, GroupUser::$role_co_pilot] : null),
                            'group_id'   => $groupId,
                        ],
                    );
                } else {
                    // if request without groupKey then we search in the system users
                    $data = $this->services->userService->getUserForSearch(
                        $request->input('key'),
                        $request->input('search', []),
                        [
                            'like'       => true,
                            'all_data'   => true,
                            'group_role' => $request->input('filter') == 'group_user' ? [GroupUser::$role_User] :
                                ($request->input('filter') == 'group_organiser' ? [GroupUser::$role_Organiser, GroupUser::$role_owner, GroupUser::$role_co_pilot] : null),
//                            'group_id'   => $groupId,
                        ],
                    );
                }
//                $data->load('group');
            }
            $result = $this->handleDataPagination($data, $isPaginated, $limit);

            return $request->input('mode') == 'extended' ?
                UserFullResource::collection($result)->additional(['status' => true])
                : HostResource::collection($result)->additional(['status' => true]);
        } catch (ValidationException $e) {
            return $this->send422($e->getMessage(), $e->errors());
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/users/roles",
     *  tags={"User"},
     *  summary="To update user role",
     *  description="To update the user role",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UserRoleUpdateRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/UserFullResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will update the user role either in Group or in event.
     * If request has event_uuid then it will update the user's group role.
     * If request doesn't have event_uuid then it will update the user's event role accordingly
     * -----------------------------------------------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will update the user role either in Group or in event according to request
     * parameter provided.
     * @note:-
     * 1. If request has event_uuid then it will update the user's event role.
     * 2. If request doesn't have event_uuid then it will update the user's group role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserRoleUpdateRequest $request
     * @return JsonResponse|EventUserResource|UserFullResource
     */
    public function updateUserRole(UserRoleUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $auth = Auth::user();
            if ($auth->group->id ?? null) {
                $user = $auth->group->groupUser()->where('user_id', $request->input('user_id'))->first();
                if ($user) {
                    if ($request->input('event_uuid')){
                        // updating user's event role
                        $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
                        $userRoleData = $this->services->dataFactory->prepareUserRoleUpdate($request);
                        $event->eventUserRelation()->updateOrCreate(['user_id' => $request->user_id, 'event_uuid' => $request->event_uuid], $userRoleData);
                        $user = $this->services->userService->findById($request->input("user_id"));
                        $user->load('eventUser', 'group');
                        DB::connection('tenant')->commit();
                        return (new EventUserResource($user))->additional(['status'=> true]);
                    }else{
                        // updating user's group role
                        $user->role = $request->input('role');
                        $user->update();
                    }
                    $user = $this->services->userService->findById($request->input("user_id"));
                    $user->load('group', 'group.organiser', 'unions', 'company');
                    DB::connection('tenant')->commit();
                    return (new UserFullResource($user))->additional(['status' => true,]);
                } else {
                    return $this->send422(__('validation.exists', ['attribute' => 'group']));
                }
            }
            return $this->send422(__('validation.exists', ['attribute' => 'group']));
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

}
