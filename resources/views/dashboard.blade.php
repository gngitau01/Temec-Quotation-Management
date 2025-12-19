@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <div class="space-x-4">
            <a href="{{ route('customers.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded">Customers</a>
            <a href="{{ route('quotations.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded">Quotations</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-sm text-gray-500">Customers</h3>
            <div class="text-3xl font-bold mt-2">{{ $customersCount ?? 0 }}</div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-sm text-gray-500">Quotations</h3>
            <div class="text-3xl font-bold mt-2">{{ $quotationsCount ?? 0 }}</div>
        </div>
    </div>

@endsection
