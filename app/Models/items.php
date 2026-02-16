<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class items extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_no',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount',
        'vat',
        'vat_percentage',
        'total_cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    /**
     * Calculate VAT based on unit price and VAT percentage
     */
    public function calculateVAT()
    {
        if (!$this->unit_price || !$this->vat_percentage) {
            return 0;
        }
        return ($this->unit_price * $this->vat_percentage) / 100;
    }

    /**
     * Calculate total cost: (Quantity * Unit Price) - Discount + VAT
     */
    public function calculateTotalCost()
    {
        if (!$this->quantity || !$this->unit_price) {
            return 0;
        }
        $subtotal = $this->quantity * $this->unit_price;
        $discount = $this->discount ?? 0;
        $vat = $this->vat ?? 0;
        return $subtotal - $discount + $vat;
    }

    /**
     * Update item pricing
     */
    public function updatePricing($data)
    {
        if (isset($data['unit_price'])) {
            $this->unit_price = $data['unit_price'];
        }
        if (isset($data['discount'])) {
            $this->discount = $data['discount'];
        }
        if (isset($data['vat_percentage'])) {
            $this->vat_percentage = $data['vat_percentage'];
        }

        // Recalculate VAT and Total Cost
        $this->vat = $this->calculateVAT();
        $this->total_cost = $this->calculateTotalCost();
        $this->save();

        return $this;
    }

    public function quotation_info()
    {
        return $this->belongsTo(quotation_info::class);
    }
}
