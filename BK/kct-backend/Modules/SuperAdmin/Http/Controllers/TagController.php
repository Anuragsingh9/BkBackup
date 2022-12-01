<?php

namespace Modules\SuperAdmin\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\SuperAdmin\Exceptions\SuCustomException;
use Modules\SuperAdmin\Http\Requests\TagRejectRequest;
use Modules\SuperAdmin\Http\Requests\TagUpdateRequest;
use Modules\SuperAdmin\Traits\SuHelper;
use Modules\SuperAdmin\Transformers\UserTagResource;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the tag management of the super admin
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class TagController
 * @package Modules\SuperAdmin\Http\Controllers
 */
class TagController extends BaseController {
    use SuHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To list the tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param string $tagType
     * @return Renderable
     */
    public function index(Request $request, string $tagType): Renderable {
        $tagType = (int)$tagType;
        $tags = $this->repo->tagRepository->getUnModeratedTagByType(
            $tagType,
            $request->input('perPage', 5)
        );
        // get the professional and personal tags
        $tagType_Professional = config('superadmin.models.userTag.tagType_Professional');
        $tagType_Personal = config('superadmin.models.userTag.tagType_Personal');

        // if user tag type is professional then get the unmoderated tag
        if ($tagType == config('superadmin.models.userTag.tagType_Professional')) {
            // if fr request have tag type of persnol tags then get the tags
            $unModeratedProfessionalCount = $tags->total();
            $unModeratedPersonalCount = $this->repo->tagRepository
                ->getUnModeratedTagByType(config('superadmin.models.userTag.tagType_Personal'))
                ->count();
        } else {// return the professional tags
            $unModeratedProfessionalCount = $this->repo->tagRepository
                ->getUnModeratedTagByType(config('superadmin.models.userTag.tagType_Professional'))
                ->count();
            $unModeratedPersonalCount = $tags->total();
        }

        return view('superadmin::tag-moderation.index')->with(compact(
            'tags',
            'tagType',
            'unModeratedPersonalCount',
            'unModeratedProfessionalCount',
            'tagType_Personal',
            'tagType_Professional'
        ));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the tag value for the specific locale
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param TagUpdateRequest $request
     * @return JsonResponse
     */
    public function updateTag(TagUpdateRequest $request): JsonResponse {
        try {
            $tagId = $request->input('id');
            $tagValue = $request->input('value');
            $locale = $request->input('locale');

            // Update the locale value
            $this->repo->tagRepository->updateTagLocaleValue($tagId, $tagValue, $locale);

            return response()->json([
                'status' => true,
                'data'   => true,
            ]);

        } catch (SuCustomException $exception) {
            return $exception->render();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for export the user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $tagType
     * @return BinaryFileResponse
     */
    public function export(string $tagType): BinaryFileResponse {
        // getting tags
        $tags = $this->repo->tagRepository->getModeratedTagsByType($tagType);
        $fileName = $tagType == 1 ? 'Professional-Tags.xlsx' : 'Personal-Tags.xlsx';
        return $this->services->exportService->exportUserTags($tags, $fileName);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To change the status of tag and mark it as accepted
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param TagRejectRequest $request
     * @return JsonResponse
     */
    public function acceptTag(TagRejectRequest $request): JsonResponse {
        try {
            $tagId = $request->input('id');
            $this->repo->tagRepository->updateTagStatus(
                $tagId,
                config('superadmin.models.userTag.status_Accepted')
            );

            return response()->json([
                'status' => true,
                'data'   => true,
            ]);

        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To change the status of tag and mark it as rejected
     * this will also remove the tag from respective user attached
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param TagRejectRequest $request
     * @return JsonResponse
     */
    public function rejectTag(TagRejectRequest $request): JsonResponse {
        try {
            $tagId = $request->input('id');
            //change the status of tag and mark it as rejected
            $this->repo->tagRepository->updateTagStatus(
                $tagId,
                config('superadmin.models.userTag.status_Rejected')
            );

            return response()->json([
                'status' => true,
                'data'   => true,
            ]);

        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method will use for search the tags
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchTag(Request $request) {
        try {
            $validator = Validator::make($request->all(), ['key' => 'required|string|min:3']);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg'    => implode(',', $validator->errors()->all())
                ], 422);
            }
            $key = $request->input('key');
            $tagType = $request->input('tagType');
            $userTags = $this->repo->tagRepository->searchTag($key, $tagType);
            // If user tag have value then send the user tag collection
            if ($userTags) {
                return UserTagResource::collection($userTags)->additional(['status' => true]);
            }
            return response()->json(['status' => false, 'data' => 'No record found'], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => 'Internal Server Error',
                'error'  => $e->getMessage()
            ], 500);
        }
    }
}
