<?php

namespace Modules\Messenger\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Messenger\Http\Requests\TopicCreateRequest;
use Modules\Messenger\Http\Requests\UpdateTopicRequest;
use Modules\Messenger\Service\ChannelService;
use Modules\Messenger\Service\MessageService;
use Modules\Messenger\Service\TopicService;
use Modules\Messenger\Transformers\TopicResource;

class TopicController extends Controller {
    /**
     * @param TopicCreateRequest $request
     * @return JsonResponse|TopicResource
     * This will create topic along with channel
     */
    public function create(TopicCreateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $param = [
                'topic_name'  => $request->topic_name,
                'workshop_id' => $request->workshop_id, // workshop id in which topic will belongs
            ];
            $topicService = TopicService::getInstance(); // now getting instance inside method so methods which not required service object will save heap memory
            $topic = $topicService->create($param);  // this will create topic and return the topic model
            DB::connection('tenant')->commit();
            return new TopicResource($topic); // API Resource
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500(__('message.IM.internalServerError'));
        }
    }
    
    /**
     * @param UpdateTopicRequest $request
     * @return JsonResponse|TopicResource
     */
    public function update(UpdateTopicRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models which extends TenantModel only
            $param = [
                'topic_name' => $request->topic_name,
            ];
            $topicService = TopicService::getInstance(); // now getting instance inside method so methods which not required service object will save heap memory
            $topic = $topicService->update($param, $request->topic_id);// this will update the topic and
            DB::connection('tenant')->commit();
            return new TopicResource($topic); // API Resource
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500(__('message.IM.internalServerError'));
        }
    }
    
    
}
