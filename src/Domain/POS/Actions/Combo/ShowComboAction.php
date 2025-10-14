<?php

namespace Domain\POS\Actions\Combo;

use Infra\POS\Models\Combo;
use Infra\Shared\Foundations\Action;

class ShowComboAction extends Action
{
    public function execute(Combo $combo): Combo
    {
        return $combo->load('items');
    }
}
