<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Domain\SelfOrder\Actions\CreateSelfOrderAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class CreateOrderController extends BaseController
{
    public function __invoke(Request $request, CreateSelfOrderAction $action)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'table_no' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'nullable|integer|exists:pos_menu_items,id',
            'items.*.combo_id' => 'nullable|integer|exists:pos_combos,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:200',
        ]);

        $order = $action->execute($validated);

        return $this->success($order, 'Order berhasil dibuat', 201);
    }
}
