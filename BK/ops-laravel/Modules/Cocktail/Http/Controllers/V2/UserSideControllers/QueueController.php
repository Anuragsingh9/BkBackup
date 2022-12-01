<?php

namespace Modules\Cocktail\Http\Controllers\V2\UserSideControllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Entities\EventUserTagRelation;
use Modules\Cocktail\Entities\UserCall;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Http\Requests\V2\UserQueRequest;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Exception;
use App\User;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Http\Requests\V2\QueueSearchRequest;
use Modules\Cocktail\Transformers\V2\UserSide\BadgeV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\Queue\IndexResource;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\Cocktail\Services\V2Services\DataMapService;
use Modules\Cocktail\Services\EventSpaceService;

class QueueController extends Controller
{
    /**
     * @param UserQueRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUserQueLogs(Request $request){
        try {
            DB::connection('tenant')->beginTransaction();
            $param = DataV2Service::getInstance()->prepareParamForQueueLog($request);
            $status = $request->input('status');
            if ($status == config('cocktail.que_log_status.missed')) {
                $logData = KctCoreService::getInstance()->createMissedLog($param);
                if ($logData) {
                    $data = 'Added Missed call.';
                }
            } else if($status == config('cocktail.que_log_status.rejected')) {
                $logData = KctCoreService::getInstance()->createRejectedLog($param);
                if ($logData) {
                    $data = 'Added rejected call.';
                }
            } else if($status == config('cocktail.que_log_status.answered')) {
                $joinConvo = EventSpaceService::getInstance()->joinWithConversation($param['to_id'], $param['space_uuid']);
                if($joinConvo) {
                    $param['conversation_uuid'] = $joinConvo['id'];
                    $getSpace = DataV2Service::getInstance()->getSpaceByConversation($param['conversation_uuid']);
                    $param['space_uuid'] = $getSpace->space_uuid;
                    $logData = KctCoreService::getInstance()->createAnsweredLog($param);
                    if ($logData) {
                        $data = 'Added answered call.';
                    }
                }
            }
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => true],201);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * get all missed calls to a user
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMissedCalls(Request $request){
        try{
            $userData = collect([]);
            $iMissedUserId = KctCoreService::getInstance()->getIMissedId($request);
            $theyMissedUserId = KctCoreService::getInstance()->getTheyMissedId($request);
            
            $userData->i_missed = User::with('union','companies','tagsRelationForPP')
                ->where(function ($q) use ($iMissedUserId){
                    $q->whereIn('id',$iMissedUserId);
                })->get();
            $userData->i_missed = KctCoreService::getInstance()->getPPTags($userData->i_missed);
            
            $userData->they_missed = User::with('union','companies','tagsRelationForPP')
                ->where(function ($q) use ($theyMissedUserId){
                    $q->whereIn('id',$theyMissedUserId);
                })->get();
            $userData->they_missed = KctCoreService::getInstance()->getPPTags($userData->they_missed);
            return IndexResource::collection($userData);
        }catch (Exception $e){
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * search user in queue
     * @param QueueSearchRequest $request
     * @return BadgeV2USResource::collection()
     */
    public function search(QueueSearchRequest $request) {
        $eventUuid = $request->event_id;
        $val = $request->key;
        $type = $request->type;
        $data = KctCoreService::getInstance()->searchQueueBuilder($eventUuid);
        if($type == 1) {
            $data = KctCoreService::getInstance()->searchByName($eventUuid, $val, $data);
        } else if($type == 2 || $type == 3) {
            $filter = ($type == 2) ? 'companies' : 'unions';
            $data = KctCoreService::getInstance()->searchByCompanyUnion($eventUuid, $val, $filter, $data);
        } else if($type == 4 || $type == 5) {
            $filter = ($type == 4) ? '1' : '2';
            $data = KctCoreService::getInstance()->searchByUserTags($eventUuid, $val, $filter, $data);
        }

        return BadgeV2USResource::collection($data);
    }

    /**
     * get all rejected calls to a user
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRejectedCalls(Request $request){
        try{
            $userId = UserCall::where(function ($q) use ($request){
                $q->where('status',config('cocktail.que_log_status.rejected'));
                $q->where('to_id', Auth::user()->id);
                $q->where('event_uuid',$request->event_uuid);
            })->get()->pluck('from_id','updated_at');
            $data=[];
            foreach ($userId as $time => $id){
                $da = User::where('id',$id)->first();
                $user = $da->fname . ' '. $da->lname;
                $data["$user"] = "$time";
            }
            return response()->json(['status' => true, 'data' => $data],200);
        }catch (Exception $e){
            return response()->json(['status' => false, 'msg' => $e->getMessage()],422);
        }
    }
}
