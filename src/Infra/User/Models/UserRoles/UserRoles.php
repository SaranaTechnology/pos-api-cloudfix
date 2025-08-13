<?php

namespace Infra\User\Models\UserRoles;

use Infra\Shared\Models\PivotModel;

class UserRoles extends PivotModel
{
    protected $table = 'role_users';
}
