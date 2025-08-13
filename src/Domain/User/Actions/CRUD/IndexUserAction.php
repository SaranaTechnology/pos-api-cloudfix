<?php

namespace Domain\User\Actions\CRUD;

use Domain\Shared\Actions\CheckRolesAction;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Infra\Shared\Foundations\Action;
use Infra\User\Models\User;

class IndexUserAction extends Action
{
    protected $user;

    public function execute($query)
    {
        if (! CheckRolesAction::resolve()->execute()) {
            throw new Exception('User can`t use This access');
        }
        $this->user = User::query();
        if (Arr::exists($query, 'total_only') && $query['total_only'] === 'true') {
            $data['total'] = $this->user->count();

            return $data;
        }
        $this->user = $this->user->where('id', '!=', Auth::user()->id)->where('id', '!=', 1);

        if (Arr::exists($query, 'with')) {
            $this->handleWith($query['with']);
        }
        $page_size = Arr::get($query, 'page_size', 10);
        $page = Arr::get($query, 'page', 1);
        $this->handlePaginate($page_size);

        return $this->user;
    }

    protected function handleWith($relationship)
    {
        $with = explode(',', $relationship);
        $this->user = $this->user->with($with);
    }

    protected function handlePaginate($page_size)
    {
        $this->user = $this->user->paginate($page_size);
    }
}
