<?php

namespace Infra\Staff\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Staff extends Authenticatable
{
    use SoftDeletes, HasApiTokens, Notifiable;

    protected $table = 'staff';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'password_token_reset',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
