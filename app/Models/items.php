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
        'discount', // percentage
        'discount_value', // per-unit monetary discount
        'vat',
        'vat_percentage',
        'total_cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'vat' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    /**
     * Calculate VAT based on unit price and VAT percentage
     * If not set, fetch from SparePart
     */
    public function calculateVAT($data)
    {
        $vat_percentage = $data['vat_percentage'] ?? null;
        // dd($vat_percentage);
        if ((!$vat_percentage || $vat_percentage == 0) && $this->item_no) {
            $spare = SparePart::where('part_number', $this->item_no)->first();
            if ($spare) {
                $vat_percentage = $spare->vat;
            }
        }
        if (!$this->unit_price || !$vat_percentage) {
            return 0;
        }

        // dd($this->item_no,$vat_percentage);
        return ($this->unit_price * $vat_percentage) / 100;
    }

    /**
     * Get discount, fallback to SparePart if not set
     */
    public function getDiscount($data)
    {
        $discount_percentage = $data['discount'] ?? null;
        if ((!$discount_percentage || $discount_percentage == 0) && $this->item_no) {
            $spare = SparePart::where('part_number', $this->item_no)->first();
            if ($spare) {
                $discount_percentage = $spare->discount;
            }
        }
        if (!$this->unit_price || !$discount_percentage) {
            return 0;
        }
        return ($this->unit_price * $discount_percentage) / 100;
    }


    /**
     * Calculate total cost: (Quantity * Unit Price) - Discount + VAT
     */
    public function calculateTotalCost($data)
    {
        if (!$this->quantity || !$this->unit_price) {
            return 0;
        }
        $discount = $this->getDiscount($data);
        $vat = $this->calculateVAT($data);
        // dd($this->unit_price, $discount, $vat);
        $subtotal = $this->quantity * (($this->unit_price - $discount) + $vat);
        return $subtotal;
    }

    /**
     * Update item pricing.
     * Accepts unit_price, discount, and vat_percentage from request (like unit_price).
     * Falls back to SparePart values only when not provided in $data.
     */
    public function updatePricing($data)
    {
        if (array_key_exists('unit_price', $data)) {
            $this->unit_price = $data['unit_price'];
        }

        $spare = $this->item_no ? SparePart::where('part_number', $this->item_no)->first() : null;

        // Use request values when provided; otherwise fall back to SparePart or zero
        if (array_key_exists('vat_percentage', $data) && $data['vat_percentage'] !== null && $data['vat_percentage'] !== '') {
            $this->vat_percentage = is_numeric($data['vat_percentage']) ? (string) round((float) $data['vat_percentage'], 2) : '0';
        } elseif ($spare && is_numeric($spare->vat)) {
            $this->vat_percentage = (string) round((float) $spare->vat, 2);
        } else {
            $this->vat_percentage = '0';
        }

        if (array_key_exists('discount', $data) && $data['discount'] !== null && $data['discount'] !== '') {
            $this->discount = is_numeric($data['discount']) ? (string) round((float) $data['discount'], 2) : '0';
        } elseif ($spare && is_numeric($spare->discount)) {
            $this->discount = (string) round((float) $spare->discount, 2);
        } else {
            $this->discount = '0';
        }

        // Compute and persist per-unit discount price
        $discountValue = round($this->getDiscount($data), 2);
        $this->discount_value = is_numeric($discountValue) ? (string) $discountValue : null;

        // Recalculate VAT amount and total cost
        $vatValue = round($this->calculateVAT($data), 2);
        $totalCostValue = round($this->calculateTotalCost($data), 2);
        $this->vat = is_numeric($vatValue) ? (string) $vatValue : null;
        $this->total_cost = is_numeric($totalCostValue) ? (string) $totalCostValue : null;
        $this->save();

        return $this;
    }

    public function quotation_info()
    {
        return $this->belongsTo(quotation_info::class);
    }
}
