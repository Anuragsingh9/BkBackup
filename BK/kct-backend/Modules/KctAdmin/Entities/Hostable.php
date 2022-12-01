<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer host_id
 * @property string hostable_uuid
 * @property string hostable_type
 *
 * Class KctHostables
 * @package Modules\KctAdmin\Entities
 */
class Hostable extends TenantModel {
    use HasFactory;

    protected $table = 'event_hostables';

    protected $fillable = [
        'host_id',
        'hostable_uuid',
        'hostable_type',
    ];

    public function spaces(): HasOne {
        return $this->hasOne(Space::class,'space_uuid','hostable_uuid');
    }

}
