@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h2>

            <div class="mb-6">
                <div class="w-20 h-20 bg-purple-500 text-white rounded-full flex items-center justify-center text-3xl font-bold mb-4">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <p class="text-lg text-gray-900">{{ Auth::user()->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-lg text-gray-900">{{ Auth::user()->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                    <p class="text-lg text-gray-900">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <button class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                    <i class="fas fa-edit mr-2"></i> Edit Profile
                </button>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
