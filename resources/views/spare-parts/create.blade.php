@extends('layouts.app')

@section('title', 'Add Spare Part')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Spare Part</h2>

        <div class="bg-white rounded shadow p-8">
            <form action="{{ route('spare-parts.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Spare Part Number *</label>
                    <input type="text" name="part_number" value="{{ old('part_number') }}" required
                        class="w-full border @error('part_number') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('part_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Spare Part Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Spare Part Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border @error('description') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount per Unit *</label>
                    <input type="number" step="0.01" name="amount_per_unit" value="{{ old('amount_per_unit') }}" required
                        class="w-full border @error('amount_per_unit') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('amount_per_unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Create Spare Part
                    </button>
                    <a href="{{ route('spare-parts.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection