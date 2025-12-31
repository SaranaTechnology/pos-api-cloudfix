<?php

namespace App\Http\Controllers\API\V1\POS\Category;

use Domain\POS\Actions\Category\DeleteCategoryAction;
use Infra\POS\Models\Category;
use Infra\Shared\Controllers\BaseController;

class DeleteCategoryController extends BaseController
{
    public function __invoke(Category $category, DeleteCategoryAction $action)
    {
        $action->execute($category);

        return $this->resolveForSuccessResponseWith('Category deleted successfully');
    }
}
