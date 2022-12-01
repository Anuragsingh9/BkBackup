<?php

namespace Modules\Messenger\Http\Controllers;

use App\Notifications\MessengerPush;
use App\Notifications\WebPush;
use App\User;
use App\WorkshopMeta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Messenger\Entities\Channel;
use Modules\Messenger\Entities\UserChannelVisit;
use Modules\Messenger\Service\ChannelService;
use Modules\Messenger\Transformers\LoadPanelChannelResource;

class PushController extends Controller {
    /**
     * @param Request $request
     * @return JsonResponse|string
     * To store the push relation of user with browser id
     */
    public function store(Request $request) {
        try {
            $endpoint = $request->endpoint;
            $token = $request->keys['auth'];
            $key = $request->keys['p256dh'];
            $user = Auth::user();
            $user->updatePushSubscription($endpoint, $key, $token);
            return response()->json(['success' => TRUE], 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function push() {
        Notification::send(User::all(), new MessengerPush());
        return redirect()->back();
    }
    
    public function test() {
        return [
            ChannelService::getInstance()->getLastChatChannel() 
        ];
    }
}
