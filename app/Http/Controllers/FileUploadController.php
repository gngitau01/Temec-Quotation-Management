<?php

namespace App\Http\Controllers;

use App\Models\buyers;
use App\Models\items;
use App\Models\quotation_info;
use App\Models\QuotationFile;
use App\Models\vendors;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\WebsiteSetting;

class FileUploadController extends Controller
{
    public function saveQuotationFile(Request $request)
    {
        // Validation
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'file' => 'required|file|max:2048'
        ]);

        $file = $request->file('file');
        $file_size = $file->getSize();
        $MimeType = $file->getMimeType();

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $uniqueName = uniqid('quote_') . '_' . time() . '.' . $extension;
        $path = $file->storeAs('uploads', $uniqueName);

        // Save entry in DB
        $fileRecord = QuotationFile::create([
            'customer_id' => $request->customer_id,
            'file_name' => $originalName,
            'file_path' => $path,
            'file_size' => $file_size,
            'mime_type' => $MimeType
        ]);
        return response()->json([
            'Message' => "Quotation Saved Successfully.",
            'data' => $fileRecord
        ], 200);
    }
    public function listQuotations()
    {
        $quotations = QuotationFile::all();
        return response()->json([
            'data' => $quotations
        ], 200);
    }

    public function deleteQuotation($id)
    {
        $quotation = QuotationFile::find($id);

        if (!$quotation) {
            return response()->json([
                'message' => 'quotation not found'
            ], 404);
        }

        $quotation->delete();

        return response()->json([
            'message' => 'quotation deleted successfully'
        ], 200);
    }
    public function search(Request $request)
    {
        $query = QuotationFile::query();

        if ($request->id) {
            $query->where('id', 'like', '%' . $request->id . '%');
        }
        if ($request->customer_id) {
            $query->where('customer_id', 'like', '%' . $request->customer_id . '%');
        }

        if ($request->file_name) {
            $query->where('file_name', 'like', '%' . $request->file_name . '%');
        }


        return response()->json($query->get());
    }

    public function readQuotationFiles(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'file' => 'required|file|max:2048|mimes:pdf'
            ]);

            $file = $request->file('file');
            $apiURL = env('EXTERNAL_API_UPLOAD_URL', 'http://139.162.155.251:8080/api/pdf/upload');

            // Stream directly to external API using the temporary uploaded file
            $response = Http::timeout(60)
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($apiURL, [
                    'customer_id' => $request->customer_id,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            // Simply forward the external API response

            $responseData = $response->json();
            $documentInfo = [
                'customer_id' => $request->customer_id,
                'document_number' => $responseData['documentNumber'],
                'date' => Carbon::createFromFormat('d.m.Y', $responseData['date'])->format('Y-m-d'),
                'collective_no' => $responseData['collectiveNo'],
                'vendorNo' => $responseData['vendorNo'],
                'email' => $responseData['email'],
                'closing_date' => Carbon::createFromFormat('d.m.Y', $responseData['closingDate'])->format('Y-m-d'),
                'closingTime' => $responseData['closingTime'],
                'prepared_by' => $responseData['preparedBy'],
                'approved_by' => $responseData['approvedBy']
            ];

            $vendorInfo = [
                'vendorNo' => $responseData['vendorNo'],
                'name' => $responseData['vendor']['name'],
                'location' => $responseData['vendor']['location']
            ];
            $buyerInfo = [
                'name' => $responseData['buyer']['name'],
                'address' => $responseData['buyer']['address'],
                'town' => $responseData['buyer']['town'],
                'country' => $responseData['buyer']['country']
            ];

            $items = $responseData['items'];
            $defaultVat = WebsiteSetting::current()->vat_percentage ?? 0;

            // Use database transaction to ensure data consistency
            DB::transaction(function () use ($documentInfo, $vendorInfo, $buyerInfo, $items, $defaultVat) {
                $document = quotation_info::create($documentInfo);

                // Create or update vendor
                $vendor = vendors::updateOrCreate(
                    ['quotation_id' => $document->id],
                    $vendorInfo
                );

                // Create buyer
                $buyer = buyers::create(array_merge(['quotation_id' => $document->id], $buyerInfo));

                // Create document

                // Create items
                foreach ($items as $item) {
                    $unitPrice = !empty($item['unitPrice']) ? (float) $item['unitPrice'] : 0;
                    $discountPercent = !empty($item['discount']) ? (float) $item['discount'] : 0;
                    $discountValue = ($unitPrice > 0 && $discountPercent > 0)
                        ? ($unitPrice * $discountPercent) / 100
                        : 0;

                    $vatPercentage = isset($item['vatPercentage']) || isset($item['vat_percentage'])
                        ? (float) ($item['vatPercentage'] ?? $item['vat_percentage'])
                        : (float) $defaultVat;

                    items::create([
                        'quotation_id' => $document->id,
                        'item_no' => $item['itemNo'],
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'unit_price' => $unitPrice ?: null,
                        'discount' => $discountPercent ?: null,
                        'discount_value' => $discountValue ?: null,
                        'vat' => $item['vat'] ? floatval($item['vat']) : null,
                        'vat_percentage' => $vatPercentage,
                        'total_cost' => $item['totalCost'] ? floatval($item['totalCost']) : null,
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'document' => $documentInfo,
                    'vendor' => $vendorInfo,
                    'buyer' => $buyerInfo,
                    'items' => $items
                ],
                'message' => 'Data extracted successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'error' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('File stream error', [
                'error' => $e->getMessage(),
                'customer_id' => $request->customer_id ?? 'unknown'
            ]);

            return response()->json([
                'status' => false,
                'error' => 'UPLOAD_ERROR',
                'message' => 'Failed to upload file to external service',
                'details' => config('app.debug') ? $e->getMessage() : 'Upload service unavailable'
            ], 502);
        }
    }

    public function readQuotationById($id)
    {
        try {
            $quotation = quotation_info::findOrFail($id);
            $vendor = vendors::where('quotation_id', $id)->first();
            $buyer = buyers::where('quotation_id', $id)->first();
            $items = items::where('quotation_id', $id)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'quotation' => $quotation,
                    'vendor' => $vendor,
                    'buyer' => $buyer,
                    'items' => $items
                ],
                'message' => 'Quotation details retrieved successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quotation not found',
                'quotation_id' => $id
            ], 404);
        } catch (Exception $e) {
            Log::error('Error retrieving quotation', [
                'quotation_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve quotation details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function updateItemPricing(Request $request, $itemId)
    {
        try {
            $item = items::findOrFail($itemId);

            $request->validate([
                'unit_price' => 'sometimes|numeric|min:0',
                'discount' => 'sometimes|numeric|min:0',
            ]);

            // VAT percentage is always taken from WebsiteSetting inside updatePricing()
            $item->updatePricing($request->only(['unit_price', 'discount']));

            return response()->json([
                'status' => 'success',
                'message' => 'Item pricing updated successfully',
                'data' => [
                    'id' => $item->id,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'vat' => $item->vat,
                    'vat_percentage' => $item->vat_percentage,
                    'total_cost' => $item->total_cost
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found',
                'item_id' => $itemId
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error updating item pricing', [
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update item pricing',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    public function getItemPricing($itemId)
    {
        try {
            $item = items::findOrFail($itemId);

            $data = [
                'id' => $item->id,
                'item_no' => $item->item_no,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'vat_percentage' => $item->vat_percentage,
                'vat' => $item->vat,
                'total_cost' => $item->total_cost,
            ];

            $spare = $item->item_no ? \App\Models\SparePart::where('part_number', $item->item_no)->first() : null;
            if ($spare) {
                $data['spare_part_suggestion'] = [
                    'unit_price' => $spare->amount_per_unit,
                    'discount' => $spare->discount,
                    'vat_percentage' => WebsiteSetting::current()->vat_percentage,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found',
                'item_id' => $itemId
            ], 404);
        } catch (Exception $e) {
            Log::error('Error retrieving item pricing', [
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve item pricing',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send quotation data to external API generation endpoint
     * POST /api/quotation/{id}/generate
     */
    public function sendQuotationToApi($id)
    {
        try {
            $quotation = quotation_info::findOrFail($id);
            $vendor = vendors::where('quotation_id', $id)->first();
            $buyer = buyers::where('quotation_id', $id)->first();
            $items = items::where('quotation_id', $id)->get()->map(function ($i) {
                return [
                    'itemNo' => $i->item_no,
                    'description' => $i->description,
                    'quantity' => $i->quantity,
                    'unit' => $i->unit,
                    'unitPrice' => $i->unit_price,
                    'discount' => $i->discount,
                    'vat' => $i->vat,
                    'totalCost' => $i->total_cost,
                ];
            })->toArray();

            // Build payload matching the format expected by the generation API (flattened fields)
            $payload = [
                'documentNumber' => $quotation->document_number,
                'date' => $quotation->date ? Carbon::parse($quotation->date)->format('d.m.Y') : null,
                'collectiveNo' => $quotation->collective_no ?? null,
                'closingDate' => $quotation->closing_date ? Carbon::parse($quotation->closing_date)->format('d.m.Y') : null,
                'closingTime' => $quotation->closingTime ?? null,
                'preparedBy' => $quotation->prepared_by ?? null,
                'approvedBy' => $quotation->approved_by ?? null,
                'email' => $quotation->email ?? null,
                'vendorNo' => $vendor->vendorNo ?? null,
                'vendor' => $vendor ? [
                    'name' => $vendor->name ?? null,
                    'location' => $vendor->location ?? null,
                ] : null,
                'buyer' => $buyer ? [
                    'name' => $buyer->name ?? null,
                    'address' => $buyer->address ?? null,
                    'town' => $buyer->town ?? null,
                    'country' => $buyer->country ?? null,
                ] : null,
                'items' => $items,
                'quotation_id' => $id,
            ];

            $apiURL = env('API_GENERATION_URL', 'http://139.162.155.251:8080/api/pdf/create');

            // Log payload for debugging (will appear in storage/logs)
            Log::info('Sending quotation to generation API', ['quotation_id' => $id, 'payload' => $payload]);

            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->timeout(60)
                ->post($apiURL, $payload);

            // Log response for debugging
            Log::info('Generation API response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Quotation sent to generation API successfully',
                    'api_response' => $response->json()
                ], 200);
            }

            // Non-successful response
            return response()->json([
                'status' => 'error',
                'message' => 'Generation API error',
                'status_code' => $response->status(),
                'body' => $response->body()
            ], 502);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Quotation not found', 'quotation_id' => $id], 404);
        } catch (Exception $e) {
            Log::error('Error sending quotation to generation API', ['quotation_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to send quotation', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Get list of all quotations with summary information
     */
    public function listQuotationsWithSummary()
    {
        try {
            $quotations = quotation_info::with(['vendor', 'buyers', 'items'])->orderByDesc('id')->get();
            
            $quotationList = $quotations->map(function ($quotation) {
                $items = $quotation->items ?? collect();
                $totalCost = $items->sum('total_cost');
                $itemCount = $items->count();
                
                return [
                    'id' => $quotation->id,
                    'document_number' => $quotation->document_number,
                    'date' => $quotation->date,
                    'collective_no' => $quotation->collective_no,
                    'closing_date' => $quotation->closing_date,
                    'closing_time' => $quotation->closing_time,
                    'email' => $quotation->email,
                    'prepared_by' => $quotation->prepared_by,
                    'approved_by' => $quotation->approved_by,
                    'vendor_name' => $quotation->vendor?->name ?? 'N/A',
                    'buyer_name' => $quotation->buyers?->name ?? 'N/A',
                    'item_count' => $itemCount,
                    'total_cost' => $totalCost,
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'data' => $quotationList,
                'count' => $quotationList->count(),
                'message' => 'Quotations retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error retrieving quotations', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve quotations',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}

