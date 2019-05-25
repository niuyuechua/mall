<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'role';
    public $timestamps = false;
    protected $primaryKey='role_id';
}
