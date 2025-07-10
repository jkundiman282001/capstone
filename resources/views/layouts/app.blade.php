<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCIP Staff Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-blue-50 via-cyan-100 to-blue-200 min-h-screen font-sans">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-800 via-blue-600 to-cyan-500 shadow-lg">
        <div class="container mx-auto flex items-center px-6 py-4">
            <img src="{{ asset('National_Commission_on_Indigenous_Peoples_(NCIP).png') }}" alt="NCIP Logo" class="h-10 w-10 mr-4 rounded-full bg-white shadow">
            <span class="text-white text-2xl font-bold tracking-wide">NCIP Scholarship System</span>
        </div>
    </header>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg border-r hidden md:block">
            <nav class="flex flex-col h-full py-8 px-4 space-y-2">
                <a href="{{ route('staff.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-700 bg-blue-100 font-semibold hover:bg-blue-200 transition">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('staff.reports.download') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Download Reports
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7"></path></svg>
                    Logout
                </a>
                <a href="#feedback-section" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 14h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z"/></svg>
                    Feedback & Support
                </a>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html> 