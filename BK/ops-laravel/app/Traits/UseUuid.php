<?php

namespace App\Traits;


use Ramsey\Uuid\Uuid;

/**
 * Trait UseUuid
 * @package App\Traits
 *
 * TODO [DEVELOPER] Please add protected $primary = {column name which is uuid}
 * Require uuid column to be primary key
 * if already have id as primary key and still wanna have another column as uuid
 * then use HaveUuidColumn with respective requirements
 */
trait UseUuid {
    
    protected static function bootUseUuid() {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->primaryKey} = Uuid::uuid1()->toString();
            }
        });
    }
    
    public function getIncrementing() {
        return FALSE;
    }
    
    public function getKeyType() {
        return 'string';
    }
}