@extends('layouts.student')

@section('title', 'Profile - IP Scholar Portal')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-earth {
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 25%, #CD853F 50%, #DEB887 75%, #F4A460 100%);
        }
        
        .gradient-nature {
            background: linear-gradient(135deg, #2D5016 0%, #4F7942 25%, #228B22 50%, #32CD32 75%, #90EE90 100%);
        }
        
        .gradient-sky {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 25%, #06b6d4 50%, #0891b2 75%, #0e7490 100%);
        }
        
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .earth-accent {
            border-left: 4px solid #D2691E;
        }
        
        .nature-accent {
            border-left: 4px solid #228B22;
        }
        
        .sky-accent {
            border-left: 4px solid #06b6d4;
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .nav-gradient {
            background: linear-gradient(135deg, rgba(139, 69, 19, 0.95) 0%, rgba(210, 105, 30, 0.95) 50%, rgba(205, 133, 63, 0.95) 100%);
        }

        .sticky-sidebar {
            position: sticky;
            top: 1rem;
            align-self: flex-start;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }

        .content-grid {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        @media (max-width: 1024px) {
            .content-grid {
                flex-direction: column;
            }
            
            .sticky-sidebar {
                position: static;
                max-height: none;
                overflow-y: visible;
            }
        }
    </style>
@endpush

@push('head-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@section('content')
<div class="bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 min-h-screen pt-20">

    <!-- Hero Section with Cultural Elements -->
    <div class="relative overflow-hidden">
        <div class="gradient-sky pattern-dots">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                    <div class="relative">
                        <div id="profile-pic-container" class="w-32 h-32 bg-gradient-to-br from-amber-200 to-orange-300 rounded-full border-4 border-white shadow-xl flex items-center justify-center cursor-pointer hover:shadow-2xl transition-all duration-300 overflow-hidden" onclick="document.getElementById('profile-pic-input').click()">
                            @if(auth()->user()->profile_pic)
                                <img id="profile-pic-image" src="{{ Storage::url(auth()->user()->profile_pic) }}" alt="Profile Picture" class="w-full h-full object-cover rounded-full">
                            @else
                                <i data-lucide="user" class="w-16 h-16 text-orange-700"></i>
                            @endif
                            <div class="absolute inset-0 bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 rounded-full flex items-center justify-center">
                                <i data-lucide="camera" class="w-8 h-8 text-white opacity-0 hover:opacity-100 transition-all duration-300"></i>
                            </div>
                        </div>
                        <input type="file" id="profile-pic-input" accept="image/*" class="hidden" onchange="uploadProfilePic(this)">
                        <div id="upload-progress" class="hidden absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        <h2 class="text-4xl font-bold text-orange-400 mb-2">
                            @auth
                                {{ trim(auth()->user()->first_name . ' ' . (auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '') . auth()->user()->last_name) }}
                            @else
                                Guest
                            @endauth
                        </h2>
                        <p class="text-blue-400 text-lg mb-1">Student ID: 2024-001</p>
                        <p class="text-blue-500 font-medium mb-3">Indigenous Youth Scholar</p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="bg-white bg-opacity-20 text-black px-3 py-1 rounded-full text-sm">
                                {{ auth()->user()->course ? auth()->user()->course : 'Course not set' }}
                            </span>
                            <span class="bg-white bg-opacity-20 text-black px-3 py-1 rounded-full text-sm">Class of 2025</span>
                            <span class="bg-white bg-opacity-20 text-black px-3 py-1 rounded-full text-sm">Dean's List</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 gradient-earth opacity-10 rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 gradient-nature opacity-10 rounded-full translate-y-24 -translate-x-24"></div>
    </div>

     <!-- Main Content -->
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-8 relative z-10">
        <div class="content-grid">
            
            <!-- Left Sidebar - Profile & Stats (Sticky) -->
            <div class="sticky-sidebar w-full lg:w-96 flex-shrink-0">
                <div class="space-y-6">
                    <!-- Personal Journey Card -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-4">
                            <i data-lucide="compass" class="w-5 h-5 text-orange-600"></i>
                            <h3 class="text-lg font-semibold text-gray-800">My Journey</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="earth-accent bg-orange-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 font-medium">Academic Excellence</span>
                                    <span class="text-orange-600 font-bold">3.8 GPA</span>
                                </div>
                                <div class="w-full bg-orange-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-orange-500 to-red-500 h-2 rounded-full" style="width: 95%"></div>
                                </div>
                            </div>
                            
                            <div class="nature-accent bg-green-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 font-medium">Degree Progress</span>
                                    <span class="text-green-600 font-bold">62.5%</span>
                                </div>
                                <div class="w-full bg-green-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full" style="width: 62.5%"></div>
                                </div>
                            </div>
                            
                            <div class="sky-accent bg-cyan-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 font-medium">Community Impact</span>
                                    <span class="text-cyan-600 font-bold">250+ hrs</span>
                                </div>
                                <div class="w-full bg-cyan-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" style="width: 80%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cultural Connections -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-4">
                            <i data-lucide="heart" class="w-5 h-5 text-red-500"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Cultural Connections</h3>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 p-3 bg-amber-50 rounded-lg">
                                <div class="w-8 h-8 gradient-earth rounded-full flex items-center justify-center">
                                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Indigenous Student Alliance</p>
                                    <p class="text-sm text-gray-600">President</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                <div class="w-8 h-8 gradient-nature rounded-full flex items-center justify-center">
                                    <i data-lucide="leaf" class="w-4 h-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Environmental Stewardship</p>
                                    <p class="text-sm text-gray-600">Volunteer</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 gradient-sky rounded-full flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-4 h-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Indigenous Knowledge Keeper</p>
                                    <p class="text-sm text-gray-600">Mentorship Program</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Highlights -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-4">
                            <i data-lucide="award" class="w-5 h-5 text-yellow-600"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Recent Achievements</h3>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                <span class="text-gray-700 text-sm">Academic Excellence Grant - $5,000</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-gray-700 text-sm">Community Leadership Recognition</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-700 text-sm">Dean's List - 3 Semesters</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-4">
                            <i data-lucide="zap" class="w-5 h-5 text-purple-600"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                        </div>
                        
                        <div class="space-y-3">
                            <button class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">
                                Apply for Scholarship
                            </button>
                            <button class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all">
                                Update Documents
                            </button>
                            <button class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all">
                                View Calendar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area (Scrollable) -->
            <div class="main-content">
                <div class="space-y-6">
                    
                    <!-- Current Applications -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="scroll" class="w-5 h-5 text-orange-600"></i>
                                <h3 class="text-xl font-semibold text-gray-800">Scholarship Applications</h3>
                            </div>
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">3 Active</span>
                        </div>
                        
                        <div class="grid gap-4">
                            <!-- Application 1 -->
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4 hover-lift">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="sun" class="w-5 h-5 text-yellow-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Indigenous Youth Leadership Fund</h4>
                                            <p class="text-gray-600 text-sm">Supporting future Indigenous leaders in academia</p>
                                            <p class="text-gray-500 text-xs mt-1">Submitted: November 15, 2024</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Under Review</span>
                                        <p class="text-yellow-700 font-bold text-lg mt-1">$7,500</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Application 2 -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 hover-lift">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Academic Excellence Grant</h4>
                                            <p class="text-gray-600 text-sm">Recognition for outstanding academic performance</p>
                                            <p class="text-gray-500 text-xs mt-1">Awarded: October 20, 2024</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Approved</span>
                                        <p class="text-green-700 font-bold text-lg mt-1">$5,000</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Application 3 -->
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 hover-lift">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Community Leadership Award</h4>
                                            <p class="text-gray-600 text-sm">Additional documentation required for completion</p>
                                            <p class="text-gray-500 text-xs mt-1">Updated: November 10, 2024</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Action Needed</span>
                                        <button class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm mt-2 hover:bg-red-600">
                                            Upload Docs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Portfolio -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-6">
                            <i data-lucide="folder" class="w-5 h-5 text-blue-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800">Academic Portfolio</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-xl p-4 hover-lift">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-6 h-6 text-red-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Official Transcript</h4>
                                        <p class="text-gray-600 text-sm">Updated: Nov 15, 2024</p>
                                        <span class="text-red-600 text-xs font-medium">Verified</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100 rounded-xl p-4 hover-lift">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Resume & CV</h4>
                                        <p class="text-gray-600 text-sm">Updated: Nov 10, 2024</p>
                                        <span class="text-blue-600 text-xs font-medium">Current</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-xl p-4 hover-lift">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <i data-lucide="star" class="w-6 h-6 text-green-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Letters of Recommendation</h4>
                                        <p class="text-gray-600 text-sm">Updated: Nov 8, 2024</p>
                                        <span class="text-green-600 text-xs font-medium">3 Letters</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100 rounded-xl p-4 hover-lift">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <i data-lucide="pen-tool" class="w-6 h-6 text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Personal Statement</h4>
                                        <p class="text-gray-600 text-sm">Updated: Nov 5, 2024</p>
                                        <span class="text-purple-600 text-xs font-medium">Reviewed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Community Impact -->
                    <div class="glass-effect rounded-2xl p-6 hover-lift">
                        <div class="flex items-center space-x-2 mb-6">
                            <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800">Community Impact & Leadership</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 gradient-nature rounded-full flex items-center justify-center">
                                        <i data-lucide="tree-pine" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Environmental Conservation</h4>
                                        <p class="text-gray-600 text-sm">120 hours • Land restoration project
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropper-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
        <div style="background:#fff; padding:20px; border-radius:10px; max-width:90vw; max-height:90vh;">
            <div>
                <img id="cropper-image" style="max-width:70vw; max-height:60vh;">
            </div>
            <div style="margin-top:10px; text-align:right;">
                <button onclick="closeCropper()" style="margin-right:10px;">Cancel</button>
                <button onclick="cropAndUpload()">Crop & Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cropper;
    let selectedFile;

    function uploadProfilePic(input) {
        const file = input.files[0];
        if (!file) return;

        selectedFile = file;

        // Show modal
        const modal = document.getElementById('cropper-modal');
        modal.style.display = 'flex';

        // Show image in modal
        const img = document.getElementById('cropper-image');
        img.src = URL.createObjectURL(file);

        // Wait for image to load, then initialize cropper
        img.onload = function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, {
                aspectRatio: 1, // Circle
                viewMode: 1,
                background: false,
                autoCropArea: 1,
            });
        };
    }

    function closeCropper() {
        document.getElementById('cropper-modal').style.display = 'none';
        if (cropper) cropper.destroy();
        cropper = null;
        selectedFile = null;
        document.getElementById('profile-pic-input').value = '';
    }

    function cropAndUpload() {
        if (!cropper) return;
        // Get cropped image as blob
        cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
            // Show progress indicator
            document.getElementById('upload-progress').classList.remove('hidden');

            // Prepare form data
            const formData = new FormData();
            formData.append('profile_pic', blob, selectedFile.name);
            formData.append('_token', '{{ csrf_token() }}');

            // Upload via AJAX
            fetch('{{ route("student.update-profile-pic") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the profile picture
                    const image = document.getElementById('profile-pic-image');
                    if (image) {
                        image.src = data.profile_pic_url + '?t=' + new Date().getTime(); // bust cache
                    }
                    showNotification('Profile picture updated successfully!', 'success');
                } else {
                    showNotification('Failed to update profile picture. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while uploading. Please try again.', 'error');
            })
            .finally(() => {
                document.getElementById('upload-progress').classList.add('hidden');
                closeCropper();
            });
        }, 'image/jpeg', 0.95);
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
@endpush 