<?php

namespace Modules\Messenger\Entities;

use App\RegularDocument;
use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Messenger\Traits\UseUuid;

class Channel extends TenancyModel {
    use SoftDeletes, UseUuid; // the use uuid is trait which makes the uuid column to treat as primary key and  also it generates the new uuid on time bases
    protected $table = 'im_channels';
    protected $primaryKey = 'uuid'; // explicitly defining primary key as no longer using id here
    
    protected $casts = [
        'channel_fields' => 'array',    // as discussed if we need to add additional fields then we can put extra data here
    ];
    
    protected $fillable = [
        'channel_name',
        'channel_type', // 1-Workshop, 2-Channel, 3-Personal
        'is_private',
        'owner_id',
        'channel_fields',
    ];
    
    /**
     * @return HasOne
     */
    public function owner() {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
    
    /**
     * @return BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class, 'im_channel_users', 'channel_uuid', 'user_id')
            ->whereNull('deleted_at');
    }
    
    /**
     * @return HasMany
     */
    public function messages() {
        return $this->hasMany(Message::class, 'channel_uuid', 'uuid')->withCount('likes')
            ->withCount('isStared')
            ->withCount('replies')
            ->with('attachments');
    }
    
    /**
     * @return HasMany
     */
    public function channelVisited() {
        return $this->hasMany(UserChannelVisit::class, 'channel_uuid', 'uuid');
    }
    
    /**
     * @return HasMany
     * Join Message and user last visited to message ->channel_id then check if message created is after last visited then treat it as unread message
     * withCount will not work
     */
    public function unreadMessages() {
        return $this->hasMany(Message::class, 'channel_uuid', 'uuid')
            ->select('im_messages.id as message_id', 'im_messages.message_text',
                'im_messages.channel_uuid', 'usu.last_visited_at',
                'im_messages.created_at as message_sent_on')
            ->join('im_user_channel_visits as usu', 'usu.channel_uuid', '=', 'im_messages.channel_uuid')
            ->where(DB::raw('im_messages.created_at'), '>', DB::raw('usu.last_visited_at'))
            ->where('usu.user_id', Auth::user()->id)
            ->where('sender_id', '!=', Auth::user()->id);
    }
    
    public function topic() {
        return $this->belongsTo(WorkshopTopic::class, 'uuid', 'channel_uuid');
    }
    
    public function secondUserOfPersonalChat() {
        $id = Auth::user()->id;
        return $this->hasOne(UserChannelUserRelation::class, 'channel_uuid', 'uuid')
            ->with('user1')
            ->select(DB::raw(" CASE WHEN user1_id=$id THEN user2_id WHEN user2_id=$id THEN user1_id ELSE user1_id END as user1_id"), 'channel_uuid');
    }
    
    public function files() {
        return $this->belongsToMany(MessageMedia::class, 'im_messages', 'channel_uuid', 'id', 'uuid', 'attachmentable_id')
            ->where("attachmentable_type", Message::class);
    }
    
    public function workshopDocs() {
        return $this->belongsToMany(RegularDocument::class, 'im_topics', 'channel_uuid', 'workshop_id', 'uuid', '');
    }
}
