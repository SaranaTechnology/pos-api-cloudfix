<?php

namespace Domain\Roles\Actions;

use Infra\Roles\Models\Roles;
use Infra\Shared\Foundations\Action;

class DeleteRolesAction extends Action
{
    public function execute(Roles $role)
    {
        $role->delete();

        return true;
    }
}
