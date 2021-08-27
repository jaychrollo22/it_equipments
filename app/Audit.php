<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Audit extends Model
{
    protected $connection = "mysql";

    public function user()
    {
       return $this->hasOne('App\User', 'id', 'user_id')->select('id','name','email');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
