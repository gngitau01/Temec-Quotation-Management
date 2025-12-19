<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class buyers extends Model
{
     protected $fillable = ['quotation_id', 'name', 'address', 'town', 'country'];
     
     public function quotation_Info()
     {
          return $this->hasMany(quotation_info::class,);
     }
}
