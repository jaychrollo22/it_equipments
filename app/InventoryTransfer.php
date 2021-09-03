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
}
