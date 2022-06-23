<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use DateTimeInterface;

class UserReturnRequestItem extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use SoftDeletes;
    
    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function user_inventory(){
        return $this->belongsTo('App\UserInventory','user_inventory_id','id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
