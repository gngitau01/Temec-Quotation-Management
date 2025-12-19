@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Quotations</h2>
        <div class="space-x-3">
            <a href="{{ route('quotations.api-view') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition inline-block">
                <i class="fas fa-database mr-2"></i> View by API
            </a>
            <a href="{{ route('quotations.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition inline-block">
                <i class="fas fa-upload mr-2"></i> Upload Quotation
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded flex items-center justify-between">
            {{ session('status') }}
            <button class="text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded flex items-center justify-between">
            {{ session('error') }}
            <button class="text-red-700 hover:text-red-900" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-700 rounded flex items-center justify-between">
            {{ session('warning') }}
            <button class="text-yellow-700 hover:text-yellow-900" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        @if ($quotations->count())
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">File Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Size</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Uploaded</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotations as $quotation)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <i class="fas fa-file-pdf text-red-500 mr-2"></i>{{ Str::limit($quotation->file_name, 30) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $quotation->customer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($quotation->file_size / 1024, 2) }} KB</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $quotation->mime_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $quotation->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-900 text-sm mr-3" title="View File">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirm('Delete this quotation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-6">
                {{ $quotations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-pdf text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No quotations yet</h3>
                <p class="text-gray-500 mb-6">Start by uploading your first quotation file.</p>
                <a href="{{ route('quotations.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition inline-block">
                    <i class="fas fa-upload mr-2"></i> Upload File
                </a>
            </div>
        @endif
    </div>
@endsection
