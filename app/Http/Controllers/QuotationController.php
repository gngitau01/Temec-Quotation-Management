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

        $file = $request->file('file');
        $file_size = $file->getSize();
        $mimeType = $file->getMimeType();
        $path = $file->store('uploads');

        $fileRecord = QuotationFile::create([
            'customer_id' => $request->customer_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file_size,
            'mime_type' => $mimeType
        ]);

        $apiURL = env('EXTERNAL_API_UPLOAD_URL', 'http://139.162.165.148:8080/api/pdf/upload');

        try {
            $storedPath = storage_path('app/' . $fileRecord->file_path);
            if (!file_exists($storedPath)) {
                return redirect()->route('quotations.index')->with('error', 'File saved but could not send to API.');
            }

            $response = Http::attach(
                'file', fopen($storedPath, 'r'), $fileRecord->file_name
            )->post($apiURL, [
                'customer_id' => $request->customer_id,
                'file_name' => $fileRecord->file_name,
            ]);

            if ($response->successful()) {
                return redirect()->route('quotations.index')->with('status', 'Quotation uploaded and sent to API successfully.');
            } else {
                return redirect()->route('quotations.index')->with('warning', 'File uploaded but API responded with: ' . $response->status());
            }
        } catch (\Exception $e) {
            return redirect()->route('quotations.index')->with('error', 'File uploaded but API error: ' . $e->getMessage());
        }
    }

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
}
