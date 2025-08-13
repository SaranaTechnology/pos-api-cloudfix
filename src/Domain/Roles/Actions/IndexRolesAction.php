<?php

namespace Domain\Roles\Actions;

use Illuminate\Support\Arr;
use Infra\Roles\Models\Roles;
use Infra\Shared\Foundations\Action;

class IndexRolesAction extends Action
{
    protected $roles;

    public function execute($query)
    {
        $this->roles = Roles::query();
        if (Arr::exists($query, 'with')) {
            $this->handleWith($query['with']);
        }
        if (Arr::exists($query, 'nama')) {
            $this->handleNama($query['nama']);
        }
        if (Arr::exists($query, 'total_only') && $query['total_only'] == 'true') {
            $data['total'] = $this->roles->count();

            return $data;
        }
        $page_size = Arr::get($query, 'page_size', 10);
        $page = Arr::get($query, 'page', 1);
        if (Arr::exists($query, 'select2') && $query['select2'] == true) {
            return $this->handleSelect();
        }
        $this->handlePaginate($page_size);

        return $this->roles;
    }

    protected function handleNama($name)
    {
        $this->roles = $this->roles->where('nama', 'like', '%'.$name.'%');
    }

    protected function handleWith($relationship)
    {
        $with = explode(',', $relationship);
        $this->roles = $this->roles->with($with);
    }

    protected function handleSelect()
    {
        return $this->roles->get();
    }

    protected function handlePaginate($page_size = 10)
    {
        $this->roles = $this->roles->paginate($page_size);
    }
}
