<?php

namespace Modules\Messenger\Entities;

use App\Workshop;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopTopic extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_topics';
    protected $fillable = [
        'topic_name',
        'workshop_id',
        'channel_uuid',
    ];
    
    /**
     * @return HasOne
     */
    public function workshop() {
        return $this->hasOne(Workshop::class, 'id', 'workshop_id');
    }
    
    /**
     * @return HasOne
     */
    public function channel() {
        return $this->hasOne(Channel::class, 'uuid', 'channel_uuid');
    }
}
