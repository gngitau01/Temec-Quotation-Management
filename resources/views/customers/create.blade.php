@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Customer</h2>

        <div class="bg-white rounded shadow p-8">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Factory Number *</label>
                    <input type="text" name="factory_number" value="{{ old('factory_number') }}" required
                        class="w-full border @error('factory_number') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('factory_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}" required
                        class="w-full border @error('address') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Town *</label>
                    <input type="text" name="town" value="{{ old('town') }}" required
                        class="w-full border @error('town') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('town')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                        class="w-full border @error('phone_number') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('phone_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                        class="w-full border @error('contact_person') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('contact_person')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Create Customer
                    </button>
                    <a href="{{ route('customers.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
