<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class used for organiser tag
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class OrganiserTagUser
 *
 * @package Modules\KctUser\Entities
 */
class OrganiserTagUser extends TenancyModel {
    protected $fillable = ['user_id', 'tag_id'];
    protected $table = "organiser_tag_users";

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used creating tag relation with organiser
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return HasOne
     */
    public function tag(): HasOne {
        return $this->hasOne(OrganiserTag::class, 'id', 'tag_id');
    }
}
