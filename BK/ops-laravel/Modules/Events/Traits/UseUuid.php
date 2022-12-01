<?php

namespace Modules\Events\Traits;


use Ramsey\Uuid\Uuid;

/**
 * Trait UseUuid
 * @package App\Traits
 *
 * TODO [DEVELOPER] Please add protected $primary = {column name which is uuid}
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