<?php
    
    namespace App;
    
    use Auth;
//use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class Message extends TenancyModel
    {
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        public $fillable = ['id', 'workshop_id', 'messages_text', 'user_id', 'category_id', 'to_id', 'visitor_ip', 'type'];
        
        public function user()
        {
            return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'fname', 'lname', 'avatar', 'email', 'phone']);
        }
        
        public function messageReplies()
        {
            return $this->hasMany('App\MessageReply', 'message_id', 'id')->orderBy('id', 'desc');
        }
        
        public function messageLikes()
        {
            return $this->hasOne('App\MessageLike', 'message_id', 'id')->where('user_id', Auth::user()->id)->whereNull('message_reply_id')->select(['id', 'message_id', 'message_reply_id', 'user_id', 'status']);;
        }
        
        public function replyLikes()
        {
            return $this->hasMany('App\MessageLike', 'message_id', 'id')->where('user_id', Auth::user()->id)->whereNotNull('message_reply_id')->select(['id', 'message_id', 'message_reply_id', 'user_id', 'status']);;
        }
        
        public function countLikesMessage()
        {
            return $this->hasMany('App\MessageLike', 'message_id', 'id')->where('status', 1)->whereNull('message_reply_id')->select(['id', 'message_id', 'message_reply_id', 'user_id', 'status']);;
        }
        
        public function countLikesReply()
        {
            return $this->hasMany('App\MessageLike', 'message_id', 'id')->where('status', 1)->whereNotNull('message_reply_id')->select(['id', 'message_id', 'message_reply_id', 'user_id', 'status']);;
        }
        
        public function category()
        {
            return $this->hasOne('App\MessageCategory', 'id', 'user_id')->select(['id', 'category_name']);
        }
        public function channel() {
            return $this->hasOne('Modules\Messenger\Entities\Channel', 'id', 'channel_id');
        }
    }
