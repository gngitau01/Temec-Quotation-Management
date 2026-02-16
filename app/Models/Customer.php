<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     use HasFactory;

    protected $fillable = [
        'factory_number',
        'name',
        'address',
        'town',
        'phone_number',
        'contact_person',
    ];
}
