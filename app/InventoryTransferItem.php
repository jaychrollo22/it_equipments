<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class InventoryTransferItem extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use SoftDeletes;
    
    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function inventory_info(){
        return $this->belongsTo('App\Inventory','inventory_id','id')->select('id','model','serial_number','type','location','company');
    }
}
