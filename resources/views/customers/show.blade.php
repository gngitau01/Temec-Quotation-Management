@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('customers.edit', $customer) }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition text-sm">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Delete this customer?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded shadow p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Factory Number</label>
                    <p class="text-lg text-gray-900">{{ $customer->factory_number }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                    <p class="text-lg text-gray-900">{{ $customer->contact_person }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <p class="text-lg text-gray-900">{{ $customer->address }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Town</label>
                    <p class="text-lg text-gray-900">{{ $customer->town }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <p class="text-lg text-gray-900">{{ $customer->phone_number }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                    <p class="text-lg text-gray-900">{{ $customer->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Updated At</label>
                    <p class="text-lg text-gray-900">{{ $customer->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t flex gap-4">
                <a href="{{ route('customers.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Customers
                </a>
            </div>
        </div>
    </div>
@endsection
