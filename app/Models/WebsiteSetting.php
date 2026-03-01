<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'time_zone',
        'currency',
        'vat_percentage',
        'external_api_upload_url',
        'external_api_create_quotation_url',
    ];

    protected $casts = [
        'vat_percentage' => 'decimal:2',
    ];

    public static function current(): self
    {
        // Safe fallback (e.g. before migrations are run)
        if (!Schema::hasTable('website_settings')) {
            $s = new static();
            $s->time_zone = config('app.timezone');
            $s->currency = null;
            $s->vat_percentage = 16;
            $s->external_api_upload_url = null;
            $s->external_api_create_quotation_url = null;
            return $s;
        }

        return static::query()->first() ?? static::query()->create([
            'time_zone' => config('app.timezone'),
            'currency' => null,
            'vat_percentage' => 16,
            'external_api_upload_url' => null,
            'external_api_create_quotation_url' => null,
        ]);
    }
}

