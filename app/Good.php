<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    public $timestamps = false;
    protected $table = 'goods';
    protected $primaryKey='goods_id';
}
