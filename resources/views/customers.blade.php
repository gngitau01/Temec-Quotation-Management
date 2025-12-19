@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <script>
        // Redirect legacy root-level customers view to resource index
        window.location.href = "{{ route('customers.index') }}";
    </script>
    <noscript>
        <div class="p-6">
            <p>Please enable JavaScript to be redirected. <a href="{{ route('customers.index') }}">Go to Customers</a></p>
        </div>
    </noscript>
@endsection
