<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'name',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];
}
