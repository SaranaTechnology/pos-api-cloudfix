<?php

namespace Infra\User\Models;

use Infra\Roles\Models\Roles;
use Infra\Shared\Models\AuthModel;

class User extends AuthModel
{
    public function roles()
    {
        return $this->belongsToMany(related: Roles::class, table: 'role_users',foreignPivotKey:'user_id',relatedPivotKey:"role_id");
    }
}
