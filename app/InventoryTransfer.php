<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class InventoryTransfer extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function inventory_transfer_items(){
        return $this->hasMany('App\InventoryTransferItem','inventory_transfer_id','id');
    }

    public function requested_by(){
        return $this->belongsTo('App\User','requested_by','id');
    }
}
