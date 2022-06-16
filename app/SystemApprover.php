<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemApprover extends Model
{
    protected $connection = 'mysql';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User','user_id','id')->select('id','name','email');
    }
}
