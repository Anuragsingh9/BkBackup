<?php
/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 5/31/2019
 * Time: 4:55 PM
 */

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WorkshopScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('display', 1)->where(function ($q) {
            $q->OrWhereNull('is_qualification_workshop')->OrWhere('is_qualification_workshop', 0);
        });
    }
}