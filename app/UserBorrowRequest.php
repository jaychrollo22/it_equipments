<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use DateTimeInterface;

class UserBorrowRequest extends Model implements AuditableContract
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

    public function approved_by_it_head_info(){
        return $this->belongsTo('App\User','approved_by_it_head','id')->select('id','email','name');
    }

    public function acknowledge_by_it_support_info(){
        return $this->belongsTo('App\User','acknowledge_by_it_support','id')->select('id','email','name');
    }

    public function borrow_request_items(){
        return $this->hasMany('App\UserBorrowRequestItem');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
