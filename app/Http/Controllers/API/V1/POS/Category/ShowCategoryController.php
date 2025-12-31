<?php

namespace App\Http\Controllers\API\V1\POS\Category;

use Domain\POS\Actions\Category\ShowCategoryAction;
use Infra\Shared\Controllers\BaseController;

class ShowCategoryController extends BaseController
{
    public function __invoke(int $category, ShowCategoryAction $action)
    {
        $category = $action->execute($category);

        return $this->resolveForSuccessResponseWith('Success', $category);
    }
}
