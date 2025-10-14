<?php

namespace Domain\POS\Actions\Menu;

use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;

class DeleteMenuItemAction extends Action
{
    public function execute(MenuItem $menuItem): void
    {
        $menuItem->delete();
    }
}
