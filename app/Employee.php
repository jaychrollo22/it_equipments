<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = 'mysql_hr';

    public function user()
    {
        return $this->belongsTo('App\User')->select('id','name','email');
    }

    public function borrowed_items(){
        return $this->hasMany('App\UserInventory','employee_id','id')->where('status','Borrowed')->orderBy('borrow_date','DESC');
    }
    
}
