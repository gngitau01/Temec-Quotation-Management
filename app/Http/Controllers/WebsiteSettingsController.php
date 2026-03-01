<?php

namespace App\Http\Controllers;

use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingsController extends Controller
{
    public function show()
    {
        $settings = WebsiteSetting::current();

        return response()->json([
            'status' => 'success',
            'data' => [
                'time_zone' => $settings->time_zone,
                'currency' => $settings->currency,
                'vat_percentage' => $settings->vat_percentage,
                'external_api_upload_url' => $settings->external_api_upload_url,
                'external_api_create_quotation_url' => $settings->external_api_create_quotation_url,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'time_zone' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:8',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'external_api_upload_url' => 'nullable|string|max:2000',
            'external_api_create_quotation_url' => 'nullable|string|max:2000',
        ]);

        $settings = WebsiteSetting::current();
        $settings->fill($validated);
        $settings->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Settings updated successfully',
            'data' => [
                'time_zone' => $settings->time_zone,
                'currency' => $settings->currency,
                'vat_percentage' => $settings->vat_percentage,
                'external_api_upload_url' => $settings->external_api_upload_url,
                'external_api_create_quotation_url' => $settings->external_api_create_quotation_url,
            ],
        ]);
    }
}

