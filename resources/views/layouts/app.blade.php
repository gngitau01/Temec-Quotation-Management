<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 280px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar-hidden { margin-left: 0; }
        .sidebar-visible { margin-left: 280px; }
        .mobile-menu-btn { display: none; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; z-index: 1000; }
            .sidebar.active { transform: translateX(0); }
            .mobile-menu-btn { display: block; }
            .sidebar-visible { margin-left: 0; }
        }
        .dropdown-menu { display: none; position: absolute; top: 100%; right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 200px; z-index: 100; }
        .dropdown-menu.active { display: block; }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar text-white" id="sidebar">
        <div class="p-6">
            <h2 class="text-2xl font-bold">TEMEC</h2>
            <p class="text-sm text-purple-200">Dashboard</p>
        </div>
        <nav class="mt-8">
            <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-purple-700 transition {{ request()->routeIs('dashboard') ? 'bg-purple-900' : '' }}">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <a href="{{ route('quotations.api-view') }}" class="block px-6 py-3 hover:bg-purple-700 transition {{ request()->routeIs('quotations.api-view') ? 'bg-purple-900' : '' }}">
                <i class="fas fa-file-pdf mr-2"></i> Quotations
            </a>
            <a href="{{ route('customers.index') }}" class="block px-6 py-3 hover:bg-purple-700 transition {{ request()->routeIs('customers.*') ? 'bg-purple-900' : '' }}">
                <i class="fas fa-users mr-2"></i> Customers
            </a>
            <a href="{{ route('quotations.index') }}" class="block px-6 py-3 hover:bg-purple-700 transition {{ request()->routeIs('quotations.create') ? 'bg-purple-900' : '' }}">
                <i class="fas fa-file-pdf mr-2"></i> Uploads
            </a>
            <a href="{{ route('settings') }}" class="block px-6 py-3 hover:bg-purple-700 transition {{ request()->routeIs('settings') ? 'bg-purple-900' : '' }}">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="sidebar-visible" id="mainContent">
        <!-- Top Navbar -->
        <nav class="bg-white shadow sticky top-0 z-50">
            <div class="px-6 py-4 flex items-center justify-between">
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-btn text-gray-600 hover:text-gray-900 text-xl" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Right Side: Time & User Menu -->
                <div class="ml-auto flex items-center gap-6">
                    <!-- Current Time -->
                    <div class="text-sm text-gray-600" id="currentTime">
                        <span id="timeDisplay">00:00:00</span>
                    </div>

                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center gap-3 text-gray-700 hover:text-gray-900" id="userMenuBtn">
                            <div class="w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">
                                {{ optional(Auth::user())->name ? substr(optional(Auth::user())->name, 0, 1) : '' }}
                            </div>
                            <span class="text-sm hidden sm:inline">{{ optional(Auth::user())->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu" id="userDropdown">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="p-6">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Toggle user dropdown
        document.getElementById('userMenuBtn').addEventListener('click', () => {
            document.getElementById('userDropdown').classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('userDropdown');
            const userMenuBtn = document.getElementById('userMenuBtn');
            if (!e.target.closest('.relative')) {
                dropdown.classList.remove('active');
            }
        });

        // Update time every second
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('timeDisplay').textContent = `${hours}:${minutes}:${seconds}`;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>

    @yield('scripts')
</body>
</html>
