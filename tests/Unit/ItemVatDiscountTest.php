<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SparePart;
use App\Models\items;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemVatDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_total_cost_uses_spare_part_vat_and_discount()
    {
        $spare = SparePart::create([
            'part_number' => 'SP-001',
            'name' => 'Test Spare',
            'amount_per_unit' => 100,
            'vat' => 10, // 10%
            'discount' => 5 // 5
        ]);

        $item = items::create([
            'quotation_id' => 1,
            'item_no' => 'SP-001',
            'description' => 'Test Item',
            'quantity' => 2,
            'unit' => 'pcs',
            'unit_price' => 100,
            'vat_percentage' => null,
            'discount' => null
        ]);

        $item->vat = $item->calculateVAT();
        $item->total_cost = $item->calculateTotalCost();
        $item->save();

        // subtotal = 2 * 100 = 200
        // discount = 5
        // vat = 10% of 100 = 10 per unit, so 20 for 2 units
        // total = 200 - 5 + 20 = 215
        $this->assertEquals(20, $item->vat);
        $this->assertEquals(215, $item->total_cost);
    }
}
