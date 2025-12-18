@extends('layouts.app')

@section('content')
<script>
    window.openGranteesReport = function() {
        const modal = document.getElementById('granteesReportModal');
        const loading = document.getElementById('reportLoading');
        const content = document.getElementById('reportContent');
        const tableBody = document.getElementById('reportTableBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        tableBody.innerHTML = '';
        if (typeof window.hasUnsavedChanges !== 'undefined') {
            window.hasUnsavedChanges = false;
        }
        
        // Reset save button
        const saveBtn = document.getElementById('saveGrantsBtn');
        const unsavedIndicator = document.getElementById('unsavedIndicator');
        if (saveBtn) {
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            saveBtn.disabled = true;
        }
        if (unsavedIndicator) {
            unsavedIndicator.classList.add('hidden');
        }

        // Get current filter values
        const params = new URLSearchParams();
        const province = document.getElementById('province-filter')?.value;
        const municipality = document.getElementById('municipality-filter')?.value;
        const barangay = document.getElementById('barangay-filter')?.value;
        const ethno = document.getElementById('ethno-filter')?.value;

        if (province) params.append('province', province);
        if (municipality) params.append('municipality', municipality);
        if (barangay) params.append('barangay', barangay);
        if (ethno) params.append('ethno', ethno);

        fetch('{{ route("staff.grantees.report") }}?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Grantees report data received:', data);
                
                if (data.success && Array.isArray(data.grantees)) {
                    // Store data globally
                    window.granteesData = data.grantees;
                    
                    console.log(`Loaded ${data.grantees.length} grantees`);
                    
                    // Try to render immediately, or wait for function to be available
                    function tryRender() {
                        if (typeof window.renderReportTable === 'function') {
                            console.log('Rendering report table...');
                            window.renderReportTable(data.grantees);
                            loading.classList.add('hidden');
                            content.classList.remove('hidden');
                            return true;
                        }
                        return false;
                    }
                    
                    if (!tryRender()) {
                        // Wait and try again
                        const checkInterval = setInterval(function() {
                            if (tryRender()) {
                                clearInterval(checkInterval);
                            }
                        }, 50);
                        
                        // Stop trying after 2 seconds
                        setTimeout(function() {
                            clearInterval(checkInterval);
                            if (!tryRender()) {
                                console.error('renderReportTable function not found after waiting');
                                loading.classList.add('hidden');
                                content.classList.remove('hidden');
                                // Show error message in table
                                const tableBody = document.getElementById('reportTableBody');
                                if (tableBody) {
                                    tableBody.innerHTML = `
                                        <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                                Error: Could not render report. Please refresh the page.
                                            </td>
                                        </tr>
                                    `;
                                }
                            }
                        }, 2000);
                    }
                } else {
                    console.error('Invalid response data:', data);
                    alert('Error loading report data: ' + (data.message || 'Invalid response format'));
                    loading.classList.add('hidden');
                    // Show error in table
                    const tableBody = document.getElementById('reportTableBody');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                    ${data.message || 'No data available or invalid response'}
                                </td>
                            </tr>
                        `;
                    }
                    content.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading grantees report:', error);
                alert('Error loading report data: ' + error.message);
                loading.classList.add('hidden');
                // Show error in table
                const tableBody = document.getElementById('reportTableBody');
                const content = document.getElementById('reportContent');
                if (tableBody && content) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                Error loading data: ${error.message}
                            </td>
                        </tr>
                    `;
                    content.classList.remove('hidden');
                }
            });
    };

    window.openPamanaReport = function() {
        const modal = document.getElementById('pamanaReportModal');
        const loading = document.getElementById('pamanaReportLoading');
        const content = document.getElementById('pamanaReportContent');
        const tableBody = document.getElementById('pamanaReportTableBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        tableBody.innerHTML = '';
        
        // Get current filter values
        const params = new URLSearchParams();
        const province = document.getElementById('province-filter')?.value;
        const municipality = document.getElementById('municipality-filter')?.value;
        const barangay = document.getElementById('barangay-filter')?.value;
        const ethno = document.getElementById('ethno-filter')?.value;

        if (province) params.append('province', province);
        if (municipality) params.append('municipality', municipality);
        if (barangay) params.append('barangay', barangay);
        if (ethno) params.append('ethno', ethno);

        fetch('{{ route("staff.pamana.report") }}?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && Array.isArray(data.pamana)) {
                    // Store data globally
                    window.pamanaData = data.pamana;

                    function tryRender() {
                        if (typeof window.renderPamanaReportTable === 'function') {
                            window.renderPamanaReportTable(data.pamana);
                            loading.classList.add('hidden');
                            content.classList.remove('hidden');
                            return true;
                        }
                        return false;
                    }

                    if (!tryRender()) {
                        const checkInterval = setInterval(function() {
                            if (tryRender()) {
                                clearInterval(checkInterval);
                            }
                        }, 50);

                        setTimeout(function() {
                            clearInterval(checkInterval);
                            if (!tryRender()) {
                                console.error('renderPamanaReportTable function not found after waiting');
                                loading.classList.add('hidden');
                                content.classList.remove('hidden');
                                if (tableBody) {
                                    tableBody.innerHTML = `
                                        <tr>
                                            <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                                Error: Could not render Pamana report. Please refresh the page.
                                            </td>
                                        </tr>
                                    `;
                                }
                            }
                        }, 2000);
                    }
                } else {
                    console.error('Invalid Pamana response data:', data);
                    alert('Error loading Pamana report data: ' + (data.message || 'Invalid response format'));
                    loading.classList.add('hidden');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                    ${data.message || 'No data available or invalid response'}
                                </td>
                            </tr>
                        `;
                    }
                    content.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading Pamana report:', error);
                alert('Error loading Pamana report data: ' + error.message);
                loading.classList.add('hidden');
                if (tableBody && content) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-red-500">
                                Error loading data: ${error.message}
                            </td>
                        </tr>
                    `;
                    content.classList.remove('hidden');
                }
            });
    };

    window.openWaitingListReport = function() {
        const modal = document.getElementById('waitingListReportModal');
        const loading = document.getElementById('waitingReportLoading');
        const content = document.getElementById('waitingReportContent');
        const tableBody = document.getElementById('waitingReportTableBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        tableBody.innerHTML = '';
        if (typeof window.hasUnsavedWaitingChanges !== 'undefined') {
            window.hasUnsavedWaitingChanges = false;
        }
        
        // Reset save button
        const saveBtn = document.getElementById('saveWaitingGrantsBtn');
        const unsavedIndicator = document.getElementById('waitingUnsavedIndicator');
        if (saveBtn) {
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            saveBtn.disabled = true;
        }
        if (unsavedIndicator) {
            unsavedIndicator.classList.add('hidden');
        }

        // Get current filter values
        const params = new URLSearchParams();
        const province = document.getElementById('province-filter')?.value;
        const municipality = document.getElementById('municipality-filter')?.value;
        const barangay = document.getElementById('barangay-filter')?.value;
        const ethno = document.getElementById('ethno-filter')?.value;

        if (province) params.append('province', province);
        if (municipality) params.append('municipality', municipality);
        if (barangay) params.append('barangay', barangay);
        if (ethno) params.append('ethno', ethno);

        fetch('{{ route("staff.waiting-list.report") }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.waitingListData = data.waitingList;
                    
                    // Wait for render function to be available
                    const renderInterval = setInterval(function() {
                        if (typeof window.renderWaitingReportTable === 'function') {
                            clearInterval(renderInterval);
                            window.renderWaitingReportTable(data.waitingList);
                            loading.classList.add('hidden');
                            content.classList.remove('hidden');
                        }
                    }, 50);
                    
                    // Stop trying after 3 seconds
                    setTimeout(function() {
                        clearInterval(renderInterval);
                        if (typeof window.renderWaitingReportTable === 'function') {
                            window.renderWaitingReportTable(data.waitingList);
                        } else {
                            console.error('renderWaitingReportTable function not found');
                        }
                        loading.classList.add('hidden');
                        content.classList.remove('hidden');
                    }, 3000);
                } else {
                    alert('Error loading waiting list data');
                    loading.classList.add('hidden');
                    if (typeof window.closeWaitingListReport === 'function') {
                        window.closeWaitingListReport();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading waiting list data');
                loading.classList.add('hidden');
                if (typeof window.closeWaitingListReport === 'function') {
                    window.closeWaitingListReport();
                }
            });
    };

    window.closeGranteesReport = function() {
        const modal = document.getElementById('granteesReportModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    window.closePamanaReport = function() {
        const modal = document.getElementById('pamanaReportModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    window.closeWaitingListReport = function() {
        const modal = document.getElementById('waitingListReportModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // Normalize remarks/status display in reports
    window.normalizeRemarksStatus = function(value) {
        if (value === null || value === undefined) return '';
        const raw = String(value).trim();
        const lower = raw.toLowerCase();
        if (
            lower === 'validated' ||
            lower === 'grantee' ||
            lower === 'validated/grantee' ||
            lower === 'validated / grantee'
        ) {
            return 'On Going';
        }
        return raw;
    };

    // Define renderReportTable function early so it's available when needed
    window.renderReportTable = function(grantees) {
        const tableBody = document.getElementById('reportTableBody');
        const reportCount = document.getElementById('reportCount');
        
        if (!tableBody) {
            console.error('reportTableBody element not found');
            return;
        }
        
        if (!reportCount) {
            console.error('reportCount element not found');
        } else {
            reportCount.textContent = `Total Grantees: ${grantees ? grantees.length : 0}`;
        }

        if (!grantees || !Array.isArray(grantees) || grantees.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No grantees found
                    </td>
                </tr>
            `;
            return;
        }
        
        console.log(`Rendering ${grantees.length} grantees to table`);

        tableBody.innerHTML = grantees.map((grantee, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            
            // Format address and AD Reference
            const addressLine = [
                grantee.province || '',
                grantee.municipality || '',
                grantee.barangay || '',
                grantee.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            // Gender checkboxes (using exact values from form: "Male" or "Female")
            const isFemale = grantee.is_female || false;
            const isMale = grantee.is_male || false;
            
            // School info from first intended school; fallback to legacy flags
            const isPrivate = grantee.is_private || false;
            const isPublic = grantee.is_public || false;
            const schoolType = (grantee.school_type || grantee.school1_type || '').toLowerCase();
            const schoolName = grantee.school_name || grantee.school1_name || grantee.school || '';
            
            // Year level checkboxes (using boolean flags from controller)
            const is1st = grantee.is_1st || false;
            const is2nd = grantee.is_2nd || false;
            const is3rd = grantee.is_3rd || false;
            const is4th = grantee.is_4th || false;
            const is5th = grantee.is_5th || false;

            const remarksStatus = window.normalizeRemarksStatus(grantee.remarks || '');
            
            return `
                <tr class="${rowClass} hover:bg-blue-50 transition-colors">
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${addressLine}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.contact_email || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.batch || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-medium">${grantee.no || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.name || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${grantee.age || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.ethnicity || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text"
                               class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}"
                               readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text"
                               class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}"
                               readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${grantee.course || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">10,000</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">10,000</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${remarksStatus}</td>
                </tr>
            `;
        }).join('');
    };

    window.renderPamanaReportTable = function(applicants) {
        const tableBody = document.getElementById('pamanaReportTableBody');
        const reportCount = document.getElementById('pamanaReportCount');
        
        if (!tableBody) {
            console.error('pamanaReportTableBody element not found');
            return;
        }
        
        if (!reportCount) {
            console.error('pamanaReportCount element not found');
        } else {
            reportCount.textContent = `Total Pamana Applicants: ${applicants ? applicants.length : 0}`;
        }

        if (!applicants || !Array.isArray(applicants) || applicants.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No Pamana applicants found
                    </td>
                </tr>
            `;
            return;
        }
        
        tableBody.innerHTML = applicants.map((applicant, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            
            const isPrivate = applicant.is_private || false;
            const isPublic = applicant.is_public || false;
            const schoolType = (applicant.school_type || applicant.school1_type || '').toLowerCase();
            const schoolName = applicant.school_name || applicant.school1_name || applicant.school || '';
            
            const is1st = applicant.is_1st || false;
            const is2nd = applicant.is_2nd || false;
            const is3rd = applicant.is_3rd || false;
            const is4th = applicant.is_4th || false;
            const is5th = applicant.is_5th || false;
            
            return `
                <tr class="${rowClass} hover:bg-blue-50 transition-colors">
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${addressLine}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.contact_email || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${applicant.batch || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-medium">${applicant.no || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.name || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${applicant.age || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.ethnicity || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text"
                               class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}"
                               readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text"
                               class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}"
                               readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.course || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">10,000</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">10,000</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.remarks || ''}</td>
                </tr>
            `;
        }).join('');
    };

    // Initialize variables for grant management
    window.granteesData = [];
    window.pamanaData = [];
    window.hasUnsavedChanges = false;

    window.markAsChanged = function() {
        window.hasUnsavedChanges = true;
        const saveBtn = document.getElementById('saveGrantsBtn');
        const unsavedIndicator = document.getElementById('unsavedIndicator');
        if (saveBtn) {
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            saveBtn.disabled = false;
        }
        if (unsavedIndicator) {
            unsavedIndicator.classList.remove('hidden');
        }
    };
</script>
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    
    <div class="max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-600 shadow-lg shadow-orange-200/50">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Applicants</h1>
                        <p class="text-slate-500 text-sm mt-0.5">Manage scholarship applications</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filters
                </h3>
                <a href="{{ route('staff.applicants.list') }}" class="text-xs font-bold text-orange-600 hover:text-orange-800 px-3 py-1.5 rounded-lg hover:bg-orange-50 transition-all">Clear All</a>
            </div>
            
            <form method="GET" action="{{ route('staff.applicants.list') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Province</label>
                        <select name="province" id="province-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ $selectedProvince == $province ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Municipality</label>
                        <select name="municipality" id="municipality-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Municipalities</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality }}" {{ $selectedMunicipality == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Barangay</label>
                        <select name="barangay" id="barangay-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">IP Group</label>
                        <select name="ethno" id="ethno-filter" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                            <option value="">All IP Groups</option>
                            @foreach($ethnicities as $ethno)
                                <option value="{{ $ethno->id }}" {{ $selectedEthno == $ethno->id ? 'selected' : '' }}>{{ $ethno->ethnicity }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-6 py-2.5 text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <span>Apply Filters</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="text-3xl font-black text-slate-900">{{ $applicants->total() }}</span>
                <span class="text-sm font-medium text-slate-500">applicants found</span>
            </div>
            <div class="flex items-center gap-4">
            <div class="text-xs font-medium text-slate-500">
                Showing {{ $applicants->firstItem() ?? 0 }}-{{ $applicants->lastItem() ?? 0 }}
                </div>
                @if(isset($masterlistType) && $masterlistType === 'Regular Grantees')
                    <button onclick="window.openGranteesReport();" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        View Report
                    </button>
                @endif
                @if(isset($masterlistType) && $masterlistType === 'Pamana')
                    <button onclick="window.openPamanaReport();" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        View Report
                    </button>
                @endif
                @if(isset($masterlistType) && $masterlistType === 'Regular Waiting')
                    <button onclick="window.openWaitingListReport();" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        View Report
                    </button>
                @endif
            </div>
        </div>

        <!-- Applicants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
            @forelse($applicants as $applicant)
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-200 hover:border-orange-200 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    <!-- Card Header with Gradient -->
                    <div class="relative h-24 bg-gradient-to-br from-orange-500 via-amber-500 to-red-500 p-4">
                        <!-- Status Badge -->
                        @if($applicant->basicInfo && $applicant->basicInfo->type_assist)
                            @php
                                $appStatus = $applicant->basicInfo->application_status ?? 'pending';
                                $isValidated = $appStatus === 'validated';
                                $isRejected = $appStatus === 'rejected';
                            @endphp
                            <div class="absolute top-3 left-3">
                                <span class="{{ $isValidated ? 'bg-emerald-500' : ($isRejected ? 'bg-red-500' : 'bg-amber-500') }} text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg flex items-center gap-1">
                                    @if($isValidated)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Validated
                                    @elseif($isRejected)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Rejected
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pending
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Profile Picture -->
                    <div class="relative -mt-12 flex justify-center px-4">
                        @if($applicant->profile_pic)
                            <img class="h-24 w-24 rounded-2xl object-cover border-4 border-white shadow-xl group-hover:scale-105 transition-transform" src="{{ asset('storage/' . $applicant->profile_pic) }}" alt="Profile">
                        @else
                            <div class="h-24 w-24 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-300 text-slate-600 flex items-center justify-center font-black text-3xl border-4 border-white shadow-xl group-hover:scale-105 transition-transform">
                                {{ substr($applicant->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Card Content -->
                    <div class="p-4 pt-3">
                        <!-- Name -->
                        <div class="text-center mb-3">
                            <h3 class="font-bold text-slate-900 text-base leading-tight group-hover:text-orange-600 transition-colors">
                                {{ $applicant->first_name }} {{ $applicant->last_name }}
                            </h3>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5">ID: {{ $applicant->id }}</p>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-2 mb-3">
                            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-2.5 py-2 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="font-medium truncate">{{ Str::limit($applicant->email, 20) }}</span>
                            </div>
                            @if($applicant->contact_num)
                            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-2.5 py-2 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 12.284 3 6V5z"></path></svg>
                                <span class="font-medium">{{ $applicant->contact_num }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                {{ Str::limit($applicant->basicInfo->fullAddress->address->municipality ?? 'N/A', 12) }}
                            </span>
                            @if($applicant->ethno)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-700">
                                    <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    {{ Str::limit($applicant->ethno->ethnicity, 10) }}
                                </span>
                            @endif
                        </div>

                        <!-- Documents Progress -->
                        @php
                            $documents = $applicant->documents ?? collect();
                            $approvedDocs = $documents->where('status', 'approved')->count();
                            $totalDocs = $documents->count();
                            $percentage = $totalDocs > 0 ? ($approvedDocs / $totalDocs) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Documents</span>
                                <span class="text-xs font-bold text-slate-700">{{ $approvedDocs }}/{{ $totalDocs }}</span>
                            </div>
                            <div class="relative w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="absolute inset-0 
                                    @if($percentage >= 100) bg-gradient-to-r from-emerald-400 to-green-500
                                    @elseif($percentage >= 50) bg-gradient-to-r from-orange-400 to-amber-500
                                    @else bg-gradient-to-r from-slate-300 to-slate-400 @endif
                                    h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('staff.applications.view', $applicant->id) }}" class="w-full flex items-center justify-center gap-2 bg-slate-900 hover:bg-orange-600 text-white rounded-xl px-4 py-2.5 font-bold text-sm shadow-md hover:shadow-lg transition-all group/btn">
                            <span>Review</span>
                            <svg class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-16 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-slate-900 font-bold text-lg mb-2">No applicants found</h3>
                        <p class="text-slate-500 text-sm max-w-md mx-auto mb-4">Try adjusting your filters to see more results.</p>
                        <a href="{{ route('staff.applicants.list') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition-all text-sm">
                            Clear all filters
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($applicants->hasPages())
            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-6 py-4">
                    {{ $applicants->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Grantees Report Modal -->
<div id="granteesReportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white">Grantees Report</h2>
                    <p class="text-sm text-white/90">Excel-style grid view of all grantee applicants</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(isset($masterlistType) && $masterlistType === 'Regular Grantees')
                    <span id="unsavedIndicator" class="hidden text-xs font-semibold text-white/80 bg-white/20 px-3 py-1.5 rounded-lg">
                        Unsaved changes
                    </span>
                @endif
                <button onclick="window.closeGranteesReport()" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            </div>
        </div>

        <!-- Modal Body - Excel-like Grid -->
        <div class="flex-1 overflow-auto p-6 bg-slate-50">
            <div id="reportLoading" class="flex items-center justify-center py-20">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
                    <p class="text-slate-600 font-medium">Loading grantees data...</p>
                </div>
            </div>
            <div id="reportContent" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" style="min-width: 2400px;">
                            <thead class="bg-gradient-to-r from-emerald-700 to-green-800 sticky top-0 z-10">
                                <!-- First row with main headers -->
                                <tr>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Reference No.</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Contact Number/Email</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">BATCH</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NO</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">NAME OF SCHOOL</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="5" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">YEAR</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GRANTS</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">REMARKS/STATUS</th>
                                </tr>
                                <!-- Second row with sub-headers -->
                                <tr>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">3rd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">4th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">5th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st Sem</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd Sem</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody" class="bg-white">
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm text-slate-600">
                    <span id="reportCount" class="font-medium"></span>
                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="window.exportGranteesExcel()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export to Excel
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pamana Report Modal -->
<div id="pamanaReportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white">Pamana Report</h2>
                    <p class="text-sm text-white/90">Grid view of Pamana scholarship applicants</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.closePamanaReport()" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            </div>
        </div>

        <!-- Modal Body - Excel-like Grid -->
        <div class="flex-1 overflow-auto p-6 bg-slate-50">
            <div id="pamanaReportLoading" class="flex items-center justify-center py-20">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-600 border-t-transparent mb-4"></div>
                    <p class="text-slate-600 font-medium">Loading Pamana data...</p>
                </div>
            </div>
            <div id="pamanaReportContent" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" style="min-width: 2400px;">
                            <thead class="bg-gradient-to-r from-emerald-700 to-teal-800 sticky top-0 z-10">
                                <!-- First row with main headers -->
                                <tr>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Reference No.</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Contact Number/Email</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">BATCH</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NO</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">NAME OF SCHOOL</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="5" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">YEAR</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GRANTS</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">REMARKS/STATUS</th>
                                </tr>
                                <!-- Second row with sub-headers -->
                                <tr>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">3rd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">4th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">5th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st Sem</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd Sem</th>
                                </tr>
                            </thead>
                            <tbody id="pamanaReportTableBody" class="bg-white">
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm text-slate-600">
                    <span id="pamanaReportCount" class="font-medium"></span>
                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="window.exportPamanaToCSV()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export to CSV
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Waiting List Report Modal -->
<div id="waitingListReportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-purple-600 to-pink-600 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white">Master List of Wait Listed Applicants (MLWLA)</h2>
                    <p class="text-sm text-white/90">Educational Assistance Program / Merit-based Scholarship Program</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="waitingUnsavedIndicator" class="hidden text-xs font-semibold text-white/80 bg-white/20 px-3 py-1.5 rounded-lg">
                    Unsaved changes
                </span>
                <button onclick="window.closeWaitingListReport()" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Modal Body - Excel-like Grid -->
        <div class="flex-1 overflow-auto p-6 bg-slate-50">
            <div id="waitingReportLoading" class="flex items-center justify-center py-20">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-purple-600 border-t-transparent mb-4"></div>
                    <p class="text-slate-600 font-medium">Loading waiting list data...</p>
                </div>
            </div>
            <div id="waitingReportContent" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" style="min-width: 2800px;">
                            <thead class="bg-gradient-to-r from-purple-700 to-pink-800 sticky top-0 z-10">
                                <!-- First row with main headers -->
                                <tr>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Reference No.</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Contact Number/Email</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">BATCH</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NO</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">SCHOOL</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="5" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">YEAR</th>
                                    <th colspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">GRANTS</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">RSSC's Score</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Rank</th>
                                </tr>
                                <!-- Second row with sub-headers -->
                                <tr>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">3rd</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">4th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">5th</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">1st Sem</th>
                                    <th class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">2nd Sem</th>
                                </tr>
                            </thead>
                            <tbody id="waitingReportTableBody" class="bg-white">
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm text-slate-600">
                    <span id="waitingReportCount" class="font-medium"></span>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <button onclick="window.checkAllWaiting1stSem()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Check 1st Sem
                            </button>
                            <button onclick="window.checkAllWaiting2ndSem()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Check 2nd Sem
                            </button>
                        </div>
                        <button onclick="window.saveWaitingGrants()" id="saveWaitingGrantsBtn" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Changes
                        </button>
                        <button onclick="window.exportWaitingToCSV()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export to CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceFilter = document.getElementById('province-filter');
        const municipalityFilter = document.getElementById('municipality-filter');
        const barangayFilter = document.getElementById('barangay-filter');

        provinceFilter.addEventListener('change', function() {
            const province = this.value;
            if (province) {
                fetch(`/address/municipalities?province=${encodeURIComponent(province)}`)
                    .then(response => response.json())
                    .then(municipalities => {
                        municipalityFilter.innerHTML = '<option value="">All Municipalities</option>';
                        municipalities.forEach(municipality => {
                            const option = document.createElement('option');
                            option.value = municipality;
                            option.textContent = municipality;
                            municipalityFilter.appendChild(option);
                        });
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                    });
            }
        });

        municipalityFilter.addEventListener('change', function() {
            const municipality = this.value;
            if (municipality) {
                fetch(`/address/barangays?municipality=${encodeURIComponent(municipality)}`)
                    .then(response => response.json())
                    .then(barangays => {
                        barangayFilter.innerHTML = '<option value="">All Barangays</option>';
                        barangays.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay;
                            option.textContent = barangay;
                            barangayFilter.appendChild(option);
                        });
                    });
            }
        });
    });

    // markAsChanged and related variables are now defined in the initial script block above

    window.checkAll1stSem = function() {
        const checkboxes = document.querySelectorAll('.grant-checkbox[data-sem="1st"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        window.markAsChanged();
    };

    window.checkAll2ndSem = function() {
        const checkboxes = document.querySelectorAll('.grant-checkbox[data-sem="2nd"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        window.markAsChanged();
    };

    window.saveGrants = function() {
        const checkboxes = document.querySelectorAll('.grant-checkbox');
        const grants = [];
        const grantMap = {};

        // Collect all grant states
        checkboxes.forEach(checkbox => {
            const userId = checkbox.getAttribute('data-user-id');
            const sem = checkbox.getAttribute('data-sem');
            
            if (!grantMap[userId]) {
                grantMap[userId] = {
                    user_id: parseInt(userId),
                    grant_1st_sem: false,
                    grant_2nd_sem: false
                };
            }
            
            if (sem === '1st') {
                grantMap[userId].grant_1st_sem = checkbox.checked;
            } else if (sem === '2nd') {
                grantMap[userId].grant_2nd_sem = checkbox.checked;
            }
        });

        // Convert map to array
        grants.push(...Object.values(grantMap));

        const saveBtn = document.getElementById('saveGrantsBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';

        fetch('{{ route("staff.grantees.update-grants") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ grants: grants })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                hasUnsavedChanges = false;
                saveBtn.innerHTML = originalText;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                saveBtn.disabled = true;
                
                // Hide unsaved indicator
                const unsavedIndicator = document.getElementById('unsavedIndicator');
                if (unsavedIndicator) {
                    unsavedIndicator.classList.add('hidden');
                }
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
                successMsg.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>${data.message}</span>
                `;
                document.body.appendChild(successMsg);
                setTimeout(() => {
                    successMsg.remove();
                }, 3000);
                } else {
                alert('Error saving grants: ' + (data.message || 'Unknown error'));
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            alert('Error saving grants. Please try again.');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            });
    }

    // renderReportTable is now defined in the initial script block above

    window.exportGranteesExcel = function() {
        if (!window.granteesData || window.granteesData.length === 0) {
            alert('No data to export');
            return;
        }

        // Sync current checkbox states before exporting
        const checkboxes = document.querySelectorAll('.grant-checkbox');
        checkboxes.forEach(checkbox => {
            const userId = parseInt(checkbox.getAttribute('data-user-id'));
            const sem = checkbox.getAttribute('data-sem');
            const grantee = window.granteesData.find(g => g.user_id === userId);
            if (grantee) {
                if (sem === '1st') {
                    grantee.grant_1st_sem = checkbox.checked;
                } else if (sem === '2nd') {
                    grantee.grant_2nd_sem = checkbox.checked;
                }
            }
        });

        // Simple HTML escape to keep cells clean
        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        // Colors inspired by the provided form
        const headerBg = '#6b8c2f';
        const headerText = '#ffffff';
        const border = '1px solid #4a5d1d';

        // Build table head (two rows with colspans to mimic the layout)
        const head = `
            <tr>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">Province, Municipality, Barangay, AD Reference No.</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">Contact Number/Email</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">BATCH</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">NO</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">NAME</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">AGE</th>
                <th colspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">GENDER</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">IP GROUP</th>
                <th colspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">NAME OF SCHOOL</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">COURSE</th>
                <th colspan="5" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">YEAR</th>
                <th colspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">GRANTS</th>
                <th rowspan="2" style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">REMARKS/STATUS</th>
            </tr>
            <tr>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">F</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">M</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">Private</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">Public</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">1st</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">2nd</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">3rd</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">4th</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">5th</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">1st Sem</th>
                <th style="background:${headerBg};color:${headerText};border:${border};padding:6px;font-size:11px;font-weight:bold;text-align:center;">2nd Sem</th>
            </tr>
        `;

        const bodyRows = window.granteesData.map(grantee => {
            const addressLine = [
                grantee.province || '',
                grantee.municipality || '',
                grantee.barangay || '',
                grantee.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = grantee.is_female || false;
            const isMale = grantee.is_male || false;

            const schoolType = (grantee.school_type || grantee.school1_type || '').toLowerCase();
            const schoolName = grantee.school_name || grantee.school1_name || grantee.school || '';
            const privateSchool = (schoolType === 'private' || grantee.is_private) ? schoolName : '';
            const publicSchool = (schoolType === 'public' || grantee.is_public) ? schoolName : '';

            const yearVal = (flag, label) => flag ? '✓' : '';
            const remarksStatus = window.normalizeRemarksStatus(grantee.remarks || '');

            return `
                <tr>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(addressLine)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(grantee.contact_email || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${escapeHtml(grantee.batch || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${escapeHtml(grantee.no || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(grantee.name || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${escapeHtml(grantee.age || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${isFemale ? '✓' : ''}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${isMale ? '✓' : ''}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(grantee.ethnicity || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(privateSchool)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(publicSchool)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(grantee.course || '')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_1st, '1st')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_2nd, '2nd')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_3rd, '3rd')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_4th, '4th')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_5th, '5th')}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">10,000</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">10,000</td>
                    <td style="border:${border};padding:6px;font-size:11px;">${escapeHtml(remarksStatus)}</td>
                </tr>
            `;
        }).join('');

        const html = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                  xmlns:x="urn:schemas-microsoft-com:office:excel"
                  xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { mso-number-format:"\\@"; } /* keep text formatting */
                </style>
            </head>
            <body>
                <table>
                    <thead>${head}</thead>
                    <tbody>${bodyRows}</tbody>
                </table>
            </body>
            </html>
        `;

        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        const dateStr = new Date().toISOString().split('T')[0];
        link.download = `Grantees_Report_${dateStr}.xls`;
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    window.exportPamanaToCSV = function() {
        if (!window.pamanaData || window.pamanaData.length === 0) {
            alert('No data to export');
            return;
        }

        const headers = [
            'Province, Municipality, Barangay, AD Reference No.',
            'Contact Number/Email',
            'BATCH',
            'NO',
            'NAME',
            'AGE',
            'GENDER',
            'IP GROUP',
            'SCHOOL (Private)',
            'SCHOOL (Public)',
            'COURSE',
            'YEAR',
            'GRANTS (1st Sem)',
            'GRANTS (2nd Sem)',
            'REMARKS/STATUS'
        ];

        const rows = window.pamanaData.map(applicant => {
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            let genderValue = '';
            if (isFemale && isMale) {
                genderValue = 'F, M';
            } else if (isFemale) {
                genderValue = 'F';
            } else if (isMale) {
                genderValue = 'M';
            }

            const is1st = applicant.is_1st || false;
            const is2nd = applicant.is_2nd || false;
            const is3rd = applicant.is_3rd || false;
            const is4th = applicant.is_4th || false;
            const is5th = applicant.is_5th || false;
            const yearLevels = [];
            if (is1st) yearLevels.push('1st');
            if (is2nd) yearLevels.push('2nd');
            if (is3rd) yearLevels.push('3rd');
            if (is4th) yearLevels.push('4th');
            if (is5th) yearLevels.push('5th');
            const yearValue = yearLevels.join(', ');

            const grant1stSem = '10,000';
            const grant2ndSem = '10,000';

            const schoolType = (applicant.school_type || applicant.school1_type || '').toLowerCase();
            const schoolName = applicant.school_name || applicant.school1_name || applicant.school || '';

            return [
                addressLine,
                applicant.contact_email || '',
                applicant.batch || '',
                applicant.no || '',
                applicant.name || '',
                applicant.age || '',
                genderValue,
                applicant.ethnicity || '',
                (schoolType === 'private' || applicant.is_private) ? schoolName : '',
                (schoolType === 'public' || applicant.is_public) ? schoolName : '',
                applicant.course || '',
                yearValue,
                grant1stSem,
                grant2ndSem,
                applicant.remarks || ''
            ];
        });

        function escapeCSV(value) {
            if (value === null || value === undefined) return '';
            const stringValue = String(value);
            return `"${stringValue.replace(/"/g, '""')}"`;
        }

        const csvContent = [
            headers.map(escapeCSV).join(','),
            ...rows.map(row => row.map(escapeCSV).join(','))
        ].join('\r\n');

        const BOM = '\uFEFF';
        const csvWithBOM = BOM + csvContent;

        const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        const dateStr = new Date().toISOString().split('T')[0];
        link.setAttribute('download', `Pamana_Report_${dateStr}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Close modal on outside click
    document.getElementById('granteesReportModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.closeGranteesReport();
        }
    });

    document.getElementById('pamanaReportModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.closePamanaReport();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeGranteesReport();
            window.closePamanaReport();
            window.closeWaitingListReport();
        }
    });

    // ========== WAITING LIST REPORT FUNCTIONS ==========
    window.waitingListData = [];
    window.hasUnsavedWaitingChanges = false;

    window.markWaitingAsChanged = function() {
        window.hasUnsavedWaitingChanges = true;
        const saveBtn = document.getElementById('saveWaitingGrantsBtn');
        const unsavedIndicator = document.getElementById('waitingUnsavedIndicator');
        if (saveBtn) {
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            saveBtn.disabled = false;
        }
        if (unsavedIndicator) {
            unsavedIndicator.classList.remove('hidden');
        }
    };

    window.checkAllWaiting1stSem = function() {
        const checkboxes = document.querySelectorAll('.waiting-grant-checkbox[data-sem="1st"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        window.markWaitingAsChanged();
    };

    window.checkAllWaiting2ndSem = function() {
        const checkboxes = document.querySelectorAll('.waiting-grant-checkbox[data-sem="2nd"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        window.markWaitingAsChanged();
    };


    window.renderWaitingReportTable = function(waitingList) {
        const tableBody = document.getElementById('waitingReportTableBody');
        const reportCount = document.getElementById('waitingReportCount');
        
        reportCount.textContent = `Total Wait Listed Applicants: ${waitingList.length}`;

        if (waitingList.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No wait listed applicants found
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = waitingList.map((applicant, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            
            // Format address and AD Reference
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            // Gender checkboxes
            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            
            // School type checkboxes
            const isPrivate = applicant.is_private || false;
            const isPublic = applicant.is_public || false;
            
            // Year level checkboxes
            const is1st = applicant.is_1st || false;
            const is2nd = applicant.is_2nd || false;
            const is3rd = applicant.is_3rd || false;
            const is4th = applicant.is_4th || false;
            const is5th = applicant.is_5th || false;
            
            // RSSC Score - use manual score if available, otherwise calculated score
            const manualRsscScore = applicant.manual_rssc_score !== null && applicant.manual_rssc_score !== undefined ? applicant.manual_rssc_score : null;
            const calculatedRsscScore = applicant.rssc_score || applicant.priority_score || 0;
            const rsscScore = manualRsscScore !== null ? manualRsscScore : calculatedRsscScore;
            
            // Rank
            const rank = applicant.rank || applicant.priority_rank || '';
            
            return `
                <tr class="${rowClass} hover:bg-purple-50 transition-colors">
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${addressLine}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.contact_email || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${applicant.batch || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-medium">${applicant.no || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.name || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${applicant.age || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.ethnicity || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isPrivate ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${isPublic ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.course || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">
                        <label class="flex items-center justify-center cursor-pointer">
                            <input type="checkbox" 
                                   class="waiting-grant-checkbox w-5 h-5 text-purple-600 border-slate-300 rounded focus:ring-purple-500" 
                                   data-user-id="${applicant.user_id}"
                                   data-sem="1st"
                                   ${applicant.grant_1st_sem ? 'checked' : ''}
                                   onchange="window.markWaitingAsChanged()">
                        </label>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">
                        <label class="flex items-center justify-center cursor-pointer">
                            <input type="checkbox" 
                                   class="waiting-grant-checkbox w-5 h-5 text-purple-600 border-slate-300 rounded focus:ring-purple-500" 
                                   data-user-id="${applicant.user_id}"
                                   data-sem="2nd"
                                   ${applicant.grant_2nd_sem ? 'checked' : ''}
                                   onchange="window.markWaitingAsChanged()">
                        </label>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               max="100"
                               class="waiting-rssc-score w-full px-2 py-1 text-xs text-center font-semibold border border-slate-300 rounded focus:border-purple-500 focus:ring-1 focus:ring-purple-500" 
                               data-user-id="${applicant.user_id}"
                               value="${rsscScore.toFixed(2)}"
                               onchange="window.markWaitingAsChanged()">
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-bold">${rank ? '#' + rank : ''}</td>
                </tr>
            `;
        }).join('');
    }

    window.saveWaitingGrants = function() {
        const checkboxes = document.querySelectorAll('.waiting-grant-checkbox');
        const rsscInputs = document.querySelectorAll('.waiting-rssc-score');
        const grants = [];
        const grantMap = {};

        // Collect all grant states
        checkboxes.forEach(checkbox => {
            const userId = checkbox.getAttribute('data-user-id');
            const sem = checkbox.getAttribute('data-sem');
            
            if (!grantMap[userId]) {
                grantMap[userId] = {
                    user_id: parseInt(userId),
                    grant_1st_sem: false,
                    grant_2nd_sem: false,
                    rssc_score: null
                };
            }
            
            if (sem === '1st') {
                grantMap[userId].grant_1st_sem = checkbox.checked;
            } else if (sem === '2nd') {
                grantMap[userId].grant_2nd_sem = checkbox.checked;
            }
        });

        // Collect all RSSC scores
        rsscInputs.forEach(input => {
            const userId = parseInt(input.getAttribute('data-user-id'));
            const rsscValue = parseFloat(input.value);
            
            if (!grantMap[userId]) {
                grantMap[userId] = {
                    user_id: userId,
                    grant_1st_sem: false,
                    grant_2nd_sem: false,
                    rssc_score: null
                };
            }
            
            grantMap[userId].rssc_score = isNaN(rsscValue) ? null : rsscValue;
        });

        // Convert map to array
        grants.push(...Object.values(grantMap));

        const saveBtn = document.getElementById('saveWaitingGrantsBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';

        fetch('{{ route("staff.waiting-list.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ applicants: grants })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.hasUnsavedWaitingChanges = false;
                saveBtn.innerHTML = originalText;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                saveBtn.disabled = true;
                
                // Hide unsaved indicator
                const unsavedIndicator = document.getElementById('waitingUnsavedIndicator');
                if (unsavedIndicator) {
                    unsavedIndicator.classList.add('hidden');
                }
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
                successMsg.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>${data.message}</span>
                `;
                document.body.appendChild(successMsg);
                setTimeout(() => {
                    successMsg.remove();
                }, 3000);
            } else {
                alert('Error saving grants: ' + (data.message || 'Unknown error'));
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving grants. Please try again.');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }

    window.exportWaitingToCSV = function() {
        if (window.waitingListData.length === 0) {
            alert('No data to export');
            return;
        }

        // Update waitingListData with current checkbox states and RSSC scores before exporting
        const checkboxes = document.querySelectorAll('.waiting-grant-checkbox');
        const rsscInputs = document.querySelectorAll('.waiting-rssc-score');
        
        checkboxes.forEach(checkbox => {
            const userId = parseInt(checkbox.getAttribute('data-user-id'));
            const sem = checkbox.getAttribute('data-sem');
            const applicant = waitingListData.find(a => a.user_id === userId);
            if (applicant) {
                if (sem === '1st') {
                    applicant.grant_1st_sem = checkbox.checked;
                } else if (sem === '2nd') {
                    applicant.grant_2nd_sem = checkbox.checked;
                }
            }
        });
        
        rsscInputs.forEach(input => {
            const userId = parseInt(input.getAttribute('data-user-id'));
            const rsscValue = parseFloat(input.value);
            const applicant = waitingListData.find(a => a.user_id === userId);
            if (applicant) {
                applicant.manual_rssc_score = isNaN(rsscValue) ? null : rsscValue;
            }
        });

        // CSV Headers matching the grid table view structure
        const headers = [
            'Province, Municipality, Barangay, AD Reference No.',
            'Contact Number/Email',
            'BATCH',
            'NO',
            'NAME',
            'AGE',
            'GENDER',
            'IP GROUP',
            'SCHOOL (Private)',
            'SCHOOL (Public)',
            'COURSE',
            'YEAR',
            'GRANTS (1st Sem)',
            'GRANTS (2nd Sem)',
            'RSSC\'s Score',
            'Rank'
        ];

        // CSV Rows
        const rows = window.waitingListData.map(applicant => {
            // Format address and AD Reference
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');
            
            // Gender - combine F and M into single value
            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            let genderValue = '';
            if (isFemale && isMale) {
                genderValue = 'F, M';
            } else if (isFemale) {
                genderValue = 'F';
            } else if (isMale) {
                genderValue = 'M';
            }
            
            // Year - combine all year levels into single value
            const is1st = applicant.is_1st || false;
            const is2nd = applicant.is_2nd || false;
            const is3rd = applicant.is_3rd || false;
            const is4th = applicant.is_4th || false;
            const is5th = applicant.is_5th || false;
            const yearLevels = [];
            if (is1st) yearLevels.push('1st');
            if (is2nd) yearLevels.push('2nd');
            if (is3rd) yearLevels.push('3rd');
            if (is4th) yearLevels.push('4th');
            if (is5th) yearLevels.push('5th');
            const yearValue = yearLevels.join(', ');
            
            // Get current checkbox states for grants
            const grant1stSem = applicant.grant_1st_sem ? '✓' : '';
            const grant2ndSem = applicant.grant_2nd_sem ? '✓' : '';
            
            // RSSC Score - use manual score if available, otherwise calculated score
            const rsscScore = applicant.manual_rssc_score !== null && applicant.manual_rssc_score !== undefined 
                ? applicant.manual_rssc_score 
                : (applicant.rssc_score || applicant.priority_score || 0);
            
            // Rank
            const rank = applicant.rank || applicant.priority_rank || '';
            
            return [
                addressLine,
                applicant.contact_email || '',
                applicant.batch || '',
                applicant.no || '',
                applicant.name || '',
                applicant.age || '',
                genderValue,
                applicant.ethnicity || '',
                applicant.is_private ? '✓' : '',
                applicant.is_public ? '✓' : '',
                applicant.course || '',
                yearValue,
                grant1stSem,
                grant2ndSem,
                rsscScore.toFixed(2),
                rank ? '#' + rank : ''
            ];
        });

        // Escape CSV values
        function escapeCSV(value) {
            if (value === null || value === undefined) return '';
            const stringValue = String(value);
            return `"${stringValue.replace(/"/g, '""')}"`;
        }

        // Create CSV content with UTF-8 BOM for Excel compatibility
        const csvContent = [
            headers.map(escapeCSV).join(','),
            ...rows.map(row => row.map(escapeCSV).join(','))
        ].join('\r\n');

        // Add UTF-8 BOM for Excel to recognize special characters correctly
        const BOM = '\uFEFF';
        const csvWithBOM = BOM + csvContent;

        // Create blob and download
        const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        const dateStr = new Date().toISOString().split('T')[0];
        link.setAttribute('download', `Waiting_List_Report_${dateStr}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Close waiting list modal on outside click
    document.getElementById('waitingListReportModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            window.closeWaitingListReport();
        }
    });

    // Attach event listeners to buttons (works even if DOMContentLoaded already fired)
    function attachButtonListeners() {
        // Waiting list report button
        const openWaitingBtn = document.getElementById('openWaitingListReportBtn');
        if (openWaitingBtn && !openWaitingBtn.dataset.listenerAttached) {
            openWaitingBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.openWaitingListReport) {
                    window.openWaitingListReport();
                }
            });
            openWaitingBtn.dataset.listenerAttached = 'true';
        }
        
        // Grantees report button
        const openGranteesBtn = document.getElementById('openGranteesReportBtn');
        if (openGranteesBtn && !openGranteesBtn.dataset.listenerAttached) {
            openGranteesBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.openGranteesReport) {
                    window.openGranteesReport();
                }
            });
            openGranteesBtn.dataset.listenerAttached = 'true';
        }
    }

    // Try to attach listeners immediately if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachButtonListeners);
    } else {
        // DOM is already loaded
        attachButtonListeners();
    }
    
    // Also try after a short delay to ensure everything is ready
    setTimeout(attachButtonListeners, 100);

</script>
@endpush
@endsection
