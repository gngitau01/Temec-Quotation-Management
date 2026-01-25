<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class quotation_info extends Model
{
    /**
     * Explicit table name to match migration (singular 'quotation_info').
     */
    protected $table = 'quotation_info';
    protected $fillable = [
        'customer_id',
        'document_number',
        'date',
        'collective_no',
        'vendorNo',
        'email',
        'closing_date',
        'closing_time',
        'prepared_by',
        'approved_by'
    ];

    public function items()
    {
        return $this->hasMany(items::class,'quotation_id');
    }

    public function vendor()
    {
        return $this->hasOne(vendors::class, 'quotation_id');
    }
    public function buyers()
    {
        return $this->hasOne(buyers::class, 'quotation_id');
    }
}
