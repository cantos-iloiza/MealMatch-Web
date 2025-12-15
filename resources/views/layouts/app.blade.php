<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'MealMatch - Find Recipes. Track Calories.')</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Custom Styles --}}
    <style>
        body {
            background: linear-gradient(135deg, #FFF5CF 0%, #dfffc6 100%);
            min-height: 100vh;
        }
        
        /* Hide scrollbar but keep functionality */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth transitions */
        .sidebar-icon {
            transition: all 0.2s ease;
        }
        
        /* Custom animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        /* Pulse animation for loading */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Sidebar styling */
        .sidebar-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            cursor: pointer;
        }
        
        .sidebar-icon:hover {
            background-color: rgba(254, 243, 199, 0.5);
        }
        
        .sidebar-icon.active {
            background-color: #FEF3C7;
        }

        /* Profile Modal - Dropdown Style */
        .profile-dropdown {
            position: fixed;
            top: 5rem;
            right: 2rem;
            z-index: 100;
            animation: dropdownSlideIn 0.3s ease;
            transform-origin: top right;
        }

        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Overlay without blur - just for clicking outside */
        .dropdown-overlay {
            position: fixed;
            inset: 0;
            z-index: 99;
            background: transparent;
        }

        /* Default Avatar Icon */
        .default-avatar {
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
        }

        /* Arrow pointing to profile button */
        .profile-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 2.5rem;
            width: 16px;
            height: 16px;
            background: rgba(255, 255, 255, 0.95);
            transform: rotate(45deg);
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
    
    {{-- Additional styles from pages --}}
    @stack('styles')
</head>
<body class="antialiased">
    <div class="flex min-h-screen">
        {{-- Left Sidebar --}}
        <aside class="w-32 bg-white/70 backdrop-blur-sm shadow-xl fixed left-0 top-0 h-full flex flex-col items-center py-8 space-y-6 z-50 rounded-r-3xl">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="mb-4">
                <div class="w-14 h-14 flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
                    <span class="text-orange text-2xl font-bold font-museo">M</span>
                </div>
            </a>
            
            {{-- Navigation Icons --}}
            <div class="flex-1 flex flex-col space-y-4">
                {{-- Home --}}
                <a href="{{ route('home') }}" 
                   class="sidebar-icon {{ request()->is('/') ? 'active' : '' }}"
                   title="Home">
                    <svg class="w-7 h-7 {{ request()->is('/') ? 'text-orange-600' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                </a>
                
                {{-- What Can I Cook --}}
                <a href="{{ route('whatcanicook') }}" 
                   class="sidebar-icon {{ request()->is('what-can-i-cook') ? 'active' : '' }}"
                   title="What Can I Cook?">
                    <svg class="w-7 h-7 {{ request()->is('what-can-i-cook') ? 'text-orange-600' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </a>
                
                {{-- NEW: Recipes Icon --}}
                <a href="{{ route('recipes.index') }}" 
                   class="sidebar-icon {{ request()->routeIs('recipes.*', 'recipe.show') ? 'active' : '' }}"
                   title="All Recipes">
                    <svg class="w-7 h-7 {{ request()->routeIs('recipes.*', 'recipe.show') ? 'text-orange-600' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3.75a9.707 9.707 0 0 0-5.25.782v13.593a.75.75 0 0 0 .916.71c.789-.25 1.638-.378 2.501-.378 1.97 0 3.8.7 5.25 1.883 1.45-1.183 3.28-1.883 5.25-1.883 1.97 0 3.8.7 5.25 1.883V6.662a9.71 9.71 0 0 0-5.25-1.883 9.707 9.707 0 0 0-5.25.782Z" />
                    </svg>
                </a>

                {{-- Food Log --}}
                <a href="{{ route('food-log.index') }}" 
                   class="sidebar-icon {{ request()->is('food-log*', 'log-food') ? 'active' : '' }}"
                   title="Food Log">
                    <svg class="w-7 h-7 {{ request()->is('food-log*', 'log-food') ? 'text-orange-600' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                
                {{-- Settings --}}
                <a href="{{ route('settings') }}"
                   class="sidebar-icon {{ request()->is('settings') ? 'active' : '' }}"
                   title="Settings">
                    <svg class="w-7 h-7 {{ request()->is('settings') ? 'text-orange-600' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </aside>
        
        {{-- Main Content Area --}}
        <div class="flex-1 ml-32">
            {{-- Top Section --}}
            <div class="px-8 pt-8 pb-6">
                <div class="flex items-center justify-between mb-8">
                    {{-- Page Title - Can be overridden by child views --}}
                    @yield('page-title')
                    
                    {{-- User Profile --}}
                    <div class="flex items-center gap-4">
                        {{-- Notifications --}}
                        <a href="{{ route('notifications') }}" class="relative bg-white/50 rounded-full p-4 hover:bg-white/75 transition">
                            <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        </a>
                        
                        {{-- User Avatar & Name - CLICKABLE --}}
                        <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 bg-white/50 hover:bg-white/75 rounded-full pr-6 pl-2 py-2 transition-all cursor-pointer">
                            @if(isset($user) && $user && $user->avatar)
                                <img src="{{ $user->avatar }}" 
                                     alt="{{ $user->name ?? 'User' }}"
                                     class="w-12 h-12 rounded-full border-2 border-white object-cover">
                            @else
                                {{-- Default Avatar Icon (Facebook style) --}}
                                <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center default-avatar">
                                    <i class="fas fa-user text-gray-600 text-xl"></i>
                                </div>
                            @endif
                            <div class="text-left">
                                <p class="font-bold text-gray-900" id="header-user-name">{{ $user->name ?? 'Guest' }}</p>
                                <p class="text-sm text-gray-600" id="header-user-status">{{ isset($user) && $user ? 'Logged in' : 'Not logged in' }}</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Page Content --}}
            <main class="px-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Profile Dropdown (No blur overlay, just appears on top) --}}
    <div id="profileDropdownOverlay" class="dropdown-overlay hidden" onclick="closeProfileDropdown()"></div>
    <div id="profileDropdown" class="profile-dropdown glass-morphism rounded-3xl p-6 w-80 shadow-2xl hidden">
        {{-- Avatar --}}
        <div class="flex justify-center mb-4">
            <div id="modal-avatar-container">
                @if(isset($user) && $user && $user->avatar)
                    <img src="{{ $user->avatar }}" 
                         alt="{{ $user->name ?? 'User' }}"
                         class="w-20 h-20 rounded-full border-4 border-white shadow-lg object-cover">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-white shadow-lg flex items-center justify-center default-avatar">
                        <i class="fas fa-user text-gray-600 text-3xl"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- User Info --}}
        <div class="text-center mb-5">
            <h3 class="text-xl font-bold text-gray-800 mb-1" id="modal-user-name">{{ $user->name ?? 'Guest' }}</h3>
            <p class="text-gray-600 text-sm" id="modal-user-email">{{ $user->email ?? 'Not logged in' }}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-2.5">
            {{-- View Full Profile --}}
            <a href="{{ route('profile.index') }}" class="block w-full bg-gradient-to-r from-[#f7941d] to-[#f5b461] hover:from-[#f5b461] hover:to-[#f7941d] text-white font-bold py-3 rounded-xl transition-all shadow-lg text-center text-sm">
                <i class="fas fa-user-circle mr-2"></i> View Full Profile
            </a>

            {{-- Logout --}}
            <button onclick="confirmLogout()" class="w-full border-2 border-gray-300 hover:border-red-400 hover:bg-red-50 text-gray-700 hover:text-red-600 font-semibold py-3 rounded-xl transition-all text-center text-sm">
                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
            </button>
        </div>
    </div>
    
    {{-- Toast Notifications Container --}}
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>
    
    {{-- Global JavaScript --}}
    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // ====== PROFILE DROPDOWN FUNCTIONS ======
        
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const overlay = document.getElementById('profileDropdownOverlay');
            
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                overlay.classList.remove('hidden');
            } else {
                closeProfileDropdown();
            }
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const overlay = document.getElementById('profileDropdownOverlay');
            dropdown.classList.add('hidden');
            overlay.classList.add('hidden');
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                showToast('Logging out...', 'info');
                window.location.href = '/logout';
            }
        }

        // Close dropdown with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeProfileDropdown();
            }
        });
        
        // ====== END PROFILE DROPDOWN FUNCTIONS ======
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg shadow-lg text-white animate-slide-in ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 
                'bg-blue-500'
            }`;
            toast.textContent = message;
            
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Loading spinner function
        function showLoading(show = true) {
            let spinner = document.getElementById('global-spinner');
            
            if (show) {
                if (!spinner) {
                    spinner = document.createElement('div');
                    spinner.id = 'global-spinner';
                    spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                    spinner.innerHTML = `
                        <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                            <svg class="animate-spin h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-700 mt-3">Loading...</p>
                        </div>
                    `;
                    document.body.appendChild(spinner);
                }
            } else {
                if (spinner) {
                    spinner.remove();
                }
            }
        }
        
        // Format number with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Fetch wrapper with error handling
        async function fetchAPI(url, options = {}) {
            try {
                options.headers = {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...options.headers
                };
                
                const response = await fetch(url, options);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('An error occurred. Please try again.', 'error');
                throw error;
            }
        }

        // Logout function
        // function logout() {
        //    if (confirm('Are you sure you want to logout?')) {
        //        showToast('Logging out...', 'info');
        //        // window.location.href = '/logout';
        //    }
        //}
    </script>
    
    {{-- Additional scripts from pages --}}
    @stack('scripts')
</body>
</html>
