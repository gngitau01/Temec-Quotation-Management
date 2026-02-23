@extends('layouts.app')

@section('title', 'Spare Parts')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Spare Parts</h2>
        <div class="space-x-2">
            <a href="{{ route('spare-parts.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                <i class="fas fa-plus mr-2"></i> Add Spare Part
            </a>
            <form action="{{ route('spare-parts.import') }}" method="POST" enctype="multipart/form-data" class="inline">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required class="hidden" id="file-input">
                <label for="file-input" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition cursor-pointer">
                    <i class="fas fa-upload mr-2"></i> Import from Excel
                </label>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                    Upload
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        @if ($spareParts->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Part Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount per Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">VAT (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Discount (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spareParts as $sparePart)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $sparePart->part_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $sparePart->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $sparePart->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($sparePart->amount_per_unit, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($sparePart->vat, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($sparePart->discount, 2) }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('spare-parts.edit', $sparePart->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('spare-parts.destroy', $sparePart->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this spare part?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-500">
                No spare parts found. <a href="{{ route('spare-parts.create') }}" class="text-purple-600 hover:text-purple-900">Add one now</a>.
            </div>
        @endif
    </div>
@endsection