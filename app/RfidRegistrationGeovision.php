<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RfidRegistrationGeovision extends Model
{
    protected $connection = "sqlsrv";
    protected $table = "AccessLog";

    protected $guarded = [];
}
