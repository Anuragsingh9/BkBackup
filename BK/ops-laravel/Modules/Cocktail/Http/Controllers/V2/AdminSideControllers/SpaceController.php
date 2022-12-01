<?php

namespace Modules\Cocktail\Http\Controllers\V2\AdminSideControllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V2\CreateSpaceRequestV2;
use Modules\Cocktail\Http\Requests\V2\UpdateSpaceRequestV2;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\AdminSide\EventSpaceResource;
use Modules\Cocktail\Transformers\V2\AdminSide\SpaceResourceV2;
use Modules\Events\Exceptions\CustomException;

class SpaceController extends Controller {
    /**
     * @OA\POST(
     *  path="api/kct-admin/v2/spaces",
     *  operationId="storeSpaceV2",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To create a new Space",
     *  description="To create a new Space",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/CreateSpaceRequestV2"),
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
     *              description="Space Resource",
     *              ref="#/components/schemas/SpaceResourceV2",
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
     * @param CreateSpaceRequestV2 $request
     * @return JsonResponse|SpaceResourceV2
     */
    public function store(CreateSpaceRequestV2 $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = DataV2Service::getInstance()->spaceCreateParam($request);
            $space = EventSpaceService::getInstance()->create($param);
            $space->load('event');
            $meta = KctCoreService::getInstance()->metaForEventVersion($space->event);
            DB::connection('tenant')->commit();
            return (new SpaceResourceV2($space))->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        
        return $result;
    }
    
    /**
     * @OA\PUT(
     *  path="api/kct-admin/v2/spaces",
     *  operationId="updateSpaceV2",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To update Space",
     *  description="To update Space",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/UpdateSpaceRequestV2"),
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
     *              description="Space Resource",
     *              ref="#/components/schemas/SpaceResourceV2",
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
     * @param UpdateSpaceRequestV2 $request
     * @return JsonResponse|SpaceResourceV2
     */
    public function update(UpdateSpaceRequestV2 $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = DataV2Service::getInstance()->spaceUpdateParam($request);
            $space = EventSpaceService::getInstance()->update($param, $request->input("space_uuid"));
            $space->load('event');
            $meta = KctCoreService::getInstance()->metaForEventVersion($space->event);
            DB::connection('tenant')->commit();
            return (new SpaceResourceV2($space))->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => 'Internal server error'], 500);
        }
        return $result;
    }
    
    public function getSpace(Request $request) {
        try {
            $space = EventSpace::with('hosts', 'event')->where('space_uuid', $request->space_uuid)->first();
            if (!$space || !$space->event) {
                throw new CustomValidationException('invalid_space', '', 'message');
            }
            if (!AuthorizationService::getInstance()->isUserEventAdmin($space->event_uuid)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.not_admin')], 403);
            }
            
            $space->load('event');
            $meta = KctCoreService::getInstance()->metaForEventVersion($space->event);
            $meta['is_bj_event'] = $space->event->bluejeans_settings['event_uses_bluejeans_event'];
            
            return (new SpaceResourceV2($space))->additional([
                'status' => true,
                'data'   => ['event_status' => KctEventService::getInstance()->getEventStatus($space->event),],
                'meta'   => $meta,
            ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        }
    }
    
    public function getEventSpacesForAdmin($eventUuid) {
        try {
            $spaces = EventSpaceService::getInstance()->getSpacesList($eventUuid);
            $additional = EventSpaceService::getInstance()->getSpaceListAdditional($eventUuid);
            $meta = KctCoreService::getInstance()->metaForEventVersion($eventUuid);
            $meta = array_merge($additional, $meta);
            return SpaceResourceV2::collection($spaces)
                ->additional([
                    'status' => true,
                    'meta'   => $meta,
                ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
}
