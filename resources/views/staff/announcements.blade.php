@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-600 shadow-lg shadow-blue-200/50">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Announcements</h1>
                        <p class="text-slate-500 text-sm mt-0.5">Create and manage announcements for students</p>
                    </div>
                </div>
            </div>
            <button onclick="showCreateAnnouncementModal()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Announcement
            </button>
        </div>

        <!-- Announcements List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            @if($announcements->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($announcements as $announcement)
                        <div class="border-2 border-slate-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-lg transition-all">
                            @if($announcement->image_path)
                                <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-48 object-cover rounded-lg mb-4">
                            @endif
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-black text-slate-900 flex-1">{{ $announcement->title }}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-700' : ($announcement->priority === 'high' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($announcement->priority) }}
                                    </span>
                                    <button onclick="confirmDeleteAnnouncement({{ $announcement->id }})" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Announcement">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-4 line-clamp-3">{{ Str::limit($announcement->content, 120) }}</p>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                @if($announcement->creator)
                                    <span>By {{ $announcement->creator->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h3 class="text-slate-900 font-bold text-lg mb-2">No announcements yet</h3>
                    <p class="text-slate-500 text-sm mb-4">Create your first announcement to notify students</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Announcement Modal -->
<div id="createAnnouncementModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-blue-500 to-cyan-500 flex-shrink-0">
            <div>
                <h3 class="text-xl font-bold text-white">Create Announcement</h3>
                <p class="text-sm text-blue-100 mt-1">Share important information with all students</p>
            </div>
            <button onclick="closeCreateAnnouncementModal()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/20 text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <form id="createAnnouncementForm" onsubmit="submitAnnouncement(event)">
                <div class="mb-6">
                    <label for="announcementTitle" class="block text-sm font-bold text-slate-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="announcementTitle" 
                        name="title" 
                        required
                        placeholder="Enter announcement title..."
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                    />
                </div>
                
                <div class="mb-6">
                    <label for="announcementContent" class="block text-sm font-bold text-slate-700 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="announcementContent" 
                        name="content" 
                        rows="8" 
                        required
                        placeholder="Enter announcement content..."
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm resize-none"
                    ></textarea>
                </div>

                <div class="mb-6">
                    <label for="announcementPriority" class="block text-sm font-bold text-slate-700 mb-2">
                        Priority
                    </label>
                    <select 
                        id="announcementPriority" 
                        name="priority"
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm bg-white"
                    >
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="announcementImage" class="block text-sm font-bold text-slate-700 mb-2">
                        Image (Optional)
                    </label>
                    <div class="space-y-3">
                        <div class="relative">
                            <input 
                                type="file" 
                                id="announcementImage" 
                                name="image" 
                                accept="image/*"
                                onchange="handleImagePreview(this)"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                        </div>
                        <div id="imagePreviewContainer" class="hidden">
                            <div class="relative inline-block">
                                <img id="imagePreview" src="" alt="Preview" class="max-w-full h-48 rounded-xl object-cover border-2 border-slate-200">
                                <button 
                                    type="button" 
                                    onclick="removeImagePreview()" 
                                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-all"
                                    title="Remove image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Supported formats: JPG, PNG, GIF. Max size: 5MB</p>
                </div>

                <div class="flex items-center justify-end gap-3 flex-shrink-0 pt-4 border-t border-slate-200">
                    <button type="button" onclick="closeCreateAnnouncementModal()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="submitAnnouncementBtn" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showCreateAnnouncementModal() {
        const modal = document.getElementById('createAnnouncementModal');
        const form = document.getElementById('createAnnouncementForm');
        form.reset();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('announcementTitle').focus(), 100);
    }

    function closeCreateAnnouncementModal() {
        const modal = document.getElementById('createAnnouncementModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('createAnnouncementForm').reset();
        removeImagePreview();
    }

    function handleImagePreview(input) {
        const file = input.files[0];
        const previewContainer = document.getElementById('imagePreviewContainer');
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file.');
                input.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must be less than 5MB.');
                input.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function removeImagePreview() {
        const input = document.getElementById('announcementImage');
        const previewContainer = document.getElementById('imagePreviewContainer');
        input.value = '';
        previewContainer.classList.add('hidden');
    }

    function submitAnnouncement(event) {
        event.preventDefault();
        
        const title = document.getElementById('announcementTitle').value.trim();
        const content = document.getElementById('announcementContent').value.trim();
        const priority = document.getElementById('announcementPriority').value;
        const imageInput = document.getElementById('announcementImage');
        
        if (!title || !content) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Validate image if provided
        if (imageInput.files.length > 0) {
            const file = imageInput.files[0];
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must be less than 5MB.');
                return;
            }
        }
        
        const submitBtn = document.getElementById('submitAnnouncementBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
        submitBtn.disabled = true;
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('priority', priority);
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        formData.append('_token', '{{ csrf_token() }}');
        
        // Send API request to create announcement
        fetch('{{ route("staff.announcements.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCreateAnnouncementModal();
                alert('Announcement created successfully!');
                // Reload page to show new announcement
                window.location.reload();
            } else {
                alert('Error creating announcement: ' + (data.message || 'Unknown error'));
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating announcement. Please try again.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    // Close modal on outside click
    document.getElementById('createAnnouncementModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCreateAnnouncementModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCreateAnnouncementModal();
    });

    function confirmDeleteAnnouncement(id) {
        if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
            deleteAnnouncement(id);
        }
    }

    function deleteAnnouncement(id) {
        fetch(`/staff/announcements/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Announcement deleted successfully!');
                window.location.reload();
            } else {
                alert('Error deleting announcement: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the announcement.');
        });
    }
</script>
@endpush
@endsection

