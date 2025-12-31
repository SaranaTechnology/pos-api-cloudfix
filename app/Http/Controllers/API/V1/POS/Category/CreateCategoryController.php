<?php

namespace App\Http\Controllers\API\V1\POS\Category;

use Domain\POS\Actions\Category\CreateCategoryAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class CreateCategoryController extends BaseController
{
    public function __invoke(Request $request, CreateCategoryAction $action)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:pos_categories,slug',
            'description' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category = $action->execute($validated);

        return $this->resolveForSuccessResponseWith('Category created successfully', $category, HttpStatus::Created);
    }
}
