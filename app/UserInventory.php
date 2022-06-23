<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class UserInventory extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function employee_info(){
        return $this->belongsTo('App\Employee','employee_id','id')->select('id','id_number','first_name','last_name','cluster','new_cluster');
    }

    public function inventory_info(){
        return $this->belongsTo('App\Inventory','inventory_id','id')->select('id','model','serial_number','type','location','processor','os_name_and_version');
    }

    public function letter_of_undertaking(){
        return $this->hasOne('App\LetterOfUndertaking');
    }

    public function is_return_for_checking(){
        return $this->hasOne('App\UserReturnRequestItem','user_inventory_id','id')->where('status','For Checking');
    }

}
