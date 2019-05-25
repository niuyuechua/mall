<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionModel extends Model
{
    protected $table = 'permission';
    public $timestamps = false;
    protected $primaryKey='pms_id';
}
