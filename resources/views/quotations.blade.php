@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
    <script>
        // Redirect legacy root-level quotations view to resource index
        window.location.href = "{{ route('quotations.index') }}";
    </script>
    <noscript>
        <div class="p-6">
            <p>Please enable JavaScript to be redirected. <a href="{{ route('quotations.index') }}">Go to Quotations</a></p>
        </div>
    </noscript>
@endsection
