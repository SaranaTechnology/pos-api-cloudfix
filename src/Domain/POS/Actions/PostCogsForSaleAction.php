<?php

namespace Domain\POS\Actions;

use Illuminate\Support\Arr;
use Infra\Accounting\Models\JournalEntry;
use Infra\Accounting\Models\JournalLine;
use Infra\POS\Models\Sale;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PostCogsForSaleAction extends Action
{
    public function execute(Sale $sale, array $data)
    {
        $cogsCoa = env('POS_COGS_ACCOUNT_ID');
        $invCoa = env('POS_INVENTORY_ACCOUNT_ID');
        if (! $cogsCoa || ! $invCoa) {
            throw new BadRequestException('POS_COGS_ACCOUNT_ID dan POS_INVENTORY_ACCOUNT_ID harus diset di ENV');
        }

        // prevent double posting by reference
        $ref = 'POS-COGS-'.($sale->invoice_no ?: $sale->id);
        $exists = JournalEntry::where('source', 'pos_cogs')->where('reference', $ref)->first();
        if ($exists) {
            throw new BadRequestException('COGS untuk transaksi ini sudah diposting');
        }

        // costs mapping: [{product_id, cost}] or { product_id: cost }
        $itemsCost = Arr::get($data, 'items_cost', []);
        $map = [];
        if (is_array($itemsCost)) {
            foreach ($itemsCost as $row) {
                if (is_array($row) && Arr::has($row, ['product_id','cost'])) {
                    $map[$row['product_id']] = (int) $row['cost'];
                } elseif (is_array($itemsCost) && !Arr::isAssoc($itemsCost)) {
                    // skip
                } elseif (is_array($itemsCost) && Arr::isAssoc($itemsCost)) {
                    // keyed map form
                    $map = $itemsCost;
                    break;
                }
            }
        }

        $totalCost = 0;
        foreach ($sale->items as $item) {
            $costPerUnit = (int) ($map[$item->product_id] ?? 0);
            $totalCost += (int) ($item->qty * $costPerUnit);
        }
        if ($totalCost <= 0) {
            throw new BadRequestException('Total COGS tidak valid. Sertakan items_cost dengan cost per unit.');
        }

        $entry = JournalEntry::create([
            'date' => $sale->sold_at,
            'reference' => $ref,
            'memo' => 'POS COGS '.($sale->invoice_no ?: $sale->id),
            'source' => 'pos_cogs',
            'status' => 'Posted',
        ]);
        // Dr COGS
        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $cogsCoa,
            'debit' => $totalCost,
            'credit' => 0,
            'memo' => 'COGS',
        ]);
        // Cr Inventory
        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $invCoa,
            'debit' => 0,
            'credit' => $totalCost,
            'memo' => 'Inventory',
        ]);

        return $entry->load('lines');
    }
}

