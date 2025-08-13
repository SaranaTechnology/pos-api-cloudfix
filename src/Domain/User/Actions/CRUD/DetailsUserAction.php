<?php

namespace Domain\User\Actions\CRUD;

use Domain\Shared\Actions\CheckRolesAction;
use Exception;
use Illuminate\Support\Arr;
use Infra\Shared\Foundations\Action;
use Infra\User\Models\User;

class DetailsUserAction extends Action
{
    protected $user;

    public function execute($query, User $user)
    {
        $this->user = $user;
        if (! CheckRolesAction::resolve()->execute()) {
            throw new Exception('User can`t use This access');
        }
        if (Arr::exists($query, 'with')) {
            if (Arr::exists($query, 'only')) {
                $this->handleOnly($query['with'], $query['only']);
            } else {
                $this->handleWith($query['with']);
            }
        }

        return $this->user;
    }

    protected function handleWith($relationship)
    {
        $with = explode(',', $relationship);
        $this->user = $this->user->load($with);
    }

    protected function handleOnly($with, $only)
    {
        $with = explode(',', $with);
        $only = explode(',', $only);
        $this->user = $this->user->load($with);
        foreach ($only as $col) {
            $table = explode('.', $col);
            $this->user->{$table[0]}->transform(function ($item) use ($table) {
                return $item->{$table[1]};
            });
        }
    }
}
