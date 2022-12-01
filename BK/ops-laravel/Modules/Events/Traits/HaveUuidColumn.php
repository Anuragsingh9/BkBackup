<?php

namespace Modules\Events\Traits;


use Ramsey\Uuid\Uuid;

/**
 * Trait HaveUuidColumn
 * @package App\Traits
 *
 * TODO [DEVELOPER] Please add protected $primary = {column name which is uuid}
 */
trait HaveUuidColumn {
    
    protected static function bootHaveUuidColumn() {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                foreach ($model->uuidColumns as $column) {
                    $model->$column = Uuid::uuid1()->toString();
                }
            }
        });
    }
}