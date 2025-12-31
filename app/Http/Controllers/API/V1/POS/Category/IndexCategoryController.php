<?php

namespace App\Http\Controllers\API\V1\POS\Category;

use Domain\POS\Actions\Category\IndexCategoryAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class IndexCategoryController extends BaseController
{
    public function __invoke(Request $request, IndexCategoryAction $action)
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);

        $categories = $action->execute($filters);

        return $this->resolveForSuccessResponseWithPage('Success', $categories);
    }
}
