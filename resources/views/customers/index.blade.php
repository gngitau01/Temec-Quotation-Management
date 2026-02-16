@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Customers</h2>
        <a href="{{ route('customers.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i> Add Customer
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        @if ($customers->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Factory #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Town</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $customer->factory_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $customer->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->town }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone_number }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('customers.show', $customer->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this customer?');">
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

            <div class="px-6 py-4 bg-gray-50">
                {{ $customers->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-300 mb-4 block"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No customers yet</h3>
                <p class="text-gray-500 mb-6">Create your first customer to get started.</p>
                <a href="{{ route('customers.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition inline-block">
                    <i class="fas fa-user-plus mr-2"></i> Add Customer
                </a>
            </div>
        @endif
    </div>
@endsection
