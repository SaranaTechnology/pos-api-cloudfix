<?php

namespace Domain\POS\Actions\Combo;

use Infra\POS\Models\Combo;
use Infra\Shared\Foundations\Action;

class DeleteComboAction extends Action
{
    public function execute(Combo $combo): void
    {
        $combo->delete();
    }
}
