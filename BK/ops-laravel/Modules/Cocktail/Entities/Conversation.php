<?php

namespace Modules\Cocktail\Entities;

use App\Traits\UseUuid;
use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends TenancyModel {
    use UseUuid;
    protected $table = 'event_conversation';
    protected $primaryKey = 'uuid';
    protected $casts = ['aws_chime_meta' => 'array'];
    protected $fillable = ['space_uuid', 'aws_chime_uuid', 'aws_chime_meta', 'end_at'];
    
    protected static function boot() {
        parent::boot();
        
        static::addGlobalScope('conversation_end', function (Builder $builder) {
            $builder->whereNull('end_at');
        });
    }
    
    public function users() {
        return $this->belongsToMany(User::class,
            'event_conversation_user',
            'conversation_uuid',
            'user_id',
            'uuid'
        )->whereNull('leave_at')
            ->with([
                'unions',
                'companies',
                'instances',
                'presses',
                'facebookUrl',
                'twitterUrl',
                'instagramUrl',
                'linkedinUrl',
                'eventUsedTags',
                'tagsRelationForPP'
            ]);
    }
    
    public function userRelation() {
        return $this->hasMany(ConversationUser::class, 'conversation_uuid', 'uuid');
    }
    
    public function currentUser() {
        return $this->hasOne(ConversationUser::class, 'conversation_uuid', 'uuid')->where('user_id', Auth::user()->id);
    }
    
    public function dummyRelation() {
        return $this->hasMany(EventDummyUser::class,'current_conv_uuid', 'uuid');
    }
    
    public function space() {
        return $this->belongsTo(EventSpace::class, 'space_uuid', 'space_uuid');
    }
}
