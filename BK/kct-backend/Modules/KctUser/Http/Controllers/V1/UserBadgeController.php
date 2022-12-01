<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\UserTag;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Http\Requests\V1\AttachUserTagRequest;
use Modules\KctUser\Http\Requests\V1\BadgeTagDelete;
use Modules\KctUser\Http\Requests\V1\BadgeUpdateUSRequest;
use Modules\KctUser\Http\Requests\V1\CreateTagRequest;
use Modules\KctUser\Http\Requests\V1\RemoveUserTagRequest;
use Modules\KctUser\Http\Requests\V1\UpdatePersonalInfoRequest;
use Modules\KctUser\Http\Requests\V1\UpdateUserVisibilityRequest;
use Modules\KctUser\Http\Requests\V1\UserEntityDeleteRequest;
use Modules\KctUser\Http\Requests\V1\UserUpdateEntityRequest;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Transformers\V1\BadgeUSResource;
use Modules\KctUser\Transformers\V1\EntitySearchResource;
use Modules\KctUser\Transformers\V1\UserTagUSResource;
use Modules\UserManagement\Entities\Entity;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class contain the user badge functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserBadgeController
 *
 * @package Modules\KctUser\Http\Controllers\V1
 */
class UserBadgeController extends BaseController {
    use KctHelper;

    /**
     * @OA\Get(
     *  path="/api/v1/p/badge",
     *  operationId="us-getBadge",
     *  tags={"USAPI1- User Profile"},
     *  summary="To get the user badge",
     *  description="To get the user badge",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/BadgeUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user badge
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|BadgeUSResource
     */
    public function getBadge(Request $request) {
        try {
            $user = $this->services->userService->getUserBadge(
                Auth::user()->id,
                $request->input('eventId')
            );
            $user->load('userVisibility');
            return (new BadgeUSResource($user))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/badges/visibility",
     *  operationId="us-updateVisibility",
     *  tags={"USAPI1- User Profile"},
     *  summary="To get the user level settings",
     *  description="To get the user level settings",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/UpdateUserVisibilityRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",description="Data Result",ref="#/components/schemas/BadgeUSResource",),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update the visibility of user badge fields like lname,company,unions,personal tags,professional
     * tags in HE(Attendee side).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateUserVisibilityRequest $request
     * @return JsonResponse|BadgeUSResource
     */
    public function updateVisibility(UpdateUserVisibilityRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            $previousData = $user->userVisibility;
            // If user have previous data then prepare the new data
            if ($previousData) {
                $newData = $previousData->fields;
                $newData[$request->input('field')] = $request->input('value') ? 1 : 0;
                $param = [
                    'user_id' => Auth::user()->id,
                    'fields'  => $newData
                ];
            } else {
                $fields = [
                    $request->input('field') => $request->input('value') ? 1 : 0,
                ];
                $param = [
                    'user_id' => Auth::user()->id,
                    'fields'  => $fields,
                ];
            }
            //Update the user setting data
            $this->services->userManagementService->updateUserVisibility($param['user_id'], $param); //todo
            DB::connection('tenant')->commit();
            // Get the user full badge
            $user = $this->services->userService->getUserBadge(Auth::user()->id, $request->input('event_uuid'));
            $user->load('userVisibility');
            return (new BadgeUSResource($user))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'error'  => $exception->getMessage(),
                'trace'  => $exception->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/badges/profiles",
     *  operationId="us-updateProfileField",
     *  tags={"USAPI1- User Profile"},
     *  summary="To update user profile fields",
     *  description="To update user profile fields",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/BadgeUpdateUSRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/BadgeUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * ---------------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used for update the user profile details
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param BadgeUpdateUSRequest $request
     * @return JsonResponse|BadgeUSResource
     */
    public function updateProfileField(BadgeUpdateUSRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            // if frontend have request to upload image then upload the image else reutrn value
            $value = $request->field == 'avatar'
                ? $this->services->userManagementService->uploadUserAvatar($request->value)
                : $request->value;
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            $field = $request->field;
            if ($field === 'bg_image') { // Handling the request for updating the user's background image
                $field = 'setting';
                $setting = $user->setting;
                $setting['bg_image'] = $request->value;
                $value = $setting;
            }
            $user->$field = $value;
            $user->save();
            DB::connection('tenant')->commit();
            return (new BadgeUSResource(
                $this->services->userService->getUserBadge(
                    Auth::user()->id,
                    $request->input('event_uuid')
                )
            ))->additional(['status' => true],);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'error'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/badges/entities",
     *  operationId="us-updateEntity",
     *  tags={"USAPI1- User Profile"},
     *  summary="To update user profile entity relations",
     *  description="To update user profile entity relations",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/UserUpdateEntityRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/BadgeUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating the entity data(1.company 2.union).
     * @note :-
     * 1.If request has entity id it means we need to update the entity.
     * 2.Else if request has entity name it means we need create new entity with that name.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserUpdateEntityRequest $request
     * @return JsonResponse|BadgeUSResource
     */
    public function updateEntity(UserUpdateEntityRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $data = [];
            if ($request->input('entity_type') == Entity::$type_companyType) {
                $request->merge(['entity_type' => Entity::$type_companyType]);
                $type = Entity::$type_companyType;
            } else {
                $request->merge(['entity_type' => Entity::$type_unionType]);
                if ($request->input('entity_old_id')) { // added $u check to handle if user only send old id
                    $data['old_entity_id'] = $request->input('entity_old_id');
                }
                $type = Entity::$type_unionType;
            }

            if ($request->input('entity_id')) {
                // merge the entity id with data
                $data = array_merge($data, ['id' => $request->input('entity_id')]);
            } else if ($request->input('entity_name')) {
                $data = array_merge($data, [
                    'long_name'      => $request->input('entity_name'),
                    'entity_type_id' => $type,
                ]);
            }
            $data['position'] = $request->input('position');
            $data['entity_type_id'] = $type;
            // Update the user profile entity
            $this->services->userManagementService->updateUserEntity(Auth::user()->id, $data);
            DB::connection('tenant')->commit();
            return (new BadgeUSResource($this->services->userService->getUserBadge(
                Auth::user()->id, $request->input('event_uuid'))
            ))->additional(['status' => true]
            );
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'error'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/p/users/badges/entities",
     *  operationId="us-deleteEntity",
     *  tags={"USAPI1- User Profile"},
     *  summary="To delete user profile entity fields",
     *  description="To delete user profile entity fields",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/UserEntityDeleteRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *               example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/BadgeUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for deleting an entity(company and union)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserEntityDeleteRequest $request
     * @return JsonResponse|BadgeUSResource
     */
    public function deleteEntity(UserEntityDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $removeEntityUser = $this->repo->userRepository->removeEntity(
                Auth::user()->id,
                $request->input('entity_id')
            );
            if (!$removeEntityUser) {
                throw new Exception();
            }
            $user = $this->services->userService->getUserBadge(Auth::user()->id, $request->input('event_uuid'));
            DB::connection('tenant')->commit();
            return (new BadgeUSResource($user))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/users/badges/entity/search/{val}/{type}",
     *  operationId="us-searchEntity",
     *  tags={"USAPI1- User Profile"},
     *  summary="To search the entity",
     *  description="To search the entity",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="val",in="path",description="Keyword to search",required=true),
     *  @OA\Parameter(name="type",in="path",description="Entity Type",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="data",type="array",description="Data Result",
     *              @OA\Items(ref="#/components/schemas/EntitySearchResource")
     *          )
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for searching the entity(1.company 2.union)
     * @note Search will initiate when the key's value will be greater than 2
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $val
     * @param $type
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchEntity($val, $type) {
        $type = $this->castEntityTypeToNew($type);
        if (strlen($val) >= 3) {
            // $val length is greater than or equal to 3
            $entities = $this->services->userManagementService->searchEntity(
                $type,
                $val,
                true,
            );
        } else {
            $entities = null;
        }
        return $entities ? EntitySearchResource::collection($entities) : response()->json([]);
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/p/users/profiles/pictures",
     *  operationId="us-deleteProfilePicture",
     *  tags={"USAPI1- User Profile"},
     *  summary="To delete user profile picture",
     *  description="To delete user profile picture",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/BadgeUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for deleting the user profile picture
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return JsonResponse|BadgeUSResource
     */
    public function deleteProfilePicture(Request $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            if ($user->avatar) {
                $this->services->fileService->deleteFile($user->avatar);
                $user->avatar = null;
                $user->update();
            }
            $user = $this->services->userService->getUserBadge($user->id, $request->input('event_uuid'));
            DB::connection('tenant')->commit();
            return (new BadgeUSResource($user))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/info/update",
     *  operationId="us-updatePersonalInfo",
     *  tags={"USAPI1- User Profile"},
     *  summary="To update the personal info of user",
     *  description="To update the personal info of user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/UpdatePersonalInfoRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="array",description="Data Result",
     *              @OA\Items(ref="#/components/schemas/BadgeUSResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating the user personal information
     * like avatar, company, company position, mobile number, union, union position, internal id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdatePersonalInfoRequest $request
     * @return JsonResponse|BadgeUSResource
     */
    public function updatePersonalInfo(UpdatePersonalInfoRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            $personalInfo = $user->personalInfo;
            if (!$personalInfo) { // if the user personal information not found
                $user->personalInfo()->create([
                    'fields' => [$request->input('field') => $request->input('value'),]
                ]);
            } else { // else updating the user personal information
                $fields = $personalInfo->fields;
                $fields[$request->input('field')] = $request->input('value');
                $personalInfo->fields = $fields;
                $personalInfo->update();
            }
            $user = $this->services->userService->getUserBadge(
                Auth::user()->id,
                $request->input('event_uuid')
            );
            $user->load('userVisibility');
            DB::connection('tenant')->commit();
            return (new BadgeUSResource($user))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/users/tag/create",
     *  operationId="us-createUserTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To create a new user tag",
     *  description="To create a new user tag",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/CreateTagRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/UserTagUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for creating the user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateTagRequest $request
     * @return JsonResponse|UserTagUSResource
     */
    public function createUserTag(CreateTagRequest $request) {
        try {
            DB::beginTransaction();
            $lang = strtoupper(App::getLocale());
            $tag = $this->services->superAdminService->findTagByName(
                $request->input("tag_name"),
                $request->input('tag_type'),
                $lang
            );
            if ($tag) {
                if ($tag->status == 1) {
                    // same tag exists with same name and tag type and its verified
                    return response()->json([
                        'status' => false,
                        'msg'    => __('validation.unique', ['attribute' => 'Tag Name'])],
                        422
                    );
                } else if ($tag->status == 2) {
                    // if tag found and its already rejected make it pending now
                    $tag->status = 3;
                    $tag->save();
                }
            } else {
                $tag = $this->services->superAdminService->createTag(
                    $request->input('tag_name'),
                    $request->input('tag_type')
                );
            }
            UserTag::firstOrCreate([
                'user_id' => Auth::user()->id,
                'tag_id'  => $tag->id,
            ]);
            DB::commit();
            return (new UserTagUSResource($tag))->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/users/tag/search",
     *  operationId="us-searchTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To search the user tag",
     *  description="To search the user tag",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="key",in="query",description="Keyword to search",required=true),
     *  @OA\Parameter(name="tag_type",in="query",description="Entity Type",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="data",type="array",description="Data Result",
     *              @OA\Items(ref="#/components/schemas/UserTagUSResource")
     *          )
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for the searching the user(PPT) tags.
     * @info PPT = Personal and Professional Tag
     * 1.Professional Tag
     * 2.Personal Tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchTag(Request $request) {
        if ($request->has('key') && strlen($request->input('key')) >= 3) {
            $key = $request->input('key');
            $lang = strtolower(App::getLocale());
            $tags = $this->services->superAdminService->getTagByKey(
                $key,
                $lang,
                $request->input('tag_type')
            );
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            $user->load('tagsRelationForPP');
            // get the user tag ides
            $usedTags = $user->tagsRelationForPP->pluck('tag_id')->toArray();
            $tags = $tags->filter(function ($tag) use ($usedTags) {
                return !in_array($tag->id, $usedTags);
            });
            return UserTagUSResource::collection($tags);
        }
        return response()->json([
            'status' => false,
            'msg'    => __('validation.min.numeric', ['attribute' => 'key', 'min' => 3]),
        ], 422);
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/p/users/tag/delete",
     *  operationId="us-removeTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To unattach a tag from user",
     *  description="To unattach a tag from user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/RemoveUserTagRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              ref="#/components/schemas/UserTagUSResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used to remove the user tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param RemoveUserTagRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function removeTag(RemoveUserTagRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            UserTag::whereUserId(Auth::user()->id)->whereTagId($request->input('tag_id'))->delete();

            $tag = $this->services->superAdminService->getTagById($request->input('tag_id'));
            $tags = $this->services->kctService->getUserTags($tag->tag_type);
            DB::connection('tenant')->commit();
            return UserTagUSResource::collection($tags)->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/tag/attach",
     *  operationId="us-attachTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To attach a tag with user",
     *  description="To attach a tag with user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/AttachUserTagRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property(property="data",type="array",description="Data Result",
     *              @OA\Items(ref="#/components/schemas/UserTagUSResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used is used for attaching an user tag(PPT tags) to user's profile.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param AttachUserTagRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function attachTag(AttachUserTagRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $tag = $this->services->superAdminService->getTagById($request->input('tag_id'));

            // link the tag to user
            $this->repo->userTagsRepository->addTagToUser(Auth::user()->id, $request->input('tag_id'));
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            //get the tag relation with user
            $tagRelation = $user->tagsRelationForPP;
            $tags = $this->services->superAdminService->getAllTags();
            $tags = $tags->whereIn('id', $tagRelation->pluck('tag_id'))->where('tag_type', $tag->tag_type);
            DB::connection('tenant')->commit();
            return UserTagUSResource::collection($tags)->additional(['status' => true]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/add-tag",
     *  operationId="us-addUserTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To create a organiser tag with user",
     *  description="To create a organiser tag with user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/BadgeTagDelete")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              @OA\Property(property="used_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *              @OA\Property(property="unused_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *         )
     *      )
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for adding the user tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param BadgeTagDelete $request
     * @return JsonResponse
     */
    public function addUserTag(BadgeTagDelete $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $tags = $this->services->kctService->addUserTag($request);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tags], 200);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/tag-delete",
     *  operationId="us-deleteUserTag",
     *  tags={"USAPI1- User Tags"},
     *  summary="To remove a organiser tag from user",
     *  description="To remove a organiser tag from user",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/BadgeTagDelete")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              @OA\Property(property="used_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *              @OA\Property(property="unused_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *         )
     *      )
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for deleting user tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param BadgeTagDelete $request
     * @return JsonResponse
     */
    public function deleteUserTag(BadgeTagDelete $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $tags = $this->services->kctService->deleteTagUser($request);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tags], 200);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/get-user-tags",
     *  operationId="us-getUserTags",
     *  tags={"USAPI1- User Tags"},
     *  summary="To get a organiser tag attached with user",
     *  description="To get a organiser tag attached with user",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="Response",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Data Result",
     *              @OA\Property(property="used_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *              @OA\Property(property="unused_tag",type="array",description="Data Result",example="[]",
     *                  @OA\Items(type="object")
     *              ),
     *         )
     *      )
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetching  all user tags related to the auth(logged in) user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return JsonResponse
     */
    public function getUserTags(): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $tag_detail = $this->services->userService->getUserTag(Auth::user());
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tag_detail]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }

}
