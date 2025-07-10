<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support & Help - IP Scholar Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ip-cyan': '#06B6D4',
                        'ip-dark': '#0F172A',
                        'ip-card': '#1E293B'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav x-data="{ open: false }" class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="text-2xl font-bold">
                        <span class="text-orange-400">IP Scholar</span>
                        <span class="text-orange-400/80 text-lg ml-2">Portal</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-orange-400 transition-colors">Home</a>
                    <a href="{{ route('student.profile') }}" class="text-gray-600 hover:text-orange-400 transition-colors">Profile</a>
                    <a href="{{ route('student.performance') }}" class="text-gray-600 hover:text-orange-400 transition-colors">Performance</a>
                    <a href="{{ route('student.notifications') }}" class="text-gray-600 hover:text-orange-400 transition-colors">Notifications</a>
                    <a href="{{ route('student.support') }}" class="text-gray-600 hover:text-orange-400 transition-colors">Support/Help</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-6 py-2 border border-red-500 text-red-400 rounded-lg hover:bg-red-700 hover:text-white transition-all">Log Out</button>
                        </form>
                    @endauth
                </div>
                <!-- Hamburger Button (Mobile) -->
                <button @click="open = !open" class="md:hidden text-orange-400 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden bg-white border-t border-orange-100 px-6 py-4">
            <div class="flex flex-col space-y-4">
                <a href="{{ route('student.dashboard') }}" class="text-orange-600 hover:text-orange-400 transition-colors">Home</a>
                <a href="{{ route('student.profile') }}" class="text-orange-600 hover:text-orange-400 transition-colors">Profile</a>
                <a href="{{ route('student.performance') }}" class="text-orange-600 hover:text-orange-400 transition-colors">Performance</a>
                <a href="{{ route('student.notifications') }}" class="text-orange-600 hover:text-orange-400 transition-colors">Notifications</a>
                <a href="{{ route('student.support') }}" class="text-orange-600 hover:text-orange-400 transition-colors">Support/Help</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-0 text-red-400 hover:text-white transition-all">Log Out</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-12 mb-8">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h1 class="text-3xl font-bold mb-2">Support & Help</h1>
            <p class="text-orange-100">How can we help you? Find answers to common questions or contact our support team.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 pb-16">
        <!-- FAQ Section -->
        <div class="mb-12">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <details class="bg-white rounded-lg shadow p-4 group">
                    <summary class="font-semibold text-orange-600 cursor-pointer group-open:text-orange-700">How do I apply for a scholarship?</summary>
                    <p class="mt-2 text-gray-700">Go to the Dashboard and click the "Apply for Scholarship" button. Fill out the required information and submit your application. You can track your application status on your dashboard.</p>
                </details>
                <details class="bg-white rounded-lg shadow p-4 group">
                    <summary class="font-semibold text-orange-600 cursor-pointer group-open:text-orange-700">What documents are required?</summary>
                    <p class="mt-2 text-gray-700">You will need to submit a Certificate of Low Income, proof of Indigenous status, and your latest academic records. Check the requirements section on your dashboard for more details.</p>
                </details>
                <details class="bg-white rounded-lg shadow p-4 group">
                    <summary class="font-semibold text-orange-600 cursor-pointer group-open:text-orange-700">How will I know if my application is approved?</summary>
                    <p class="mt-2 text-gray-700">You will receive a notification on your portal and an email once your application status is updated. You can also check the Notifications page for updates.</p>
                </details>
                <details class="bg-white rounded-lg shadow p-4 group">
                    <summary class="font-semibold text-orange-600 cursor-pointer group-open:text-orange-700">Who can I contact for urgent help?</summary>
                    <p class="mt-2 text-gray-700">You can use the contact form below or email our support team at <a href="mailto:support@ipscholar.com" class="text-orange-600 underline">support@ipscholar.com</a>. For urgent issues, please indicate "URGENT" in your message subject.</p>
                </details>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow p-8 mb-12">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Contact Support</h2>
            <form method="POST" action="#">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1 font-medium">Your Email</label>
                    <input type="email" name="email" value="{{ $student->email }}" class="w-full border rounded px-3 py-2" required readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1 font-medium">Subject</label>
                    <input type="text" name="subject" class="w-full border rounded px-3 py-2" placeholder="Subject" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1 font-medium">Message</label>
                    <textarea name="message" rows="4" class="w-full border rounded px-3 py-2" placeholder="Describe your issue or question..." required></textarea>
                </div>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2 rounded transition">Send Message</button>
            </form>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Links</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition">Go to Dashboard</a>
                <a href="{{ route('student.notifications') }}" class="px-4 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition">View Notifications</a>
                <a href="{{ route('student.profile') }}" class="px-4 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition">Edit Profile</a>
            </div>
        </div>
    </div>
</body>
</html> 