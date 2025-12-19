@extends('layouts.app')

@section('title', 'Quotation Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $quotation->file_name }}</h2>
            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirm('Delete this quotation?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- File Info Card -->
            <div class="bg-white rounded shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-file-pdf text-4xl text-red-500 mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">File Type</p>
                        <p class="text-lg font-semibold text-gray-900">{{ strtoupper(pathinfo($quotation->file_name, PATHINFO_EXTENSION)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Size Card -->
            <div class="bg-white rounded shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-database text-4xl text-blue-500 mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">File Size</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($quotation->file_size / 1024, 2) }} KB</p>
                    </div>
                </div>
            </div>

            <!-- Date Card -->
            <div class="bg-white rounded shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-calendar text-4xl text-green-500 mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Uploaded</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $quotation->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded shadow p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Name</label>
                    <p class="text-lg text-gray-900">{{ $quotation->file_name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">MIME Type</label>
                    <p class="text-lg text-gray-900">{{ $quotation->mime_type }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <p class="text-lg text-gray-900">
                        {{ $quotation->customer->name ?? 'N/A' }}
                        @if ($quotation->customer)
                            <a href="{{ route('customers.show', $quotation->customer) }}" class="text-blue-600 hover:text-blue-900 text-sm ml-2">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Date</label>
                    <p class="text-lg text-gray-900">{{ $quotation->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Path</label>
                    <p class="text-lg text-gray-900 break-all">{{ $quotation->file_path }}</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t flex gap-4">
                <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Quotations
                </a>
            </div>
        </div>
    </div>
@endsection
