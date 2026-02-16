<?php

namespace App\Http\Controllers;

use App\Models\QuotationFile;
use App\Models\Customer;
use App\Models\quotation_info;
use App\Models\items;
use App\Models\vendors;
use App\Models\buyers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = QuotationFile::with('customer')->paginate(15);
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('quotations.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'file' => 'required|file|max:2048'
        ]);

        $apiURL = env('EXTERNAL_API_UPLOAD_URL', 'http://139.162.155.251:8080/api/pdf/upload');

        try {
            // Send file directly to API (no local storage)
            $response = Http::attach(
                'file', 
                $request->file('file')->get(),  // Raw file content
                $request->file('file')->getClientOriginalName()
            )->post($apiURL, [
                'customer_id' => $request->customer_id,
                'file_name' => $request->file('file')->getClientOriginalName(),
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                // Convert date format from DD.MM.YYYY to YYYY-MM-DD
                $date = null;
                if (isset($data['date'])) {
                    try {
                        $date = \Carbon\Carbon::createFromFormat('d.m.Y', $data['date'])->toDateString();
                    } catch (\Exception $e) {
                        $date = now()->toDateString();
                    }
                } else {
                    $date = now()->toDateString();
                }
                
                $closingDate = null;
                if (isset($data['closingDate'])) {
                    try {
                        $closingDate = \Carbon\Carbon::createFromFormat('d.m.Y', $data['closingDate'])->toDateString();
                    } catch (\Exception $e) {
                        $closingDate = null;
                    }
                }
                
                // Create quotation_info record from API response
                $quotation = quotation_info::create([
                    'customer_id' => $request->customer_id,
                    'document_number' => $data['documentNumber'] ?? null,
                    'date' => $date,
                    'collective_no' => $data['collectiveNo'] ?? null,
                    'vendorNo' => $data['vendorNo'] ?? 'N/A',
                    'email' => $data['email'] ?? null,
                    'closing_date' => $closingDate,
                    'closing_time' => $data['closingTime'] ?? null,
                    'prepared_by' => $data['preparedBy'] ?? null,
                    'approved_by' => $data['approvedBy'] ?? null,
                ]);

                // Create or update vendor
                try {
                    if (isset($data['vendor']) && is_array($data['vendor'])) {
                        vendors::updateOrCreate(
                            ['quotation_id' => $quotation->id],
                            [
                                'vendorNo' => $data['vendorNo'] ?? 'N/A',
                                'name' => $data['vendor']['name'] ?? null,
                                'location' => $data['vendor']['location'] ?? null
                                ]
                                );
                                // dd($data);
                    }
                } catch (\Exception $e) {
                    Log::error('Vendor creation failed: ' . $e->getMessage());
                }

                // Create or update buyer
                try {
                    if (isset($data['buyer']) && is_array($data['buyer'])) {
                        buyers::updateOrCreate(
                            ['quotation_id' => $quotation->id],
                            [
                                'name' => $data['buyer']['name'] ?? null,
                                'address' => $data['buyer']['address'] ?? null,
                                'town' => $data['buyer']['town'] ?? null,
                                'country' => $data['buyer']['country'] ?? null
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Buyer creation failed: ' . $e->getMessage());
                }

                // Create items
                try {
                    Log::info('Items data check', ['items_exist' => isset($data['items']), 'is_array' => is_array($data['items'] ?? null), 'data_keys' => array_keys($data)]);
                    
                    if (isset($data['items']) && is_array($data['items'])) {
                        Log::info('Creating items', ['count' => count($data['items'])]);
                        
                        foreach ($data['items'] as $item) {
                            if (is_array($item)) {
                                items::create([
                                    'quotation_id' => $quotation->id,
                                    'item_no' => $item['itemNo'] ?? $item['item_no'] ?? null,
                                    'description' => $item['description'] ?? null,
                                    'quantity' => !empty($item['quantity']) ? (float) $item['quantity'] : 0,
                                    'unit' => $item['unit'] ?? null,
                                    'unit_price' => !empty($item['unitPrice']) ? (float) $item['unitPrice'] : 0,
                                    'discount' => !empty($item['discount']) ? (float) $item['discount'] : null,
                                    'vat' => !empty($item['vat']) ? (float) $item['vat'] : null,
                                    'vat_percentage' => (float) ($item['vatPercentage'] ?? $item['vat_percentage'] ?? 0),
                                    'total_cost' => !empty($item['totalCost']) ? (float) $item['totalCost'] : null,
                                ]);
                            }
                        }
                        Log::info('Items created successfully');
                    } else {
                        Log::warning('Items not found or not an array', ['data' => $data]);
                    }
                } catch (\Exception $e) {
                    Log::error('Items creation failed: ' . $e->getMessage());
                }

                // Save file record
                $fileRecord = QuotationFile::create([
                    'customer_id' => $request->customer_id,
                    'file_name' => $data['file_name'] ?? $request->file('file')->getClientOriginalName(),
                    'file_path' => $data['file_path'] ?? $data['url'] ?? 'quotation_' . time() . '.pdf',
                    'file_size' => $data['file_size'] ?? $request->file('file')->getSize(),
                    'mime_type' => $data['mime_type'] ?? $request->file('file')->getMimeType(),
                    'api_response' => json_encode($data)
                ]);

                return redirect()->route('quotations.index')
                    ->with('status', 'File uploaded to API and saved successfully. ID: ' . $fileRecord->id);
            } else {
                return redirect()->route('quotations.index')
                    ->with('error', 'API upload failed: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->route('quotations.index')
                ->with('error', 'Upload error: ' . $e->getMessage());
        }
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'customer_id' => 'required|exists:customers,id',
    //         'file' => 'required|file|max:2048'
    //     ]);

    //     $file = $request->file('file');
    //     $file_size = $file->getSize();
    //     $mimeType = $file->getMimeType();
    //     $path = $file->store('uploads');

    //     $fileRecord = QuotationFile::create([
    //         'customer_id' => $request->customer_id,
    //         'file_name' => $file->getClientOriginalName(),
    //         'file_path' => $path,
    //         'file_size' => $file_size,
    //         'mime_type' => $mimeType
    //     ]);

    //     $apiURL = env('EXTERNAL_API_UPLOAD_URL', 'http://139.162.155.251:8080/api/pdf/upload');

    //     try {
    //         $storedPath = storage_path('app/' . $fileRecord->file_path);
    //         if (!file_exists($storedPath)) {
    //             return redirect()->route('quotations.index')->with('error', 'File saved but could not send to API.');
    //         }

    //         $response = Http::attach(
    //             'file',
    //             fopen($storedPath, 'r'),
    //             $fileRecord->file_name
    //         )->post($apiURL, [
    //             'customer_id' => $request->customer_id,
    //             'file_name' => $fileRecord->file_name,
    //         ]);

    //         if ($response->successful()) {
    //             return redirect()->route('quotations.index')->with('status', 'Quotation uploaded and sent to API successfully.');
    //         } else {
    //             return redirect()->route('quotations.index')->with('warning', 'File uploaded but API responded with: ' . $response->status());
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->route('quotations.index')->with('error', 'File uploaded but API error: ' . $e->getMessage());
    //     }
    // }

    public function show(QuotationFile $quotation)
    {
        return view('quotations.show', compact('quotation'));
    }

    public function destroy(QuotationFile $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('status', 'Quotation deleted successfully.');
    }

    public function details($id)
    {
        $quotation = quotation_info::findOrFail($id);
        $vendor = vendors::where('quotation_id', $id)->first();
        $buyer = buyers::where('quotation_id', $id)->first();
        $items = items::where('quotation_id', $id)->get();

        return view('quotations.details', compact('quotation', 'vendor', 'buyer', 'items'));
    }

    public function apiView()
    {
        return view('quotations.api-view');
    }

    public function detail($id)
    {
        $quotation = quotation_info::findOrFail($id);
        $vendor = vendors::where('quotation_id', $id)->first();
        $buyer = buyers::where('quotation_id', $id)->first();
        $items = items::where('quotation_id', $id)->get();

        return response()->json([
            'status' => false,
            'details' => $quotation
        ], 502);
    }
    public function generatePdf($id)
    {
        $quotation = quotation_info::with('items', 'vendor', 'buyers')->findOrFail($id);

        // Transform items to match API template expectations
        $transformedItems = $quotation->items->map(function ($item) {
            return [
                'itemNo' => $item->item_no,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'unitPrice' => (float) $item->unit_price,
                'discount' => $item->discount ? (float) $item->discount : '',
                'vat' => $item->vat ? (float) $item->vat : '',
                'totalCost' => $item->total_cost ? (float) $item->total_cost : ''
            ];
        })->toArray();

        $data = [
            'documentNumber' => $quotation->document_number ?? '',
            'date' => $quotation->date ?? now()->format('d.m.Y'),
            'collectiveNo' => $quotation->collective_no ?? '',
            'vendorNo' => $quotation->vendor->vendorNo ?? '',
            'email' => $quotation->email ?? '',
            'closingDate' => $quotation->closing_date ?? '',
            'closingTime' => $quotation->closing_time ?? '',
            'preparedBy' => $quotation->prepared_by ?? '',
            'approvedBy' => $quotation->approved_by ?? '',
            'vendor' => [
                'name' => $quotation->vendor->name ?? '',
                'location' => $quotation->vendor->location ?? ''
            ],
            'buyer' => [
                'name' => $quotation->buyers->name ?? '',
                'address' => $quotation->buyers->address ?? '',
                'town' => $quotation->buyers->town ?? '',
                'country' => $quotation->buyers->country ?? ''
            ],
            'items' => $transformedItems
        ];

        $apiURL = 'http://139.162.155.251:8080/api/pdf/create';

        try {
            $response = Http::asJson()->post($apiURL, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                $filePath = $responseData['filePath'] ?? null;

                if ($filePath) {
                    // Fetch the actual PDF file using the filePath
                    $pdfUrl = "http://139.162.155.251:8080/api/pdf/download/{$filePath}";
                    $pdfResponse = Http::get($pdfUrl);

                    if ($pdfResponse->successful()) {
                        return response($pdfResponse->body(), 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'attachment; filename="quotation_' . ($data['quotation']['documentNumber'] ?? 'doc') . '.pdf"'
                        ]);
                    } else {
                        return redirect()->back()->with('error', 'PDF download failed: ' . $pdfResponse->status());
                    }
                } else {
                    return redirect()->back()->with('error', 'API error: No file path returned. ' . json_encode($responseData));
                }
            } else {
                return redirect()->back()->with('error', 'API error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}
