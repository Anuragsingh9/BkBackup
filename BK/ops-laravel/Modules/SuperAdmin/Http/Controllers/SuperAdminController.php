<?php

namespace Modules\SuperAdmin\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

use Auth;
use Excel;
use App\Exports\ModeratedTagsExport;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\SuperAdmin\Http\Requests\MergeTagRequest;
use Modules\SuperAdmin\Http\Requests\UpdateTagRequest;
use Modules\SuperAdmin\Services\TagService;
use Modules\SuperAdmin\Transformers\UserTagResource;

/**
 * Class SuperAdminController
 * @package Modules\SuperAdmin\Http\Controllers
 */
class SuperAdminController extends Controller {

    /**
     * @OA\GET(
     *  path="v1/superadmin/export-tags/{type}",
     *  operationId="exportTags",
     *  tags={"OPS - V1 - SUPERADMIN"},
     *  summary="To export the moderated tags in .xlsx",
     *  description="To export the moderated tags in .xlsx",
     *  @OA\Parameter(
     *      name="type",
     *      in="header",
     *      required=true,
     *      description="1 or 2",
     *      @OA\Schema(
     *           type="integer"
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Download the moderated tags list for selected tag type in .xlsx",
     *      @OA\JsonContent (),
     *   ),
     *  @OA\Response(
     *      response=403,
     *      description="User is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     * @param $type
     * @return Excel::download
    */
    public function exportTags($type) {
        if($type != null && ($type == 1 || $type == 2)) {
            $header = config('superadmin.export_moderated_tags_header');
            foreach ($header as $key => $value) {
                $headerRow[] = [$value[0]];
            }
            for($i = 0; $i < sizeof($headerRow); $i++)
                $headerRow[$i] = $headerRow[$i][0];
            $data = UserTag::where('tag_type', $type)->where('status', 1)->orderBy('tag_EN', 'asc')->get();
            if(!empty($data)) {
                $result = [];
                $data->map(function ($row) use (&$result) {
                    $result[] = [
                        $row->id,
                        $row->tag_EN,
                        $row->tag_FR,
                        $row->created_at,
                        $row->updated_at
                    ];
                });
            };
            $export = new ModeratedTagsExport(collect($result), $headerRow);
            $filename = $type == 1 ? 'Professional-Tags.xlsx' : 'Personal-Tags.xlsx';

            return Excel::download($export, $filename);
        } else {
            return response()->json(['status' => false,'data' => 'Correct Tag Type is required.'], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Show list of professional tag
     *  1. Fetch  professional tag
     *  2. Return professional tags to view.
     * -----------------------------------------------------------------------------------------------------------------
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showProTags(){

        $proTags = TagService::getInstance()->getProfessionalTags();
        $persoTags = [];
        return view('superadmin::super_admin.tag_management.tag_list',compact('proTags','persoTags'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Show list of personal tag
     *  1. Fetch personal tag
     *  2. Return personal tag to view
     * -----------------------------------------------------------------------------------------------------------------
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showPersoTags(){
        $persoTags = TagService::getInstance()->getPersonalTags();
        $proTags = [];
        return view('superadmin::super_admin.tag_management.tag_list',compact('persoTags','proTags'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Search tag by given key input
     *  1. Fetch tag name matching key input
     *  2. If found return matched tags else return no record found.
     * -----------------------------------------------------------------------------------------------------------------
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTags(Request $request){
        try {
            $validator =  Validator::make($request->all(), ['key' => 'required|string|size:3']);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $key = $request->key;
            $tags = UserTag::where(function ($q) use ($key) {
                $q->where('tag_EN', 'LIKE', "%$key%");
                $q->orWhere('tag_FR', 'LIKE', "%$key%");
            })->where('status', 'verified')->get();
            if ($tags) {
                return response()->json(['status' => true, 'data' => $tags], 200);
            }
            return response()->json(['status' => false, 'data' => 'No record found'], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Merge new tag into old tag
     * 1. Get the tag in which we need to merge new tag(tagId)
     * 2. Update new tag into old tag
     * 3. Delete old tag.
     * -----------------------------------------------------------------------------------------------------------------
     * @param MergeTagRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mergeTag(MergeTagRequest $request){
        try{
            DB::connection('tenant')->beginTransaction();
            $tagId = $request->id; // old tag id
            $tag = $request->tag; // tag name
            $deleteId = $request->deleteId; // new tag id
            $tagData = UserTag::where('id',$tagId)->first();
            $newTagData = [
                'tag_EN' => $tag,
                'tag_FR' => $tag,
            ];
            $tagData->update($newTagData);
            $this->deleteTag($deleteId);
            DB::connection('tenant')->commit();
            if ($tagData){
                return response()->json(['status'=> true, 'data' => true],200);
            }
        }catch (Exception $e){
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'nsg' => 'Internal Sever Error', 'error' => $e->getMessage()],500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Delete a tag by tag id.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $id
     */
    public function deleteTag($id){
        $deleteTag = UserTag::find($id);
        $deleteTag->delete();
    }

    /**
     * @OA\GET(
     *  path="v1/superadmin/accept-tag",
     *  operationId="acceptTag",
     *  tags={"OPS - V1 - SUPERADMIN"},
     *  summary="To change the unmoderated tag's type to verified",
     *  description="To change the unmoderated tag's type to verified",
     *  @OA\RequestBody(
     *    required=true,
     *    description="body to be requested",
     *    @OA\JsonContent(ref="#/components/schemas/UpdateTagRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent (ref="#/components/schemas/UserTagResource"),
     *   ),
     *  @OA\Response(
     *      response=403,
     *      description="User is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     * @param UpdateTagRequest $request
     * @return UserTagResource
    */
    public function acceptTag(UpdateTagRequest $request) {

        $updateTag = TagService::getInstance()->updateTag($request, $status="1");
        if($updateTag) {
            return UserTagResource::collection($updateTag)->additional(['status' => true]);
        } else {
            return response()->json(['status' =>  false, 'msg' => 'Internal Server Error'],500);
        }
    }

    /**
     * @OA\GET(
     *  path="v1/superadmin/reject-tag",
     *  operationId="rejectTag",
     *  tags={"OPS - V1 - SUPERADMIN"},
     *  summary="To change the unmoderated tag's type to rejected",
     *  description="To change the unmoderated tag's type to rejected",
     *  @OA\RequestBody(
     *    required=true,
     *    description="body to be requested",
     *    @OA\JsonContent(ref="#/components/schemas/UpdateTagRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent (ref="#/components/schemas/UserTagResource"),
     *   ),
     *  @OA\Response(
     *      response=403,
     *      description="User is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     * @param UpdateTagRequest $request
     * @return UserTagResource
    */
    public function rejectTag(UpdateTagRequest $request) {

        $updateTag = TagService::getInstance()->updateTag($request, $status="2");
        if($updateTag) {
            return UserTagResource::collection($updateTag)->additional(['status' => true]);
        } else {
            return response()->json(['status' =>  false, 'msg' => 'Internal Server Error'],500);
        }
    }

    /**
     * @OA\GET(
     *  path="v1/superadmin/update-tag/",
     *  operationId="updateTagName",
     *  tags={"OPS - V1 - SUPERADMIN"},
     *  summary="To change the unmoderated tag's name",
     *  description="To change the unmoderated tag's name",
     *  @OA\RequestBody(
     *    required=true,
     *    description="body to be requested",
     *    @OA\JsonContent(ref="#/components/schemas/UpdateTagRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent (ref="#/components/schemas/UserTagResource"),
     *   ),
     *  @OA\Response(
     *      response=403,
     *      description="User is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     * @param UpdateTagRequest $request
     * @return UserTagResource
    */
    public function updateTagName(UpdateTagRequest $request) {

        $updateTag = TagService::getInstance()->updateTag($request, null);
        if($updateTag) {
            return (new UserTagResource($updateTag))->additional(['status' => true]);
        } else {
            return response()->json(['status' =>  false, 'msg' => 'Internal Server Error'],500);
        }
    }


}
