<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Reset Password</h2>

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

        <form method="POST" action="{{ route('password.reset.post') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" value="{{ $otp }}">
            <div class="mb-3">
                <label class="block text-sm font-medium">New Password</label>
                <input type="password" name="password" required class="w-full border rounded px-3 py-2" placeholder="Enter new password">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2" placeholder="Confirm new password">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Reset Password</button>
                <a href="{{ route('login') }}" class="text-sm text-blue-600">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>