<?php

namespace Domain\User\Actions\CRUD;

use Domain\Shared\Actions\CheckRolesAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Infra\Shared\Foundations\Action;
use Infra\User\Models\User;
use Infra\User\Models\UserRoles\UserRoles;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateUserAction extends Action
{
    public function execute($data)
    {
        if (! CheckRolesAction::resolve()->execute()) {
            throw new BadRequestException('User can`t use This access');
        }
        $array = ['username', 'password'];
        foreach ($array as $key) {
            if (! Arr::exists($data, $key)) {
                throw new BadRequestException($key.' is required');
            }
        }
        if (Arr::exists($data, 'roles')) {
            $roles = $data['roles'];
            $data = Arr::except($data, 'roles');
        }
        $data['username'] = Str::lower($data['username']);
        $check = User::where('username', $data['username'])->first();
        if ($check) {
            throw new BadRequestException('uusername has been used please find other username');
        }
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        if (! empty($roles)) {
            $this->handleAddRoles($roles, $user);
        }

        return $user;
    }

    public function handleAddRoles($roles, User $user)
    {
        foreach ($roles as $role) {
            UserRoles::create([
                'role_id' => $role,
                'user_id' => $user->id,
            ]);
        }
    }
}
