<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkAccAdmin extends Model {
    protected $table = 'bulk_acc_admins';
    protected $fillable = ['account_id', 'super_admin_id'];
    
    public function accountCreatedToday() {
        return $this->hasOne(Hostname::class, 'website_id', 'account_id');
    }
}
