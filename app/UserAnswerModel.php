<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAnswerModel extends Model
{
    protected $table = 'answer_user';
    public $timestamps = false;
    protected $primaryKey='a_id';
}
