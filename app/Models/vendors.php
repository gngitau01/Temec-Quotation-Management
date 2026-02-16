<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendors extends Model
{
    protected $fillable = ['quotation_id','vendorNo', 'name', 'location'];

    public function quotation_Info()
    {
        return $this->hasMany(quotation_info::class);
    }
}
