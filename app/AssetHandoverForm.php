<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AssetHandoverForm extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;
    
    public function employee_info(){
        return $this->belongsTo('App\Employee','employee_id','id')->select('id','id_number','first_name','last_name','cluster','new_cluster');
    }

}
