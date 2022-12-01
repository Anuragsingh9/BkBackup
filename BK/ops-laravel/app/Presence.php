<?php
    
    namespace App;
    //use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class Presence extends TenancyModel
    {
        protected $fillable = ['workshop_id', 'user_id', 'meeting_id', 'register_status', 'presence_status', 'with_meal_status', 'video_presence_status'];
        
        public function user()
        {
            return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'fname', 'lname', 'email']);
        }
        
        public function presence_user()
        {
            return $this->hasOne('App\User', 'id', 'user_id')->with(['union' => function ($q) {
                $q->select('id', 'union_code');
            }, 'entity'                                                      => function ($q) {
                $q->select('entities.id', 'entities.long_name');
            }])->select(['id', 'fname', 'lname', 'society', 'union_id'])->orderBy('lname', 'ASC');
        }
        
        public function meeting()
        {
            return $this->belongsTo('App\Meeting', 'meeting_id', 'id')->select(['id', 'name', 'meeting_type']);
        }
    }
