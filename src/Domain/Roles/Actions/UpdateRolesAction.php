<?php

namespace Domain\Roles\Actions;

use Illuminate\Support\Arr;
use Infra\Roles\Models\Permissions\RolesPermission;
use Infra\Roles\Models\Roles;
use Infra\Shared\Foundations\Action;

class UpdateRolesAction extends Action
{
    public function execute($data, Roles $role)
    {
        if (Arr::exists($data, 'permissions')) {
            $permission = $data['permissions'];
            $this->handlePermission($permission, $role);
            $data = Arr::except($data, 'permission');
        }
        $role->update($data);

        return $role;
    }

    protected function handlePermission($permission, $role)
    {

        $role->permission()->sync($permission);
    }
}
