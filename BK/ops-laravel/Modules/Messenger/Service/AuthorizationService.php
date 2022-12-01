<?php

namespace Modules\Messenger\Service;

use App\AccountSettings;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Carbon\Carbon;
use Exception;
use FG\ASN1\Universal\Integer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Events\Entities\Event;
use Modules\Messenger\Entities\Channel;
use Modules\Messenger\Entities\ChannelUser;
use Modules\Messenger\Entities\Message;
use Modules\Messenger\Entities\MessageMedia;
use Modules\Messenger\Entities\MessageReply;
use Modules\Messenger\Entities\UserChannelUserRelation;
use Modules\Messenger\Entities\UserChannelVisit;
use Modules\Messenger\Entities\WorkshopTopic;
use Modules\Messenger\Transformers\ChannelResource;
use Modules\Messenger\Transformers\ChannelUserResource;

class AuthorizationService {
    
    /**
     * @return static|AuthorizationService
     */
    public static function getInstance() {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static(); // by using static if a class inherit this class then by using this method we will get child class object
        }
        return $instance;
    }
    
    /**
     * @param $channelUuid
     * @param $user
     * @return bool
     *
     * For AUTHORIZATION PURPOSE
     */
    public function isUserBelongsToChannel($channelUuid, $user) {
        $channel = Channel::find($channelUuid);
        if ($channel && !(in_array($user->role, ['M0', 'M1']))) { // if channel found check its auth as owner otherwise validation will do its work
            if ($channel->channel_type == 1) { // workshop check user is workshop member or not
                $topic = WorkshopTopic::whereHas('workshop', function ($q) use ($user) {
                    $q->whereHas('meta', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->where('channel_uuid', $channelUuid)->count();
                return (boolean)$topic;
            } else if ($channel->channel_type == 2) {
                $user = ChannelUser::where('user_id', $user->id)
                    ->where('channel_uuid', $channelUuid)
                    ->count();
                return (boolean)$user;
            } else if ($channel->channel_type == 3) {
                $user = UserChannelUserRelation::where('channel_uuid', $channelUuid)->where(function ($q) use ($user) {
                    $q->where('user1_id', $user->id);
                    $q->orWhere('user2_id', $user->id);
                })->count();
                return (boolean)$user;
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    public function isUserBelongsToChannelOrWorkshop($channelUuid, $user) {
        $channel = Channel::find($channelUuid);
        if ($channel) { // if channel found check its auth as owner otherwise validation will do its work
            if ($channel->channel_type == 1) { // workshop check user is workshop member or not
                if (in_array($user->role, ['M0', 'M1'])) return TRUE;
                $topic = WorkshopTopic::whereHas('workshop', function ($q) use ($user) {
                    $q->whereHas('meta', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->where('channel_uuid', $channelUuid)->count();
                return (boolean)$topic;
            } else if ($channel->channel_type == 2) {
                if (in_array($user->role, ['M0', 'M1'])) return TRUE;
                $user = ChannelUser::where('user_id', $user->id)
                    ->where('channel_uuid', $channelUuid)
                    ->count();
                return (boolean)$user;
            } else if ($channel->channel_type == 3) {
                $user = UserChannelUserRelation::where('channel_uuid', $channelUuid)->where(function ($q) use ($user) {
                    $q->where('user1_id', $user->id);
                    $q->orWhere('user2_id', $user->id);
                })->count();
                return (boolean)$user;
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * @param $messageId
     * @return Integer|null
     */
    public function getChannelUuidFromMessageId($messageId) {
        $message = Message::with('channel')->whereHas('channel')->where('id', $messageId)->first();
        if ($message) {
            return $message->channel->uuid;
        }
        return NULL;
    }
    
    public function getChannelUuidFromAttachment($attachmentId) {
        $attachment = MessageMedia::with(['message' => function ($q) {
            $q->with('channel');
        }])->whereHas('message', function ($q) {
            $q->whereHas('channel');
        })->where('id', $attachmentId)->first();
        if ($attachment && $attachment->message && $attachment->message->channel) {
            return $attachment->message->channel->uuid;
        }
        return NULL;
    }
    
    public function checkUserBelongsToWorkshop($workshopId) {
        if (in_array(Auth::user()->role, ['M0', 'M1'])) return TRUE;
        $workshop = Workshop::with(['meta' => function ($q) {
            $q->where('user_id', Auth::user()->id);
        }])->whereHas('meta', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->where('id', $workshopId)->select('id')->first();
        return ($workshop) ? TRUE : FALSE;
    }
    
    public function isUserChannelAdmin($channel) {
        if ($channel->channel_type == 1) {
            $channel->load('topic');
            if ($channel->topic) {
                return $this->isUserWorkshopAdmin(Auth::user(), $channel->topic->workshop_id);
            }
        } else if ($channel->channel_type == 2) {
            return (boolean)$channel->owner_id == Auth::user()->id;
        } else if ($channel->channel_type == 3) {
            $user = UserChannelUserRelation::where('channel_uuid', $channel->uuid)->where(function ($q) {
                $q->where('user1_id', Auth::user()->id);
                $q->orWhere('user2_id', Auth::user()->id);
            })->count();
            return (boolean)$user;
        }
        return FALSE;
    }
    
    public function isUserWorkshopAdmin($user, $workshopId) {
        $metaFunction = function ($q) use ($user) {
            $q->where('user_id', $user->id);
            $q->whereIn('role', [1, 2]);
        };
        $workshop = Workshop::with(['meta' => $metaFunction])
            ->whereHas('meta', $metaFunction)
            ->find($workshopId);
        if ($workshop)
            return TRUE;
        return FALSE;
    }
}