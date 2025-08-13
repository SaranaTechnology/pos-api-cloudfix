<?php

namespace Infra\Roles\Models;

use Infra\Roles\Models\Permissions\Permissions;
use Infra\Shared\Models\BaseModel;
use Infra\User\Models\User;

class Roles extends BaseModel
{
    public function user()
    {
        return $this->belongsToMany(related: User::class, table: 'role_users',foreignPivotKey:'role_id',relatedPivotKey:"user_id");
    }

    public function permission()
    {
        return $this->belongsToMany(related: Permissions::class, table: 'role_permissions', relatedPivotKey: 'permission_id',foreignPivotKey:'role_id');
    }
    }
