<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Infra\POS\Models\Category;
use Infra\Shared\Controllers\BaseController;

class ListCategoriesController extends BaseController
{
    public function __invoke()
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount('activeMenuItems as items_count')
            ->get();

        return $this->resolveForSuccessResponseWith('Success', $categories);
    }
}
