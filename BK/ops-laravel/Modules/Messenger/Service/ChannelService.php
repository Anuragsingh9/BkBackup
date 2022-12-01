<?php

namespace Modules\Messenger\Service;

use App\AccountSettings;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Carbon\Carbon;
use Exception;
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

class ChannelService {
    
    /**
     * @return static|ChannelService
     */
    public static function getInstance() {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static(); // by using static if a class inherit this class then by using this method we will get child class object
        }
        return $instance;
    }
    
    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function createChannel($param) {
        $channel = Channel::create($param);
        if (!$channel) {
            throw new Exception();  // to throw the error instead of null so proper message can be shown
        }
        // to create relation for channel 3 as personal message
        if (isset($param['channel_type']) && $param['channel_type'] == 3) { // if type 3 personal then create user relation also
            $userPersonalRelation = UserChannelUserRelation::create(array_merge($param, ['channel_uuid' => $channel->uuid]));
            if (!$userPersonalRelation)
                throw new Exception();  // to throw the error instead of null so proper message can be shown  // to throw the error instead of null so proper message can be shown
        } else if (isset($param['channel_type']) && $param['channel_type'] == 2) { // channel ,
            $this->addUser($channel->uuid, Auth::user()->id); // to make the creator as member of the channel also
        }
        
        return $channel;
    }
    
    /**
     * @param $param
     * @param $channelUuid
     * @return mixed
     * @throws Exception
     */
    public function update($param, $channelUuid) {
        $channel = Channel::find($channelUuid); // find channel by uuid
        if ($channel->update($param))  // this will update channel and by find we can directly return updated channel instead of fetching again
            return $channel;
        throw new Exception();  // to throw the error instead of null so proper message can be shown
    }
    
    public function deleteChannel($channelUuid) {
        $delete = [];
        $channel = Channel::find($channelUuid);
        switch ($channel->channel_type) {
            case 1: // Deleting Topics Message
                $delete['topics'] = WorkshopTopic::where('channel_uuid', $channelUuid)->delete();
                $messages = Message::where('channel_uuid', $channelUuid)->get();
                $delete['messages'] = $messages->count();
                $messages->map(function ($row) { // as just putting delete in builder will not invoke boot method so replies , and other will not be deleted
                    $row->delete();
                });
                $delete['visit_record'] = UserChannelVisit::where('channel_uuid', $channelUuid)->delete();
                $channel->delete();
                break;
            case 2: // Channel Message Delete
                $delete['channel_user'] = ChannelUser::where('channel_uuid', $channelUuid)->delete();
                $messages = Message::where('channel_uuid', $channelUuid)->get();
                $delete['messages'] = $messages->count();
                $messages->map(function ($row) {  // as just putting delete in builder will not invoke boot method so replies , and other will not be deleted
                    $row->delete();
                });
                $delete['visit_record'] = UserChannelVisit::where('channel_uuid', $channelUuid)->delete();
                $channel->delete();
                break;
        }
        return $delete;
    }
    
    /**
     * @param $channelUuid
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public function addUser($channelUuid, $userId) {
        $channelUser = ChannelUser::updateOrCreate(
            ['channel_uuid' => $channelUuid, 'user_id' => $userId],
            ['channel_uuid' => $channelUuid, 'user_id' => $userId]
        );
        if (!$channelUser) // to throw the error so proper msg can be sent to front end
            throw new Exception();  // to throw the error instead of null so proper message can be shown
        return $channelUser;
    }
    
    /**
     * @param $userId
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed|UserChannelUserRelation|object|null
     * @throws Exception
     * To get the user personal conversations
     */
    public function getUserPersonalChannel($userId) {
        $relation = UserChannelUserRelation::with(['channel' => function ($q) {
        }])
            ->where(function (Builder $q) use ($userId) {
                $q->where('user1_id', $userId);
                $q->where('user2_id', Auth::user()->id);
            })
            ->orWhere(function (Builder $q) use ($userId) {
                $q->where('user1_id', Auth::user()->id);
                $q->where('user2_id', $userId);
            })->first();
        if ($relation)
            return $relation->channel;
        return NULL;
    }
    
    
    /**
     * @param $channelUuid
     * @param $userId
     * @return mixed
     */
    public function removeUser($channelUuid, $userId) {
        return ChannelUser::where(['channel_uuid' => $channelUuid, 'user_id' => $userId])->delete();
    }
    
    public function hideUserChannel($channelUuid) {
        return UserChannelVisit::where('channel_uuid', $channelUuid)
            ->where('user_id', Auth::user()->id)
            ->update(['is_hidden' => 1]);
    }
    
    /**
     * @param $channelUuid
     * @return mixed
     * Visit the current channel by user to show message after this for channel will be as unread
     */
    public function visitCurrentUser($channelUuid) {
        $channel = '';
        return UserChannelVisit::updateOrcreate(
            [
                'user_id'      => Auth::user()->id,
                'channel_uuid' => $channelUuid,
            ],
            [
                'is_hidden'       => 0,
                'last_visited_at' => Carbon::now('Europe/Paris'),
            ]);
    }
    
    public function getUserById($id) {
        return User::find($id);
    }
    
    /**
     * @param $channelUuid
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Channel|Channel[]|null
     */
    public function getUsers($channelUuid) {
        return Channel::with('users')->find($channelUuid);
    }
    
    /**
     * Will return channel details along with channel messages like member count and is_admin
     *
     * @param $channelUuid
     * @param $filter
     * @return mixed
     */
    public function getChannelMessages($channelUuid, $filter) {
        $perPage = 50;
        $channel = Channel::with(['topic', 'secondUserOfPersonalChat'])->find($channelUuid);
        if ($channel) {
            switch ($filter) { // may be more filters can come in future
                case 'stared' :
                    $paginatedMessage = $channel->messages()
                        ->whereHas('isStared')
                        ->orderBy('id', 'desc')
                        ->paginate($perPage);
                    break;
                default:
                    $paginatedMessage = $channel->messages()->with(['replies' => function ($q) {
                        $q->orderBy('id', 'desc');
                    }])->orderBy('id', 'desc')->paginate($perPage);
            }
            $channel->files_counts = $this->getChannelFiles($channelUuid)->count();
            $reverse = new Collection();
            $paginatedMessage->map(function ($row) use (&$reverse) {
                $reverse->prepend($row);
            });
            $channel->messages = new LengthAwarePaginator($reverse, $paginatedMessage->total(), $perPage, $paginatedMessage->currentPage(), ['path' => $paginatedMessage->toArray()['path']]);
        }
        return $channel;
    }
    
    public function getMemberCount($channelUuid = NULL, $channel = NULL) {
        $members = 0;
        if ($channelUuid) { // to get the channel if not provided
            $channel = Channel::find($channelUuid);
        }
        if ($channel) { // now channel will have either send by param or will be fetched by uuid
            switch ($channel->channel_type) {
                case 1: // topic members
                    $topic = WorkshopTopic::where('channel_uuid', $channel->uuid)->first();
                    if ($topic && $topic->workshop) {
                        $members = $topic->workshop->meta_data()->groupBy('user_id')->get()->count();
                    }
                    break;
                case 2: // channel users
                    $members = $channel->users->count();
                    break;
                case 3: // personal conversation
                    $members = 2;
            }
        }
        return $members;
        
    }
    
    public function isAdminOfChannel($channelUuid = NULL, $channel = NULL) {
        $isAdmin = FALSE;
        if ($channelUuid) { // to get the channel if not provided
            $channel = Channel::find($channelUuid);
        }
        if ($channel) { // now channel will have either send by param or will be fetched by uuid
            if (in_array(Auth::user()->role, ['M0', 'M1'])) {
                $isAdmin = TRUE;
            } else {
                switch ($channel->channel_type) {
                    case 1: // topic members
                        $topic = WorkshopTopic::where('channel_uuid', $channel->uuid)->first();
                        if ($topic && $topic->workshop) {
                            $isAdmin = (boolean)$topic->workshop->meta_data()
                                ->whereIn('role', [1, 2])
                                ->where('user_id', Auth::user()->id)
                                ->get()->count();
                        }
                        break;
                    case 2: // channel users
                        $isAdmin = $channel->owner_id == Auth::user()->id;
                        break;
                    case 3: // personal conversation
                        $isAdmin = TRUE;
                }
            }
        }
        return $isAdmin ? 1 : 0;
    }
    
    /**
     * @param $key
     * @return null|Collection
     *
     * Matches first name, last name, email, and concat f/l name
     */
    public function searchUser($key) {
        $result = NULL;
        if (strlen(trim($key)) >= 3) { // min 3 letters must be
            $result = User::where(function (Builder $q) use ($key) {
                $q->orWhere('fname', 'like', "%$key%");
                $q->orWhere('lname', 'like', "%$key%");
                $q->orWhere(DB::raw("CONCAT(fname,' ', lname)"), 'like', "%$key%");
                $q->orWhere('email', 'like', "%$key%");
            })->where('id', '!=', Auth::user()->id)
                ->get();
        }
        return $result;
    }
    
    /**
     *  Get user where user1 = auth
     *  Get user where user2 = auth
     *  Convert both to array ex [1,2,3] , [2,3,4]
     *  Merge them [1,2,3,2,3,4]
     *  Union them [0=>1,1=>2,4=>3,6=>4] // unwanted keys
     *  Get values only as union put key also which is unwanted
     * @return array
     */
    public function getPersonalChatUserIds() {
        $users1 = UserChannelUserRelation::where('user1_id', Auth::user()->id)->select('user2_id')->pluck('user2_id');
        $users2 = UserChannelUserRelation::where('user2_id', Auth::user()->id)->select('user1_id')->pluck('user1_id');
        return array_values(array_unique(array_merge($users1->toArray(), $users2->toArray())));
    }
    
    /**
     * Get workshops in which user is added
     * Then get workshops all user
     * @return array
     */
    public function getWorkshopsChatUserIds() {
        if (in_array(Auth::user()->role, ['M0', 'M1'])) {
            $users = WorkshopMeta::selectRaw('DISTINCT(user_id)')
                ->pluck('user_id');
        } else {
            $workshops = WorkshopMeta::where('user_id', Auth::user()->id)
                ->selectRaw('DISTINCT(workshop_id)')
                ->pluck('workshop_id');
            $users = WorkshopMeta::whereIn('workshop_id', $workshops->toArray())
                ->selectRaw('DISTINCT(user_id)')
                ->pluck('user_id');
        }
        return $users->toArray();
    }
    
    /**
     * Get channels in which user is memeber
     * get channels all users
     * @return array
     */ 
    public function getChannelUserIds() {
        if (in_array(Auth::user()->role, ['M0', 'M1'])) {
            $users = ChannelUser::selectRaw('DISTINCT(user_id)')
                ->pluck('user_id');
        } else {
            $channels = ChannelUser::where('user_id', Auth::user()->id)
                ->selectRaw('DISTINCT(channel_uuid)')
                ->pluck('channel_uuid');
            $users = ChannelUser::whereIn('channel_uuid', $channels->toArray())
                ->selectRaw('DISTINCT(user_id)')
                ->pluck('user_id');
        }
        return $users->toArray();
    }
    
    /**
     * get those workshop in which current user is as member
     *
     * @param $workshopType // to get normal or events workshops
     * @return Workshop[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getWorkshopsWithTopic($workshopType = '') {
        $checkMeta = function ($q) {
            $q->where('user_id', Auth::user()->id);
        };
        $withTopic = function ($q) {
            $q->with(['channel' => function ($q) {
                $q->with('unreadMessages');
            }]);
        };
        $isAdmin = function ($q) {
            $q->whereIn('role', [1, 2]);
            $q->where('user_id', Auth::user()->id);
        };
        
        if (Auth::user()->role == 'M0' || Auth::user()->role == 'M1') {
            $workshopBuilder = Workshop::with([
                'meta'     => $checkMeta,
                'imTopics' => $withTopic,
                'meta_data'
            ])
                ->withCount(['meta' => $isAdmin, 'meta_data']);
        } else {
            $workshopBuilder = Workshop::with([
                'meta'     => $checkMeta,
                'imTopics' => $withTopic,
                'meta_data'
            ])
                ->withCount(['meta' => $isAdmin, 'meta_data'])
                ->whereHas('meta', $checkMeta);
        }
        if ($workshopType == 'event') {
            if ($this->isEventEnabled()) {
                return $workshopBuilder
                    ->where('is_qualification_workshop', 3)
                    ->orderBy('workshop_name')
                    ->withoutGlobalScopes()
                    ->get();
            }
            return NULL;
        }
        return $workshopBuilder
            ->where('code1', '!=', 'NSL') // not to include newsletter workshops
            ->where('is_qualification_workshop', '!=', 3) // not to include events workshop HERE
            ->orderBy('workshop_name')
            ->withoutGlobalScopes()
            ->get();
        
    }
    
    public function getEventWorkshops() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $accountSetting = AccountSettings::where('id', $this->tenancy->hostname()['id'])->first(['setting']);
        if ($accountSetting && $accountSetting->setting && isset($accountSetting->setting['event_enabled'])) {
            return $this->getWorkshopsWithTopic('event');
        }
        return NULL;
    }
    
    public function getLastChatChannel() {
        return UserChannelVisit::where('user_id', Auth::user()->id)->orderBy('last_visited_at', 'desc')->first();
    }
    
    /**
     * To check that event module is enabled or not
     *
     * @return bool
     */
    public function isEventEnabled() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $hostname = $this->tenancy->hostname()->id;
        $account_setting = AccountSettings::where('account_id', $this->tenancy->hostname()->id)->first();
        if ($account_setting && $account_setting->setting && isset($account_setting->setting['event_enabled'])) {
            return (boolean)$account_setting->setting['event_enabled'];
        }
        return FALSE;
    }
    
    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|Channel[]
     * get the channels in which user is member
     */
    public function getChannels() {
        if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
            return Channel::withCount('users')->with(['unreadMessages'])
                ->where('channel_type', 2)
                ->orderBy('channel_name')
                ->get();
            
        } else {
            return Channel::withCount('users')->with(['unreadMessages'])
                ->whereHas('users', function ($q) {
                    $q->where('users.id', Auth::user()->id);
                })
                ->where('channel_type', 2)
                ->orderBy('channel_name')
                ->get();
        }
    }
    
    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|UserChannelUserRelation[]
     * Get user personal conversations
     */
    public function getPersonalChatUsers($selfChannel) {
        $id = Auth::user()->id;
        $users = UserChannelUserRelation::with(['channel' => function ($q) {
            $q->with('unreadMessages');
        }])
            ->whereHas('channel', function ($q) {
                $q->whereHas('channelVisited', function ($q) {
                    $q->where('last_visited_at', '>', Carbon::now('Europe/Paris')->subDays(6));
                    $q->where(function ($q) {
                        $q->where('is_hidden', '!=', 1);
//                        $q->orWhereNull('is_hidden');
                    });
                    $q->where('user_id', Auth::user()->id);
                });
            })
            ->where(function ($q) {
                $q->where('user1_id', Auth::user()->id);
                $q->orWhere('user2_id', Auth::user()->id);
            })
            ->where('channel_uuid', '!=', $selfChannel->uuid)
            ->select('im_user_channel_user.id', 'channel_uuid', 'users.*')
            ->join('users', 'users.id', '=', DB::raw(" CASE WHEN user1_id=$id THEN user2_id WHEN user2_id=$id THEN user1_id ELSE user1_id END "))
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
        return $users;
    }
    
    
    public function getWorkshopUsers($workshopId) {
        $workshop = WorkshopMeta::where('workshop_id', $workshopId)->select(DB::raw('DISTINCT(user_id)'))->get();
        return User::whereIn('id', $workshop->pluck('user_id'))->get();
    }
    
    public function getChannelFiles($channelUuid) {
        $channelMessage = function ($q) use ($channelUuid) {
            $q->where('channel_uuid', $channelUuid);
        };
        $replyMessage = function ($q) {
            $q->with('attachments');
            $q->whereHas('attachments');
        };
        $messagesMedias = MessageMedia::with(['message' => $channelMessage])
            ->where('attachmentable_type', Message::class)
            ->whereHas('message', $channelMessage)
            ->get();
        $repliesMedias = MessageMedia::where('attachmentable_type', MessageReply::class)
            ->with(['reply' => function ($q) use ($channelUuid) {
                $q->with(['message' => function ($q) use ($channelUuid) {
                    $q->where('channel_uuid', $channelUuid);
                }]);
            }])
            ->whereHas('reply', function ($q) use ($channelUuid) {
                $q->whereHas('message', function ($q) use ($channelUuid) {
                    $q->where('channel_uuid', $channelUuid);
                });
            })
            ->get();
        return $messagesMedias->merge($repliesMedias);
    }
    
    /**
     * @throws Exception
     */
    public function getSelfChannel() {
        $channel = $this->getUserPersonalChannel(Auth::user()->id);
        if ($channel)
            return $channel;
        $channelParam = [
            'channel_type' => 3, // 1-Workshop, 2-Channel, 3-Personal
            'is_private'   => 1,
            'user1_id'     => Auth::user()->id,
            'user2_id'     => Auth::user()->id,
        ];
        $channel = $this->createChannel($channelParam);
        if (!$channel)
            throw new Exception();
        return $channel;
    }
}