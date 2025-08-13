<?php

namespace Infra\Roles\Enums;

use Infra\Shared\Enums\Traits\hasCaseResolve;

enum AppsList: string
{
    use hasCaseResolve;

    case core = 'core';
    case hrd = 'hrd';
    case warehouse = 'warehouse';
}
