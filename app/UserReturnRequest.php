<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use DateTimeInterface;


class UserReturnRequest extends Model implements AuditableContract
{
    protected $connection = 'mysql';
    protected $guarded = [];

    use SoftDeletes;
    
    use Auditable;
    protected $auditIncluded = [];
    protected $auditTimestamps = true;

    public function employee(){
        return $this->belongsTo('App\Employee')->select('id','user_id','first_name','last_name','position');
    }
    public function user(){
        return $this->belongsTo('App\User')->select('id','email','name');
    }
    public function return_request_items(){
        return $this->hasMany('App\UserReturnRequestItem');
    }
}
