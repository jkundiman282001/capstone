@extends('layouts.student')

@section('title', 'My Profile - IP Scholar Portal')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
    </style>
@endpush

@push('head-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@section('content')
@php
    $student = $student ?? auth()->user();
    $fullName = trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name);
    $studentNumber = $student->student_number ?? ('IP-' . str_pad($student->id, 5, '0', STR_PAD_LEFT));
    $courseName = $student->course ?? 'Course not set';
    $ethnicity = optional($student->ethno)->ethnicity ?? 'Not declared';
    $applicationStatus = ($applicationStatus ?? 'pending') === 'validated' ? 'validated' : 'pending';
    $statusLabel = $applicationStatus === 'validated' ? 'Validated' : 'Pending';
    $statusClasses = $applicationStatus === 'validated'
        ? 'text-green-700 bg-green-50 border-green-200'
        : 'text-amber-700 bg-amber-50 border-amber-200';
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-12 pt-20 relative overflow-hidden selection:bg-orange-100 selection:text-orange-900">
    
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-orange-50/80 via-white to-transparent pointer-events-none"></div>
    <div class="absolute -top-[20%] -right-[10%] w-[800px] h-[800px] bg-gradient-to-br from-blue-50/40 to-purple-50/40 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Sidebar (Profile Card) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Main Profile Card -->
                <div class="glass-card rounded-3xl p-8 text-center relative group hover:shadow-xl hover:shadow-orange-100/50 transition-all duration-500">
                    
                    <!-- Avatar Section -->
                    <div class="relative inline-block mx-auto mb-6">
                        <div class="w-36 h-36 rounded-full p-1.5 bg-white shadow-2xl shadow-orange-100/50 cursor-pointer group/avatar relative z-10 transition-transform duration-300 hover:scale-105" onclick="document.getElementById('profile-pic-input').click()">
                            <div class="w-full h-full rounded-full overflow-hidden relative bg-slate-50">
                                <img
                                    id="profile-pic-image"
                                    src="{{ $student->profile_pic ? Storage::url($student->profile_pic) : '' }}"
                                    alt="Profile"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110 {{ $student->profile_pic ? '' : 'hidden' }}"
                                >
                                <div id="profile-pic-placeholder" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 text-orange-300 {{ $student->profile_pic ? 'hidden' : 'flex' }}">
                                    <i data-lucide="user" class="w-16 h-16"></i>
                                </div>
                                
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                                    <i data-lucide="camera" class="w-8 h-8 text-white drop-shadow-lg"></i>
                                </div>
                            </div>
                            
                            <button class="absolute bottom-1 right-1 p-2.5 rounded-2xl bg-white text-slate-600 hover:text-orange-600 shadow-lg border border-slate-100 transition-all hover:scale-110 hover:-rotate-12 group-hover/avatar:translate-x-1 group-hover/avatar:translate-y-1">
                                <i data-lucide="pen-line" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <input type="file" id="profile-pic-input" accept="image/*" class="hidden" onchange="uploadProfilePic(this)">
                    </div>

                    <h2 class="text-2xl font-bold text-slate-800 mb-1 tracking-tight">{{ $fullName }}</h2>
                    <p class="text-sm text-slate-500 mb-6 font-medium">{{ $student->email }}</p>
                    <div class="flex justify-center mb-4">
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-center gap-2 mb-8">
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-600 border border-orange-100 shadow-sm">IP Scholar</span>
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-100 shadow-sm">{{ $courseName }}</span>
                    </div>
                    
                    <!-- Apply Button in Sidebar -->
                    <a href="{{ route('student.apply') }}" class="w-full btn bg-slate-900 text-white hover:bg-slate-800 rounded-xl py-3.5 font-bold shadow-lg shadow-slate-900/20 hover:shadow-slate-900/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 group">
                        <span>Apply for Scholarship</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Right Content -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Profile Edit Form -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-white/50">
                        <h3 class="font-bold text-xl text-slate-800 flex items-center gap-2">
                            Personal Information
                        </h3>
                        <p class="text-slate-500 text-sm mt-1">Update your basic profile details here.</p>
                    </div>

                    <form action="{{ route('student.update-profile') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" placeholder="Enter first name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}" placeholder="Enter middle name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" placeholder="Enter last name" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Contact Number</label>
                                <input type="text" name="contact_num" value="{{ old('contact_num', $student->contact_num) }}" placeholder="Enter contact number" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $student->email) }}" placeholder="Enter email address" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all text-sm p-3.5 placeholder:text-orange-500 text-slate-800">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="btn bg-orange-600 text-white hover:bg-orange-700 rounded-xl px-8 py-3 text-sm font-bold shadow-lg shadow-orange-600/20 hover:-translate-y-0.5 transition-all">
                                Save Changes
                            </button>
                        </div>
                    </form>
                    </div>

                <!-- Current Academic Performance -->
                <div class="glass-card rounded-[2rem] shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-white/50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-xl text-slate-800">Current Academic Performance</h3>
                                <p class="text-slate-500 text-sm mt-1">Track your academic progress and scholarship eligibility</p>
                            </div>
                            <span class="text-green-600 bg-green-100 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap select-none">Eligible</span>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <!-- GPA, Credits Enrolled, Total Credits -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 border border-gray-100 rounded-lg overflow-hidden text-center divide-x divide-gray-100">
                            <div class="p-6">
                                <p class="text-4xl font-extrabold text-blue-600">3.85</p>
                                <p class="font-semibold text-sm text-gray-700 mt-1">Current GPA</p>
                                <p class="mt-2 text-green-600 text-xs">+0.15 from last semester</p>
                            </div>
                            <div class="p-6">
                                <p class="text-4xl font-extrabold text-blue-700">18</p>
                                <p class="font-semibold text-sm text-gray-700 mt-1">Credits Enrolled</p>
                                <a href="#" class="text-blue-600 text-xs mt-2 inline-block hover:underline">Full-time status</a>
                            </div>
                            <div class="p-6">
                                <p class="text-4xl font-extrabold text-purple-700">75</p>
                                <p class="font-semibold text-sm text-gray-700 mt-1">Total Credits</p>
                                <p class="mt-2 text-purple-600 text-xs">62.5% complete</p>
                            </div>
                        </div>

                        <!-- GPA Progress Bar -->
                        <div class="pt-2 w-full">
                            <div class="flex justify-between text-xs text-gray-500 mb-1 px-1">
                                <span>GPA Progress</span>
                                <span>Target: 3.5</span>
                            </div>
                            <progress class="w-full h-3 rounded bg-blue-100" value="3.85" max="4.0"></progress>
                        </div>

                        <!-- Academic Standing -->
                        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-md text-sm">
                            <div class="flex items-center space-x-2 font-semibold">
                                <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                                <span>Excellent Academic Standing</span>
                            </div>
                            <p class="mt-1 text-green-800 text-sm">Your GPA of 3.85 exceeds the minimum requirement of 3.5. You are currently
                                eligible for all scholarship opportunities and maintaining good academic progress.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropper-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/80 backdrop-blur-md p-4 transition-all">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[85vh] ring-1 ring-white/20">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-lg text-slate-800">Adjust Photo</h3>
                <button onclick="closeCropper()" class="p-2 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="relative flex-1 bg-slate-900 overflow-hidden flex items-center justify-center min-h-[300px]">
                <img id="cropper-image" class="max-w-full max-h-[60vh]">
            </div>
            <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
                <button onclick="closeCropper()" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl px-6 py-2.5 font-semibold">Cancel</button>
                <button onclick="cropAndUpload()" class="btn bg-orange-600 text-white hover:bg-orange-700 rounded-xl px-6 py-2.5 font-semibold shadow-lg shadow-orange-600/20">Save Photo</button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="upload-progress" class="fixed inset-0 z-[70] hidden items-center justify-center bg-white/80 backdrop-blur-md">
        <div class="flex flex-col items-center bg-white p-8 rounded-3xl shadow-2xl border border-slate-100">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-orange-500 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-slate-800 font-bold text-lg">Uploading...</p>
            <p class="text-slate-400 text-sm mt-1">Please wait a moment</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    } else {
        console.warn('Lucide icons library not loaded – skipping icon replacement.');
    }
    // --- Cropper Logic ---
    let cropper;
    let selectedFile;

    function uploadProfilePic(input) {
        const file = input.files[0];
        if (!file) return;
        selectedFile = file;

        const modal = document.getElementById('cropper-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const img = document.getElementById('cropper-image');
        img.src = URL.createObjectURL(file);

            if (cropper) cropper.destroy();
        setTimeout(() => {
            if (typeof Cropper === 'undefined') {
                console.error('CropperJS library failed to load. Please check your network connection.');
                alert('Unable to load the photo editor. Please check your internet connection and try again.');
                closeCropper();
                return;
            }
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                background: false,
                autoCropArea: 1,
            });
        }, 100);
    }

    function closeCropper() {
        const modal = document.getElementById('cropper-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (cropper) cropper.destroy();
        cropper = null;
        selectedFile = null;
        document.getElementById('profile-pic-input').value = '';
    }

    function cropAndUpload() {
        if (!cropper) return;
        cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
            document.getElementById('upload-progress').classList.remove('hidden');
            document.getElementById('upload-progress').classList.add('flex');

            const formData = new FormData();
            formData.append('profile_pic', blob, selectedFile.name);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("student.update-profile-pic") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const image = document.getElementById('profile-pic-image');
                    const placeholder = document.getElementById('profile-pic-placeholder');
                    if (image) {
                        image.src = data.profile_pic_url + '?t=' + new Date().getTime();
                        image.classList.remove('hidden');
                    }
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                    closeCropper();
                } else {
                    alert('Failed to update.');
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                document.getElementById('upload-progress').classList.add('hidden');
                document.getElementById('upload-progress').classList.remove('flex');
            });
        }, 'image/jpeg', 0.95);
    }
</script>
@endpush 
