<?php

namespace Domain\Roles\Actions;

use Domain\Shared\Actions\CheckRolesAction;
use Exception;
use Illuminate\Support\Arr;
use Infra\Roles\Models\Permissions\RolesPermission;
use Infra\Roles\Models\Roles;
use Infra\Shared\Foundations\Action;

class CreateRolesAction extends Action
{
    public function execute($data)
    {
        if (! CheckRolesAction::resolve()->execute('add-role')) {
            throw new Exception('User can`t use This access');
        }
        if (Arr::exists($data, 'permissions')) {
            $permission = $data['permissions'];
            $data = Arr::except($data, 'permission');
        }
        $roles = Roles::create($data);
        if (! empty($permission)) {
            $this->handleRolesPermission($roles, $permission);
        }

        return $roles;

    }

    protected function handleRolesPermission($roles, $permission)
    {
        foreach ($permission as $item) {
            RolesPermission::create([
                'role_id' => $roles->id,
                'permission_id' => $item,
            ]);
        }

    }
}
