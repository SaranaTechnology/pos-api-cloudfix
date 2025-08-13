<?php

namespace Domain\DataSso\Actions;

use Illuminate\Support\Arr;
use Infra\DataSso\Models\DataSso;
use Infra\Shared\Foundations\Action;

class IndexSsoAction extends Action
{
    protected $sso;

    public function execute($query)
    {
        $this->sso = DataSso::query();
        if (Arr::exists($query, 'page_size') && Arr::exists($query, 'page')) {
            $this->handlePaginate($query['page_size']);
        } else {
            $this->handlePaginate();
        }

        return $this->sso;
    }

    protected function handlePaginate($page_size = 10)
    {
        $this->sso = $this->sso->paginate($page_size);
    }
}
