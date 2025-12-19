<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Create an account</h2>

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Register</button>
                <a href="{{ route('login') }}" class="text-sm text-blue-600">Have an account? Log in</a>
            </div>
        </form>
    </div>
</body>
</html>
