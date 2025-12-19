@extends('layouts.app')

@section('title', 'Quotation Details - ' . ($quotation->document_number ?? 'N/A'))

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Quotation #{{ $quotation->document_number }}</h2>
            <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <!-- Document Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-600">Document Number</p>
                <p class="text-lg font-semibold text-gray-900">{{ $quotation->document_number }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-600">Document Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ $quotation->date ? \Carbon\Carbon::parse($quotation->date)->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-600">Collective No.</p>
                <p class="text-lg font-semibold text-gray-900">{{ $quotation->collective_no ?? 'N/A' }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-600">Closing Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ $quotation->closing_date ? \Carbon\Carbon::parse($quotation->closing_date)->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Vendor Information -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Information</h3>
                @if ($vendor)
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Vendor Number</p>
                            <p class="text-gray-900">{{ $vendor->vendorNo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900">{{ $vendor->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="text-gray-900">{{ $vendor->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No vendor information available</p>
                @endif
            </div>

            <!-- Buyer Information -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Buyer Information</h3>
                @if ($buyer)
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900">{{ $buyer->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Address</p>
                            <p class="text-gray-900">{{ $buyer->address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Town</p>
                            <p class="text-gray-900">{{ $buyer->town ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Country</p>
                            <p class="text-gray-900">{{ $buyer->country ?? 'N/A' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No buyer information available</p>
                @endif
            </div>
        </div>

        <!-- Quotation Details -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quotation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Vendor No.</p>
                    <p class="text-gray-900 font-medium">{{ $quotation->vendorNo ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-gray-900 font-medium">{{ $quotation->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Closing Time</p>
                    <p class="text-gray-900 font-medium">{{ $quotation->closingTime ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Prepared By</p>
                    <p class="text-gray-900 font-medium">{{ $quotation->prepared_by ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Approved By</p>
                    <p class="text-gray-900 font-medium">{{ $quotation->approved_by ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        @if ($items && count($items) > 0)
            <div class="bg-white rounded shadow overflow-hidden">
                <h3 class="text-lg font-semibold text-gray-900 p-6 border-b">Quotation Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Item #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Discount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">VAT</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_no }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->unit }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ $item->unit_price ? number_format($item->unit_price, 2) : 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ $item->discount ? number_format($item->discount, 2) : '0.00' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ $item->vat ? number_format($item->vat, 2) : '0.00' }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">{{ $item->total_cost ? number_format($item->total_cost, 2) : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white p-6 rounded shadow text-center">
                <p class="text-gray-500">No items found for this quotation</p>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6 flex justify-between">
            <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Quotations
            </a>
        </div>
    </div>
@endsection
