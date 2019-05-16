<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateModel extends Model
{
    protected $table = 'cate';
    public $timestamps = false;
    protected $primaryKey='cate_id';
}
