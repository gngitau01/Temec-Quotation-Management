<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Verify OTP</h2>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="text-sm text-gray-600 mb-4">
            We've sent a 6-digit OTP to <strong>{{ $email }}</strong>. Please enter it below.
        </p>

        <form method="POST" action="{{ route('password.verify.post') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="mb-3">
                <label class="block text-sm font-medium">OTP Code</label>
                <input type="text" name="otp" maxlength="6" required class="w-full border rounded px-3 py-2 text-center text-lg tracking-widest" placeholder="000000">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Verify OTP</button>
                <a href="{{ route('password.forgot') }}" class="text-sm text-blue-600">Resend OTP</a>
            </div>
        </form>
    </div>
</body>
</html>