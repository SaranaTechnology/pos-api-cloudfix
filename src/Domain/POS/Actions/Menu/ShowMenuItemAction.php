<?php

namespace Domain\POS\Actions\Menu;

use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;

class ShowMenuItemAction extends Action
{
    public function execute(MenuItem $menuItem): MenuItem
    {
        return $menuItem->load('combos');
    }
}
