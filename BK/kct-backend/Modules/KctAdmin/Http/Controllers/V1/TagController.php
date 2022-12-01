<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Http\Requests\V1\EventTagDeleteRequest;
use Modules\KctAdmin\Http\Requests\V1\EventTagUpdateRequest;
use Modules\KctAdmin\Http\Requests\V1\EventTagCreateRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Transformers\V1\TagResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage tags related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class TagController
 * @package Modules\KctAdmin\Http\Controllers\V1;
 */
class TagController extends BaseController {

    /**
     * @OA\Get(
     *  path="/api/v1/admin/tags/all/{groupKey}",
     *  operationId="getGroupTags",
     *  tags={"Organiser Tag"},
     *  summary="To fetch organisers tag list of group",
     *  description="To fetch organisers tag list of group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Key of group",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Tag Updated",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *              description="Event Tag Data",@OA\Items(ref="#/components/schemas/TagResource")),
     *      ),
     *   ),
     *  @OA\Response(response=403,
     *     description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,
     *     description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,
     *     description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to getting group tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getGroupTags(Request $request,$groupKey) {
        try {
            $validator = Validator::make($request->route()->parameters(), [
                'groupKey' => 'required|exists:tenant.groups,group_key',
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all()));
            }
            $groupId = $this->repo->groupRepository->getGroupIdByKey($groupKey);
            $result = $this->repo->orgTagsRepository->getByGroupId(
                $groupId,
                'name',
                'asc'
            );
            return TagResource::collection($result)->additional(['status' => true]);
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/tags",
     *  operationId="store",
     *  tags={"Organiser Tag"},
     *  summary="To create a new Event Tag",
     *  description="To create a new Event Tag",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/EventTagCreateRequest"),
     *  ),
     *  @OA\Response(
     *      response=201,
     *      description="Event Tag Created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *              description="Event Tag Data",ref="#/components/schemas/TagResource"),
     *      ),
     *  ),
     * @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     * @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     * @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for creating the event tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventTagCreateRequest $request
     * @return JsonResponse | TagResource
     */
    public function store(EventTagCreateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $groupId = $this->repo->groupRepository->getGroupIdByKey($request->group_key);
            $result = $this->repo->orgTagsRepository->create(
                [
                    'name'       => $request->input('tag_name'),
                    'created_by' => Auth::user()->id,
                    'is_display' => 1,
                ], $groupId
            );

            DB::connection('tenant')->commit();
            return (new TagResource($result))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/tags",
     *  operationId="update",
     *  tags={"Organiser Tag"},
     *  summary="To update a event tag",
     *  description="To update a event tag",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/EventTagUpdateRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Event Tag Updated",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *              description="Event Tag Data",ref="#/components/schemas/TagResource"),
     *      ),
     *   ),
     * @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     * @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     * @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for updating the specified tag by tag name.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventTagUpdateRequest $request
     * @return JsonResponse|TagResource
     */
    public function update(EventTagUpdateRequest $request) {
        try {
            // Fetching the requested organiser tag
            $tag = $this->repo->orgTagsRepository->findById($request->input('tag_id'));
            if ($tag) {
                $tag->name = $request->input('tag_name');
                $tag->is_display = $request->input('is_display');
                $tag->save();
            }
            return (new TagResource($tag))->additional(['status' => true]);
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }


    /**
     * @OA\Delete(
     *  path="/api/v1/admin/tags",
     *  operationId="destroy",
     *  tags={"Organiser Tag"},
     *  summary="To delete a event tag",
     *  description="To delete a event tag",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/EventTagDeleteRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Deleted Event tag Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *              description="Event Resource",@OA\Items(ref="#/components/schemas/TagResource")),
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
     * @description Remove the specified tag by tag name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventTagDeleteRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function destroy(EventTagDeleteRequest $request) {
        try {
            $this->repo->orgTagsRepository->deleteById($request->input('tag_id'));
            $result = $this->repo->orgTagsRepository->getOrderByNameAsc();
            return TagResource::collection($result)->additional(['status' => true]);
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }

}
