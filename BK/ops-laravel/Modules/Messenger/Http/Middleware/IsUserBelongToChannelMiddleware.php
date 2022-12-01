<?php

namespace Modules\Messenger\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Messenger\Service\AuthorizationService;
use Modules\Messenger\Service\ChannelService;

class IsUserBelongToChannelMiddleware {
    
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $whereToCheck) {
        $channelUuid = NULL;
        if ($whereToCheck == 'routeParam') {
            $channelUuid = $request->route('channelUuid');
        } else if ($whereToCheck == 'requestParam') {
            $channelUuid = $request->channel_uuid;
        } else if ($whereToCheck == 'messageId') {
            $channelUuid = AuthorizationService::getInstance()
                ->getChannelUuidFromMessageId($request->route('messageId'));
        } else if ($whereToCheck == 'attachmentId') {
            $channelUuid = AuthorizationService::getInstance()->getChannelUuidFromAttachment($request->attachment_id);
        }
        return
            ($channelUuid)
                ?
                (AuthorizationService::getInstance()->isUserBelongsToChannel($channelUuid, Auth::user()) ?
                    $next($request) :
                    response()->json(['status' => FALSE, 'msg' => 'You are not authorized to access this'], 403))
                :
                response()->json(['status' => FALSE, 'msg' => 'Record Not Found'], 422);
    }
    
    
}
