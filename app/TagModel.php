<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagModel extends Model
{
    protected $table = 'tag';
    public $timestamps = false;
    protected $primaryKey='t_id';
}
