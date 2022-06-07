<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Inventory extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function is_borrowed(){
        return $this->hasOne('App\UserInventory','inventory_id','id')->where('status','Borrowed');
    }

    public function is_transfer(){
        return $this->hasOne('App\InventoryTransferItem','inventory_id','id')->where('status','Pending');
    }

    public function setting_location(){
        return $this->hasOne('App\SettingLocation','name','location');
    }

    public function disposed_by_info(){
        return $this->belongsTo('App\User','disposed_by','id')->select('id','name');
    }
}
