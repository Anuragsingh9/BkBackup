<?php

namespace Modules\Cocktail\Http\Controllers\V1\AdminSideControllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V1\EventSpaceCreateRequest;
use Modules\Cocktail\Http\Requests\V1\EventSpaceResortingRequest;
use Modules\Cocktail\Http\Requests\V1\EventSpaceUpdateRequest;
use Modules\Cocktail\Http\Requests\V1\SpaceDeleteRequest;
use Modules\Cocktail\Http\Requests\V1\StockImageUploadRequest;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\DataService;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Transformers\AdminSide\EventSpaceResource;
use Modules\Cocktail\Transformers\AdminSide\UserResource;
use Modules\Cocktail\Transformers\OpeningHourResource;
use Modules\Events\Entities\Event;

class EventSpaceController extends Controller {
    
    protected $service;
    
    public function __construct() {
        $this->service = EventSpaceService::getInstance();
    }
    
    /**
     * @param EventSpaceCreateRequest $request
     * @return JsonResponse|EventSpaceResource
     */
    public function store(EventSpaceCreateRequest $request) {
        DB::connection('tenant')->beginTransaction();
        try {
            $dataService = DataService::getInstance();
            $param = $dataService->prepareSpaceCreateParam($request); // it will prepare array which gonna pass in model
            $event = $this->service->create($param);
            DB::connection('tenant')->commit();
            return (new EventSpaceResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
            
        }
    }
    
    /**
     * @param EventSpaceUpdateRequest $request
     * @return JsonResponse|EventSpaceResource
     */
    public function update(EventSpaceUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $dataService = DataService::getInstance();
            $param = $dataService->prepareSpaceUpdateParam($request); // it will prepare array which gonna pass in model
            $update = $this->service->update($param, $request->space_uuid);
            DB::connection('tenant')->commit();
            return (new EventSpaceResource($update))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param SpaceDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(SpaceDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $space = $this->service->deleteSpace($request->input('space_uuid'));
            if (!$space) throw new Exception();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'msg' => 'Space Deleted'], 200);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return JsonResponse|EventSpaceResource
     */
    public function getSpace(Request $request) {
        try {
            $space = EventSpace::with('hosts', 'event')->where('space_uuid', $request->space_uuid)->first();
            if (!$space || !$space->event) {
                throw new CustomValidationException('invalid_space', '', 'message');
            }
            if (!AuthorizationService::getInstance()->isUserEventAdmin($space->event_uuid)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.not_admin')], 403);
            }
            return (new EventSpaceResource($space))->additional([
                'status' => true,
                'data'   => ['event_status' => KctEventService::getInstance()->getEventStatus($space->event),],
            ]);
        } catch (CustomValidationException $e) {
            $e->render();
        }
    }
    
    /**
     * @param $eventUuid
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getEventSpacesForAdmin($eventUuid) {
        try {
            $spaces = $this->service->getSpacesList($eventUuid);
            $additional = $this->service->getSpaceListAdditional($eventUuid);
            return EventSpaceResource::collection($spaces)
                ->additional([
                    'status' => true,
                    'meta'   => $additional,
                ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg'    => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchEventUser(Request $request) {
        try {
            if (!AuthorizationService::getInstance()->isUserEventAdmin($request->input('event_uuid'))) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.not_admin')], 403);
            }
            if (strlen(trim($request->key)) >= 3) {
                $users = $this->service->searchUserForHost($request->key, $request->event_uuid);
                return UserResource::collection($users)->additional(['status' => true]);
            }
            return response()->json(['status' => true, 'data' => []], 200);
        } catch (CustomValidationException $e) {
            $result = $e->render();
        } catch (Exception $e) {
            $result = response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getTrace()], 500);
        }
        return $result;
    }
    
    /**
     * To upload the stock image and save it to s3 and return the s3 path
     *
     * @param StockImageUploadRequest $request
     * @return JsonResponse
     */
    public function uploadStockImage(StockImageUploadRequest $request) {
        try {
            $result = $this->service->uploadStockImage($request);
            return response()->json([
                // as to keep the old functionality remain same and to work on path instead of url so sending in path
                'status' => true, 'data' => $result['url'], 'path' => $result['path']
            ]);
        } catch (\App\Exceptions\CustomValidationException $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }
    
    public function resortSpaceList(EventSpaceResortingRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $result = $this->service->resortSpace($request->input('selected_space_uuid'), $request->input('offset'));
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $result], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
}

