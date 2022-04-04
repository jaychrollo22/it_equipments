<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class InventoriesForDisposal extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use SoftDeletes;
    use Auditable;

    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function requested_by_info(){
        return $this->belongsTo('App\User','requested_by','id')->select('id','name');
    }
    public function approved_by_it_head_info(){
        return $this->belongsTo('App\User','approved_by_it_head','id')->select('id','name');
    }
    public function approved_by_finance_info(){
        return $this->belongsTo('App\User','approved_by_finance','id')->select('id','name');
    }
    public function items(){
        return $this->hasMany('App\InventoriesForDisposalItem','inventory_for_disposal_id','id');
    }
}
