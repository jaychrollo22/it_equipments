<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = 'mysql_hr';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function borrowed_items(){
        return $this->hasMany('App\UserInventory','employee_id','id')->where('status','Borrowed')->orderBy('borrow_date','DESC');
    }

    public function companies()
    {
        return $this->belongsToMany('App\Company')->withTimestamps();
    }

    public function departments()
    {
        return $this->belongsToMany('App\Department')->withTimestamps();
    }
    public function locations()
    {
        return $this->belongsToMany('App\Location')->withTimestamps();
    }
    
}
