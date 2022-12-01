<?php

namespace App\Traits;


use Ramsey\Uuid\Uuid;

/**
 * Trait HaveUuidColumn
 * @package App\Traits
 *
 * TODO [DEVELOPER] Please add protected $primary = {column name which is uuid}
 * This trait is for when you already have the id column as primary
 * and another non prime uuid column is there and to be filled automatically
 * NOTE: making uuid column unique would be better
 * but first fill all the old rows uuid column with script with uuid if making unique
 */
trait HaveUuidColumn {
    
    protected static function bootHaveUuidColumn() {
        static::creating(function ($model) {
            foreach ($model->uuidColumns as $column) {
                $model->$column = Uuid::uuid1()->toString();
            }
        });
    }
}