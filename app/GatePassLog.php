<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class GatePassLog extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];
    
    use SoftDeletes;
    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;
    
    

}
