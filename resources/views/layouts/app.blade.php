<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Web Inventory')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <nav class="bg-white shadow-lg border-b-4 border-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-800">
                            Web Inventory
                        </span>
                    </a>
                </div>

                @auth
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                        Dashboard
                    </a>
                    @if(auth()->user()->isManager())
                    <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                        Users
                    </a>
                    @endif
                    
                    <!-- Items Dropdown -->
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button type="button" 
                            class="flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                            :class="open || {{ (auth()->user()->isManager() && request()->routeIs(['items.*', 'categories.*'])) || request()->routeIs(['stock-in.*', 'stock-out.*']) ? 'true' : 'false' }} ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'">
                            <span>@if(auth()->user()->isManager())Items @else Stock @endif</span>
                            <svg class="ml-1 w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-0 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 py-2"
                            style="display: none;">
                            @if(auth()->user()->isManager())
                            <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                    Category
                                </div>
                            </a>
                            <a href="{{ route('items.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('items.*') ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    Product
                                </div>
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            @endif
                            <a href="{{ route('stock-in.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('stock-in.*') ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Stock In
                                </div>
                            </a>
                            <a href="{{ route('stock-out.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('stock-out.*') ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    Stock Out
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('maintenances.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('maintenances.*') ? 'text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                        Maintenance
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2 px-3 py-2">
                        <span class="text-gray-700 font-medium text-sm">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-500 text-white hover:bg-red-600 transition-all duration-200 shadow-md">
                            Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success') || session('status'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') ?? session('status') }}',
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                position: 'center',
                showConfirmButton: true
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                position: 'center',
                html: `
                    <ul style="text-align: left; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                showConfirmButton: true
            });
        @endif
    });

    // Real-time Form Validation (Disable submit until valid)
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Ignore logout and delete forms
            if(form.classList.contains('logout-form') || form.classList.contains('delete-form')) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            if(!submitBtn) return;

            const checkFormValidity = () => {
                if(form.checkValidity()) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            };

            // Initial check
            checkFormValidity();

            // Check on input and change
            form.addEventListener('input', checkFormValidity);
            form.addEventListener('change', checkFormValidity);

            // Handle submit to show processing state
            form.addEventListener('submit', function(e) {
                if(!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                if(!submitBtn.disabled) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Processing...';
                }
            });
        });
    });

    // Auto-submit GET forms with debounce for Search/Filters
    document.addEventListener('DOMContentLoaded', function() {
        const searchForms = document.querySelectorAll('form[method="GET"]');
        searchForms.forEach(form => {
            const textInputs = form.querySelectorAll('input[type="text"]');
            let timeout = null;
            
            textInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        form.submit();
                    }, 500); // 500ms delay
                });
            });
        });
    });

    // Logout confirmation
    document.querySelector('.logout-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    </script>
</body>
</html>
