<?php

namespace Domain\DataSso\Actions;

use Infra\DataSso\Models\DataSso;
use Infra\Shared\Foundations\Action;

class UpdatePhotoAction extends Action
{
    protected $sso;

    public function execute(DataSso $sso, $url)
    {
        $this->sso = $sso;
        $this->sso->url_icon = $url;
        $this->sso->save();

        return $this->sso;
    }
}
