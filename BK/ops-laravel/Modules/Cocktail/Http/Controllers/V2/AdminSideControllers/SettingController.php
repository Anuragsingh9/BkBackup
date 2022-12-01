<?php

namespace Modules\Cocktail\Http\Controllers\V2\AdminSideControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V2\GraphicsCustomizationRequest;
use Modules\Cocktail\Http\Requests\V2\OrgColorRequest;
use Modules\Cocktail\Http\Requests\V2\OrgLogoRequest;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Exception;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class SettingController extends Controller {
    
    /**
     * @var DataV2Service
     */
    private $coreService;
    
    /**
     * @var KctService
     */
    private $kctService;
    
    public function __construct() {
        $this->coreService = KctCoreService::getInstance();
        $this->kctService = KctService::getInstance();
    }
    
    /**
     *
     * @OA\POST(
     *  path="api/kct-admin/v2/org/default-logo",
     *  operationId="uploadLogo",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To upload the KCT default logo",
     *  description="This will upload the default logo for kct",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/OrgLogoRequest"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              description="KCT Default logo url",
     *              @OA\Property(
     *                  property="logo",
     *                  type="url",
     *                  description="KCT Default logo url",
     *                  example="https://[bucket_name].s3.amazonaws.com/a/b.png",
     *              ),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
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
     *  ),
     * ),
     *
     *
     * @param OrgLogoRequest $request
     * @return JsonResponse
     */
    public function uploadLogo(OrgLogoRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $path = config('cocktail.s3.default_graphics_logo');
            $url = $this->kctService->fileUploadToS3($path, $request->file('logo'), 'public');
            $this->coreService->setDefaultLogoUrl($url);
            $url = KctService::getInstance()->getCore()->getS3Parameter($url);
            DB::connection('tenant')->commit();
            return response()->json([
                'status'                       => true,
                'data'                         => ['logo' => $url,],
                'kct_graphics_logo_is_default' => 0, // as after uploading it will be not default logo
            ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     *
     * @OA\POST(
     *  path="api/kct-admin/v2/org/default-color",
     *  operationId="updateDefaultColor",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To upload the KCT default Color Vale",
     *  description="This will upload the default color value for kct event graphics",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/OrgColorRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              description="KCT Default logo url",
     *              @OA\Property(
     *                  property="transparency",
     *                  type="object",
     *                  description="KCT Color Value",
     *                  ref="#/components/schemas/DocColorObject",
     *              ),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
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
     *  ),
     * ),
     *
     *
     * @param OrgColorRequest $request
     * @return JsonResponse
     */
    public function updateDefaultColor(OrgColorRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $color = $request->input('value');
            $key = $request->input('field') == 'color1' ? 'kct_graphics_color1' : 'kct_graphics_color2';
            // as from front the color has string json value so decoding it else it will save as string.
            $result = $this->coreService->setKCTSettingValue($key, json_decode($color, 1));
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $result]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     *
     * @OA\Delete (
     *  path="api/kct-admin/v2/org/default-logo",
     *  operationId="deleteDefaultLogo",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To delete the KCT default Color Vale and set Org logo or ops logo",
     *  description="This will delete the default color value for kct event graphics and set the either organisation logo or ops logo",
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="url",
     *              description="URL of new default logo after delete",
     *              example="https://[bucket_name].s3.amazonaws.com/a/b.png",
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
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
     *  ),
     * ),
     *
     * @return JsonResponse
     */
    public function deleteDefaultLogo() {
        try {
            DB::connection('tenant')->beginTransaction();
            $url = KctCoreService::getInstance()->setDefaultLogo();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $url, 'kct_graphics_logo_is_default' => 1]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function graphicsCustomisation() {
        try {
            $settingData = KctCoreService::getInstance()->getCustomGraphicsSetting();
            return response()->json([
                'status' => true,
                'data' => KctCoreService::getInstance()->prepareCustomizationResource($settingData),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function updateGraphicsCustomisation(GraphicsCustomizationRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            KctCoreService::getInstance()->updateCustomGraphics(
                $request->input('field'),
                $request->input('value')
            );
            $settingData = KctCoreService::getInstance()->getCustomGraphicsSetting();
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data' => KctCoreService::getInstance()->prepareCustomizationResource($settingData),
            ]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
}
