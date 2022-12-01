<?php
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 5/14/2020
     * Time: 4:55 PM
     */
    
    namespace App\Scopes;
    
    use Illuminate\Database\Eloquent\Scope;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Facades\Auth;
    
    class MeetingScope implements Scope
    {
        /**
         * Apply the scope to a given Eloquent query builder.
         *
         * @param \Illuminate\Database\Eloquent\Builder $builder
         * @param \Illuminate\Database\Eloquent\Model $model
         * @return void
         */
        public function apply(Builder $builder, Model $model)
        {
            if(Auth::check()){
                if (!in_array(Auth::user()->role,['M1','M0'])) {
                    $builder->where('status', 1);
                }
            }
            
            
        }
    }