<?php

namespace Modules\KctUser\Entities;

use App\Models\User;
use App\Traits\UseUuid;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\EventDummyUser;
use Modules\KctAdmin\Entities\Space;

/**
 * @property string uuid
 * @property string aws_chime_uuid
 * @property array aws_chime_meta
 * @property string space_uuid
 * @property string end_at
 */
class Conversation extends TenancyModel {
    use UseUuid;

    protected $table = 'kct_conversations';
    protected $primaryKey = 'uuid';
    protected $casts = ['aws_chime_meta' => 'array'];
    protected $fillable = ['uuid', 'aws_chime_uuid', 'aws_chime_meta', 'space_uuid', 'end_at', 'is_private','private_by'];

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('conversation_end', function (Builder $builder) {
            $builder->whereNull('end_at');
        });
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class,
            'kct_conversation_users',
            'conversation_uuid',
            'user_id',
            'uuid'
        )->whereNull('leave_at')
            ->with([
                'unions',
                'company'
            ]);
    }

    public function userRelation(): HasMany {
        return $this->hasMany(ConversationUser::class, 'conversation_uuid', 'uuid');
    }

    public function currentUser(): HasOne {
        return $this->hasOne(ConversationUser::class, 'conversation_uuid', 'uuid')->where('user_id', Auth::user()->id);
    }

    public function dummyRelation(): HasMany {
        return $this->hasMany(EventDummyUser::class, 'current_conv_uuid', 'uuid');
    }

    public function space(): BelongsTo {
        return $this->belongsTo(Space::class, 'space_uuid', 'space_uuid');
    }
}
