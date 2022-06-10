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

    public function requested_by_info(){
        return $this->belongsTo('App\User','requested_by','id')->select('id','name','email');
    }
    public function received_by_info(){
        return $this->belongsTo('App\User','received_by','id')->select('id','name','email');
    }

    public function approved_by_it_head_info(){
        return $this->belongsTo('App\User','approved_by_it_head','id')->select('id','name','email');
    }
    public function approved_by_finance_info(){
        return $this->belongsTo('App\User','approved_by_finance','id')->select('id','name','email');
    }
    public function department_info(){
        return $this->belongsTo('App\Department','transfer_department','id')->select('id','name');
    }
}
