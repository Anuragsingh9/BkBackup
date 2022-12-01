<?php

namespace Modules\Messenger\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Messenger\Http\Requests\AddUserToChannelRequest;
use Modules\Messenger\Http\Requests\ChannelDeleteRequest;
use Modules\Messenger\Http\Requests\ChannelForPersonalMessageRequest;
use Modules\Messenger\Http\Requests\ChannelUpdateRequest;
use Modules\Messenger\Http\Requests\CreateChannelRequest;
use Modules\Messenger\Http\Requests\HideUserFromPanelRequest;
use Modules\Messenger\Http\Requests\UserRemoveChannelRequest;
use Modules\Messenger\Service\ChannelService;
use Modules\Messenger\Service\MessageService;
use Modules\Messenger\Transformers\ChannelMessageCollection;
use Modules\Messenger\Transformers\ChannelResource;
use Modules\Messenger\Transformers\ChannelWithUserResource;
use Modules\Messenger\Transformers\ChannelUserResource;
use Modules\Messenger\Transformers\LoadPanelResource;
use Modules\Messenger\Transformers\MessageAttachmentResource;
use Nwidart\Modules\Collection;

class ChannelController extends Controller {
    protected $service;
    
    public function __construct() {
        $this->service = ChannelService::getInstance(); // now getting instance inside method so methods which not required service object will save heap memory
        
    }
    
    /**
     * @param CreateChannelRequest $request
     * @return JsonResponse|ChannelResource
     */
    public function create(CreateChannelRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $param = [
                'channel_name' => $request->channel_name,
                'channel_type' => 2, // 1 is for channel
                'is_private'   => $request->is_private, // 0 Public anyone can join , 1 Private invite users can join only
                'owner_id'     => Auth::user()->id, // Created By also
            ];
            $channel = $this->service->createChannel($param); // this method can also be used for workshop or personal
            DB::connection('tenant')->commit();
            return (new ChannelResource($channel))->additional(['status' => TRUE]); // API Resource
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500(__('message.IM.internalServerError'));
        }
    }
    
    /**
     * @param ChannelUpdateRequest $request
     * @return JsonResponse|ChannelResource
     */
    public function update(ChannelUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'channel_name' => $request->channel_name,
                'is_private'   => $request->is_private, // 0 Public anyone can join , 1 Private invite users can join only
            ];
            $channel = $this->service->update($param, $request->channel_uuid); // this will update and send channel model
            DB::connection('tenant')->commit();
            return (new ChannelResource($channel))->additional(['status' => TRUE]); // API Resource
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500(__('message.IM.internalServerError')); // used translate feature to show the language internal server error
        }
    }
    
    public function destroy(ChannelDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $channel = $this->service->deleteChannel($request->channel_uuid);
            DB::connection('tenant')->commit();
            return MessageService::send200('Deleted');
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500(__('message.IM.internalServerError')); // used translate feature to show the language internal server error
        }
    }
    
    /**
     * @param ChannelForPersonalMessageRequest $request
     * @return JsonResponse|ChannelResource
     * Only for user personal chat to return previous or create new if not exists channel between them
     */
    public function conversationOpen(ChannelForPersonalMessageRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $channel = $this->service->getUserPersonalChannel($request->user_id);
            if ($channel)
                return (new ChannelResource($channel))->additional(['user' => new ChannelUserResource($this->service->getUserById($request->user_id))]);
            $channelParam = [
                'channel_type' => 3, // 1-Workshop, 2-Channel, 3-Personal
                'is_private'   => 1,
                'user1_id'     => Auth::user()->id,
                'user2_id'     => $request->user_id,
            ];
            $channel = $this->service->createChannel($channelParam);
            DB::connection('tenant')->commit();
            return (new ChannelResource($channel))->additional(['user' => new ChannelUserResource($this->service->getUserById($request->user_id))]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return $e->getMessage();
            return MessageService::send500(__('message.IM.internalServerError')); // used translate feature to show the language internal server error
        }
    }
    
    /**
     * @param AddUserToChannelRequest $request
     * @return JsonResponse|ChannelWithUserResource
     */
    public function addUserToChannel(AddUserToChannelRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->addUser($request->channel_uuid, $request->user_id);
            $channelUsers = $this->service->getUsers($request->channel_uuid); // to return all users after adding a user
            DB::connection('tenant')->commit();
            return (new ChannelWithUserResource($channelUsers))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage()); // used translate feature to show the language internal server error
        }
    }
    
    /**
     * @param UserRemoveChannelRequest $request
     * @return JsonResponse|ChannelWithUserResource
     *
     * Delete Channel User relationship
     * Fetch the channel and its user and send
     */
    public function removeUserFromChannel(UserRemoveChannelRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->removeUser($request->channel_uuid, $request->user_id); // to actually remove user from channel
            $channelUsers = $this->service->getUsers($request->channel_uuid); // to return all users after adding a user
            DB::connection('tenant')->commit();
            return (new ChannelWithUserResource($channelUsers))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage()); // used translate feature to show the language internal server error
        }
    }
    
    public function hideUserFromPanel(HideUserFromPanelRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $result = $this->service->hideUserChannel($request->channel_uuid);
            DB::connection('tenant')->commit();
            return MessageService::send200((boolean)$result);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500();
        }
    }
    
    // GET API"s
    
    /**
     * @param $channelUuid
     * @return JsonResponse|ChannelWithUserResource
     *
     * Gets the users of the provided channel uuid
     */
    public function getUsers($channelUuid) {
        $users = $this->service->getUsers($channelUuid); // now getting instance inside method so methods which not required service object will save heap memory
        return $users ? (new ChannelWithUserResource($users))->additional(['status' => TRUE]) : MessageService::send200();
    }
    
    /**
     * @param Request $request
     * @param $channelUuid
     * @return JsonResponse|ChannelMessageCollection
     */
    public function getConversationHistory(Request $request, $channelUuid) {
        $channel = $this->service->getChannelMessages($channelUuid, $request->filter); // to get the messages which have channel uuid
        if ($channel) {
            $channel->memberCount = $this->service->getMemberCount(NULL, $channel); // to get the member counts
            $channel->isAdmin = $this->service->isAdminOfChannel(NULL, $channel);
            $channel ? $this->service->visitCurrentUser($channelUuid) : NULL; // create user channel visit to identify next time the unread messages which messages have created after this visited time
        }
        return $channel ? (new ChannelMessageCollection($channel->messages, $channel)) : MessageService::send200();
    }
    
    
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchUser(Request $request) {
        $result = $this->service->searchUser($request->key); // this will get the data from db
        return $result ? ChannelUserResource::collection($result)
            ->additional(['status' => TRUE]) : MessageService::send200();
        // as if result is null in case no data found or key length < 3 then API resource give error on null
    }
    
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function loadUsers() {
        $users = [];
        $users[] = $this->service->getPersonalChatUserIds(); // get users with whom started conversation
        $users[] = $this->service->getWorkshopsChatUserIds(); // get all workshops users
        $users[] = $this->service->getChannelUserIds(); // get user ids in which user is member
        $userIds = array_unique(array_merge($users[0], $users[1], $users[2], [Auth::user()->id] )); // unique all users so don't retrieve multiple time
        $users = User::whereIn('id', $userIds)->get();
        if ($users->count()) { // if some users found send as resource
            return ChannelUserResource::collection($users)->additional(['status' => TRUE]);
        }
        return response()->json(['status' => TRUE, 'data' => $users], 200);
    }
    
    /**
     * @return JsonResponse|LoadPanelResource
     */
    public function loadPanel() {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $data = new \stdClass();
            $data->lastChatChannel = $this->service->getLastChatChannel();
            $data->channels = $this->service->getChannels(); // to get the channels in which user is member
            $data->selfChannel = $this->service->getSelfChannel();
            $data->users = $this->service->getPersonalChatUsers($data->selfChannel); // get chats which have within 7 days of conversation
            $data->workshops = $this->service->getWorkshopsWithTopic(); // get workshops to which user belongs
            $data->eventsWorkshop = $this->service->getEventWorkshops(); // get event workshops if the event module is enabled
            DB::connection('tenant')->commit();
            return new LoadPanelResource(new Collection($data));
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    public function getWorkshopUsers(Request $request) {
        $result = $this->service->getWorkshopUsers($request->workshop_id);
        return $result->count() ? ChannelUserResource::collection($result)
            ->additional(['status' => TRUE]) : MessageService::send200();
    }
    
    public function getChannelFiles(Request $request, $channelUuid) {
        $files = $this->service->getChannelFiles($channelUuid);
        return $files->count() ? MessageAttachmentResource::collection($files) : MessageService::send200();
    }
    
}
