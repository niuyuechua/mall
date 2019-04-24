<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsModel extends Model
{
    protected $table = 'goods';
    public $timestamps = false;
    protected $primaryKey='goods_id';
}
