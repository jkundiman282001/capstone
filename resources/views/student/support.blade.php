@extends('layouts.student')

@section('title', 'Support & Help - IP Scholar Portal')

@push('head-scripts')
<script src="https://cdn.tailwindcss.com"></script>
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
@endpush

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 pt-20">

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
</div>
@endsection 