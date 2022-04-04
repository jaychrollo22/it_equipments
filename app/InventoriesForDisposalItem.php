<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class InventoriesForDisposalItem extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use SoftDeletes;
    use Auditable;

    protected $auditIncluded = [];
    protected $auditTimestamps = true;   

    public function inventory(){
        return $this->belongsTo('App\Inventory','inventory_id','id')->select('id','epc','type','serial_number','model');
    }
}
