<?php

namespace Modules\SuperAdmin\Entities;

use Hyn\Tenancy\Models\Hostname;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer hostname_id
 * @property string fname
 * @property string lname
 * @property string email
 * @property string password
 * @property string name_org
 * @property string acronym
 *
 * @property ?Hostname hostname
 */
class Organisation extends Model {

    protected $fillable = [
        'hostname_id',
        'fname',
        'lname',
        'email',
        'password',
        'name_org',
        'acronym',
    ];

    public function hostname(): HasOne {
        return $this->hasOne(Hostname::class, 'id', 'hostname_id');
    }

}
