<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\KctAdmin\Http\Requests\V1\UpdateLabelRequest;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\V1\LabelResource;
use Modules\KctAdmin\Transformers\V1\TechnicalSettingResource;
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage app setting related logics and functionalities
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupController
 * @package Modules\KctAdmin\Http\Controllers\V1;
 */
class SettingController extends BaseController {
    use KctHelper;


    /**
     * @OA\Post(
     *  path="/api/v1/admin/labels",
     *  tags={"Label"},
     *  summary="To update the existing label",
     *  description="To udpate the multiple labels and some or all locales value for any available label",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UpdateLabelRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Labels updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *     @OA\Items(ref="#/components/schemas/LabelResource")),
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
     * @descripiton To update multiple labels with their locale for a specific group at once.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateLabelRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function updateLabel(UpdateLabelRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $names = [];
            $groupId = $this->repo->groupRepository->getGroupIdByKey($request->group_key);
            // updating the label locale value
            foreach ($request->input('labels') as $label) {
                $names[] = $label['name'];
                foreach ($label['locales'] as $locale) {
                    $labelLocale = $this->repo->labelRepository->getLocaleByName($label['name'], strtolower($locale['locale']), $groupId);
                    // updating the locale value
                    $labelLocale->value = $locale['value'];
                    $labelLocale->update();
                }
            }
            $labels = $this->repo->labelRepository->getLabelsByName($names, $groupId);
            DB::connection('tenant')->commit();
            return LabelResource::collection($labels)->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/labels/{groupKey}",
     *  operationId="getLabels",
     *  tags={"Label"},
     *  summary="To fetch labels list",
     *  description="To fetch all labels with all locales",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Group key",required=true,@OA\Schema (type="string")),
     *  @OA\Response(
     *      response=200,
     *      description="Labels fetched successfully",
     *       @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *     @OA\Items(ref="#/components/schemas/LabelResource")),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all the labels of a group by group id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return AnonymousResourceCollection
     */
    public function getLabels(Request $request, $groupKey) {
        try {
            $validator = Validator::make($request->route()->parameters(), [
                'groupKey' => 'required|exists:tenant.groups,group_key',
            ]);
            $validator->validate();
            $group = $this->repo->groupRepository->getGroupByGroupKey($groupKey);
            $labels = $this->repo->labelRepository->getAll($group->id);
            DB::connection('tenant')->commit();
            return LabelResource::collection($labels)->additional(['status' => true]);
        } catch (ValidationException $e) {
            return $this->send422($e->getMessage(), $e->errors());
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/groups/settings/technical/{groupKey}",
     *  operationId="getTechnicalSettings",
     *  tags={"Setting"},
     *  summary="To fetch technical setting",
     *  description="To fetch technical setting for group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Key of Group",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Technical Settings",
     *       @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",@OA\Items(ref="#/components/schemas/TechnicalSettingResource")),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all technical settings related to zoom
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getTechnicalSettings(Request $request, $groupKey) {
        try {
            $validator = Validator::make($request->route()->parameters(), [
                'groupKey' => 'required|exists:tenant.groups,group_key',
            ]);
            $validator->validate();
            $groupId = $this->repo->groupRepository->getGroupIdByKey($groupKey);
            $technicalSettings = $this->services->groupService->fetchTechnicalSettings($groupId);

            return TechnicalSettingResource::collection($technicalSettings)->additional(['status' => true]);
        } catch (ValidationException $e) {
            return $this->send422($e->getMessage(), $e->errors());
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/groups/settings/technical",
     *  operationId="getLabels",
     *  tags={"Group"},
     *  summary="To update technical settings",
     *  description="To update all technical settings of group",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="group_key",in="query",description="Group secret key",required=true,@OA\Schema(type="string")),
     *  @OA\Parameter(name="key",in="query",description="Key to update",required=true,@OA\Schema(type="string")),
     *  @OA\Parameter(name="data",in="query",description="Data",required=true,@OA\Schema(type="object",
     *     @OA\Property (property="is_assigned",type="integer"))),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *       @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",@OA\Items(ref="#/components/schemas/TechnicalSettingResource")),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton TO update the technical setting data related to zoom
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function updateTechnicalSettings(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'group_key'        => 'required|exists:tenant.groups,group_key',
                'key'              => 'required|in:' . implode(',', $this->getZoomKeys()),
                'data'             => 'required|array',
                'data.is_assigned' => 'nullable|integer|in:0'
            ]);
            $data = $request->input('data');
            $validator->validate();
            $groupId = $this->repo->groupRepository->getGroupIdByKey($request->group_key);
            $technicalSetting = $this->repo->settingRepository->getSettingByKey($request->input('key'), $groupId);
            $previous = $technicalSetting->setting_value;
            $previous['enabled'] = $data['enabled'] ?? $previous['enabled'];
            if (isset($data['is_assigned']) && !$data['is_assigned']) {
                // removing license so setting the things to initial state
                $previous = config("kctadmin.broadcast_keys." . $request->input("key"));
            }
            if (isset($data['enabled']) && $data['enabled']) {
                $this->services->zoomService->toggleSettings($request->input('key'));
            }
            $technicalSetting->setting_value = $previous;
            $technicalSetting->update();
            $technicalSettings = $this->services->groupService->fetchTechnicalSettings($groupId);
            return TechnicalSettingResource::collection($technicalSettings);
        } catch (ValidationException $e) {

            return $this->send422($e->getMessage(), $e->errors());
        }
    }


}
