<?php
namespace Modules\Crm\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class AssistanceTeam extends TenancyModel
{
    protected $table = "assistance_team";
    protected $fillable = ['assistance_type_id','member_id'];
    use SoftDeletes;

    public function assistance_type(){
        return $this->hasOne('Modules\Crm\Entities\Assistance','id','assistance_type_id');
    }

    public function user(){
        return $this->hasOne('App\User','id','member_id');
    }
}
