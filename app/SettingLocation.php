<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class SettingLocation extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    
    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;
    
    protected $fillable = [
        'name',
        'address',
        'color'
    ]; 
}
