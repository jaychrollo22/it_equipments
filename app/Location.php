<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $connection = 'mysql_hr';

    public function address()
    {
        return $this->belongsToMany('App\Address')->withTimestamps();
    }
}
