@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 p-6 md:p-8 font-sans">
    <div class="w-full max-w-none mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-600 shadow-lg shadow-orange-200/50">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Reports</h1>
                        <p class="text-slate-500 text-sm mt-0.5">View and export scholarship masterlist reports</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6 min-w-0">
            <section class="space-y-6 min-w-0">
                <!-- Report Panel -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden min-w-0">
                    <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 id="reportTitle" class="text-lg font-black text-slate-900">Report</h2>
                            <p id="reportSubtitle" class="text-sm text-slate-500">Select a report type to begin.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="exportBtn" type="button" class="hidden px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span id="exportBtnText">Export</span>
                            </button>

                            <button id="saveWaitingBtn" type="button" class="hidden px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <div id="reportLoading" class="hidden flex items-center justify-center py-16 bg-slate-50">
                        <div class="text-center">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-orange-600 border-t-transparent mb-3"></div>
                            <p class="text-slate-600 font-medium">Loading report data...</p>
                        </div>
                    </div>

                    <div id="reportMeta" class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span id="reportCount" class="text-sm font-semibold text-slate-700"></span>
                        <span id="waitingUnsavedIndicator" class="hidden text-xs font-semibold text-white bg-orange-500 px-3 py-1.5 rounded-lg">Unsaved changes</span>
                    </div>

                    <div id="reportSummary" class="hidden px-6 py-6 bg-white border-b border-slate-200">
                        <div id="summaryContent" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Summary items will be injected here -->
                        </div>
                    </div>

                    <div class="overflow-x-auto max-w-full">
                        <!-- Shared header layout; we keep 3 tables and toggle visibility -->
                        <table id="granteesTable" class="hidden w-full border-collapse" style="min-width: 2400px;">
                            <thead class="bg-gradient-to-r from-emerald-700 to-green-800 sticky top-0 z-10">
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
                            <tbody id="granteesTableBody" class="bg-white"></tbody>
                        </table>

                        <table id="pamanaTable" class="hidden w-full border-collapse" style="min-width: 2400px;">
                            <thead class="bg-gradient-to-r from-emerald-700 to-teal-800 sticky top-0 z-10">
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
                            <tbody id="pamanaTableBody" class="bg-white"></tbody>
                        </table>

                        <table id="waitingTable" class="hidden w-full border-collapse" style="min-width: 2800px;">
                            <thead class="bg-gradient-to-r from-purple-700 to-pink-800 sticky top-0 z-10">
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
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">RSSC's Score</th>
                                    <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Rank</th>
                                </tr>
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
                                </tr>
                            </thead>
                            <tbody id="waitingTableBody" class="bg-white"></tbody>
                        </table>

                        <!-- Disqualified Applicants Report Table -->
                        <table id="disqualifiedTable" class="hidden w-full border-collapse" style="min-width: 2000px;">
                            <thead class="bg-yellow-300 sticky top-0 z-10">
                                <tr>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Number/Reference No.</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Contact Email</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">SCHOOL</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="3" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Reasons of Disqualification</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Not IP</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Exceeded Required Income</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Incomplete Documents</th>
                                </tr>
                            </thead>
                            <tbody id="disqualifiedTableBody" class="bg-white"></tbody>
                        </table>

                        <!-- Replacements Report Table -->
                        <table id="replacementsTable" class="hidden w-full border-collapse" style="min-width: 2600px;">
                            <thead class="bg-yellow-300 sticky top-0 z-10">
                                <tr>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Province, Municipality, Barangay, AD Reference No.</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Contact Number/Email</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">BATCH</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">NO</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">NAME</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">AGE</th>
                                    <th colspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">GENDER</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">IP GROUP</th>
                                    <th colspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">SCHOOL</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">COURSE</th>
                                    <th colspan="5" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">YEAR</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Name of Replaced Grantee/Awardee</th>
                                    <th rowspan="2" class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap align-middle">Reason/s of Replacement</th>
                                </tr>
                                <tr>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">F</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">M</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Private</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">Public</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">1st</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">2nd</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">3rd</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">4th</th>
                                    <th class="border border-black px-2 py-2 text-center text-xs font-bold text-black uppercase tracking-wider whitespace-nowrap">5th</th>
                                </tr>
                            </thead>
                            <tbody id="replacementsTableBody" class="bg-white"></tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===== helpers =====
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

    const state = {
        active: @json($activeTab),
        grantees: [],
        pamana: [],
        waiting: [],
        waitingDirty: false,
    };

    function setLoading(isLoading) {
        const el = document.getElementById('reportLoading');
        if (!el) return;
        el.classList.toggle('hidden', !isLoading);
    }

    function updateReportSummary(stats) {
        const summaryDiv = document.getElementById('reportSummary');
        const summaryContent = document.getElementById('summaryContent');
        if (!summaryDiv || !summaryContent) return;

        if (!stats || Object.keys(stats).length === 0) {
            summaryDiv.classList.add('hidden');
            return;
        }

        summaryDiv.classList.remove('hidden');
        summaryContent.innerHTML = Object.entries(stats).map(([label, value]) => `
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 shadow-sm transition-all hover:shadow-md">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">${label}</p>
                <p class="text-2xl font-black text-slate-900">${value}</p>
            </div>
        `).join('');
    }

    function setActiveTab(tab) {
        state.active = tab;

        // tables
        document.getElementById('granteesTable')?.classList.toggle('hidden', tab !== 'grantees');
        document.getElementById('pamanaTable')?.classList.toggle('hidden', tab !== 'pamana');
        document.getElementById('waitingTable')?.classList.toggle('hidden', tab !== 'waiting');
        document.getElementById('disqualifiedTable')?.classList.toggle('hidden', tab !== 'disqualified');
        document.getElementById('replacementsTable')?.classList.toggle('hidden', tab !== 'replacements');

        // meta
        const exportBtn = document.getElementById('exportBtn');
        const exportBtnText = document.getElementById('exportBtnText');
        const saveWaitingBtn = document.getElementById('saveWaitingBtn');
        const unsavedIndicator = document.getElementById('waitingUnsavedIndicator');

        if (exportBtn) exportBtn.classList.remove('hidden');
        if (saveWaitingBtn) saveWaitingBtn.classList.toggle('hidden', tab !== 'waiting');
        if (unsavedIndicator) unsavedIndicator.classList.add('hidden');
        state.waitingDirty = false;

        if (tab === 'grantees') {
            document.getElementById('reportTitle').textContent = 'Regular Grantees Report';
            document.getElementById('reportSubtitle').textContent = 'Excel-style grid view of all grantee applicants';
            if (exportBtnText) exportBtnText.textContent = 'Export to Excel';
        } else if (tab === 'pamana') {
            document.getElementById('reportTitle').textContent = 'Pamana Report';
            document.getElementById('reportSubtitle').textContent = 'Grid view of Pamana scholarship applicants';
            if (exportBtnText) exportBtnText.textContent = 'Export to CSV';
        } else {
            if (tab === 'waiting') {
                document.getElementById('reportTitle').textContent = 'Master List of Wait Listed Applicants (MLWLA)';
                document.getElementById('reportSubtitle').textContent = 'Educational Assistance Program / Merit-based Scholarship Program';
                if (exportBtnText) exportBtnText.textContent = 'Export to CSV';
            } else if (tab === 'disqualified') {
                document.getElementById('reportTitle').textContent = 'MASTER LIST OF DISQUALIFIED APPLICANTS SY';
                document.getElementById('reportSubtitle').textContent = 'Educational Assistance Program/Merit-based Scholarship Program';
                if (exportBtnText) exportBtnText.textContent = 'Export to Excel';
            } else {
                document.getElementById('reportTitle').textContent = 'Master List of Replacements';
                document.getElementById('reportSubtitle').textContent = 'Replacement awardees list';
                if (exportBtnText) exportBtnText.textContent = 'Export to Excel';
            }
        }

        // count reset
        const countEl = document.getElementById('reportCount');
        if (countEl) countEl.textContent = '';
    }

    // ===== renderers =====
    function renderGranteesTable(rows) {
        const tableBody = document.getElementById('granteesTableBody');
        const countEl = document.getElementById('reportCount');
        if (!tableBody) return;
        if (countEl) countEl.textContent = `Total Grantees: ${rows.length}`;

        if (!rows.length) {
            updateReportSummary({});
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No grantees found
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate stats
        const stats = {
            'Total Grantees': rows.length,
            'Female': rows.filter(r => r.is_female).length,
            'Male': rows.filter(r => r.is_male).length,
            'Private School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'private' || r.is_private).length,
            'Public School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'public' || r.is_public).length
        };
        updateReportSummary(stats);

        tableBody.innerHTML = rows.map((grantee, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';

            const addressLine = [
                grantee.province || '',
                grantee.municipality || '',
                grantee.barangay || '',
                grantee.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = grantee.is_female || false;
            const isMale = grantee.is_male || false;

            const isPrivate = grantee.is_private || false;
            const isPublic = grantee.is_public || false;
            const schoolType = (grantee.school_type || grantee.school1_type || '').toLowerCase();
            const schoolName = grantee.school_name || grantee.school1_name || grantee.school || '';

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
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}" readonly>
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
    }

    function renderPamanaTable(rows) {
        const tableBody = document.getElementById('pamanaTableBody');
        const countEl = document.getElementById('reportCount');
        if (!tableBody) return;
        if (countEl) countEl.textContent = `Total Pamana Applicants: ${rows.length}`;

        if (!rows.length) {
            updateReportSummary({});
            tableBody.innerHTML = `
                <tr>
                    <td colspan="20" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No Pamana applicants found
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate stats
        const stats = {
            'Total Pamana': rows.length,
            'Female': rows.filter(r => r.is_female).length,
            'Male': rows.filter(r => r.is_male).length,
            'Private School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'private' || r.is_private).length,
            'Public School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'public' || r.is_public).length
        };
        updateReportSummary(stats);

        tableBody.innerHTML = rows.map((applicant, index) => {
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

            const remarksStatus = window.normalizeRemarksStatus(applicant.remarks || '');

            return `
                <tr class="${rowClass} hover:bg-emerald-50 transition-colors">
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
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.course || ''}</td>
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
    }

    // Waiting list (keeps editable checkboxes + score)
    function markWaitingAsChanged() {
        state.waitingDirty = true;
        const btn = document.getElementById('saveWaitingBtn');
        const indicator = document.getElementById('waitingUnsavedIndicator');
        if (btn) btn.disabled = false;
        if (indicator) indicator.classList.remove('hidden');
    }

    function renderWaitingTable(rows) {
        const tableBody = document.getElementById('waitingTableBody');
        const countEl = document.getElementById('reportCount');
        if (!tableBody) return;
        if (countEl) countEl.textContent = `Total Wait Listed Applicants: ${rows.length}`;

        if (!rows.length) {
            updateReportSummary({});
            tableBody.innerHTML = `
                <tr>
                    <td colspan="19" class="border border-slate-300 px-4 py-8 text-center text-slate-500">
                        No wait listed applicants found
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate stats
        const stats = {
            'Total Waiting': rows.length,
            'Female': rows.filter(r => r.is_female).length,
            'Male': rows.filter(r => r.is_male).length,
            'Private School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'private' || r.is_private).length,
            'Public School': rows.filter(r => (r.school_type || r.school1_type || '').toLowerCase() === 'public' || r.is_public).length
        };
        updateReportSummary(stats);

        tableBody.innerHTML = rows.map((applicant, index) => {
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

            const manualRsscScore = applicant.manual_rssc_score !== null && applicant.manual_rssc_score !== undefined ? applicant.manual_rssc_score : null;
            const calculatedRsscScore = applicant.rssc_score || applicant.priority_score || 0;
            const rsscScore = manualRsscScore !== null ? manualRsscScore : calculatedRsscScore;

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
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-slate-50"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700">${applicant.course || ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center">
                        <input type="number"
                               step="0.01"
                               min="0"
                               max="100"
                               class="waiting-rssc-score w-full px-2 py-1 text-xs text-center font-semibold border border-slate-300 rounded focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                               data-user-id="${applicant.user_id}"
                               value="${Number(rsscScore).toFixed(2)}"
                               onchange="window.markWaitingAsChanged()">
                    </td>
                    <td class="border border-slate-300 px-2 py-2 text-xs text-slate-700 text-center font-bold">${rank ? '#' + rank : ''}</td>
                </tr>
            `;
        }).join('');
    }

    function renderDisqualifiedTable(rows) {
        const tableBody = document.getElementById('disqualifiedTableBody');
        const countEl = document.getElementById('reportCount');
        if (!tableBody) return;
        if (countEl) countEl.textContent = `Total Disqualified Applicants: ${rows.length}`;

        if (!rows.length) {
            updateReportSummary({});
            tableBody.innerHTML = `
                <tr>
                    <td colspan="13" class="border border-black px-4 py-8 text-center text-slate-600">
                        No disqualified applicants found
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate stats
        const stats = {
            'Total Disqualified': rows.length,
            'Not IP': rows.filter(r => r.disqualification_not_ip || r.not_ip).length,
            'Exceeded Income': rows.filter(r => r.disqualification_exceeded_income || r.exceeded_income).length,
            'Incomplete Docs': rows.filter(r => r.disqualification_incomplete_docs || r.incomplete_docs).length
        };
        updateReportSummary(stats);

        tableBody.innerHTML = rows.map((applicant, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';

            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            const ipGroup = applicant.ip_group || applicant.ethnicity || '';
            const isPrivate = applicant.is_private || applicant.is_private_school || false;
            const isPublic = applicant.is_public || applicant.is_public_school || false;
            const schoolName = applicant.school || applicant.school_name || '';
            // Show school name in the appropriate column based on type
            const privateSchool = isPrivate ? schoolName : '';
            const publicSchool = isPublic ? schoolName : '';
            const notIP = applicant.disqualification_not_ip || applicant.not_ip || false;
            const exceededIncome = applicant.disqualification_exceeded_income || applicant.exceeded_income || false;
            const incompleteDocs = applicant.disqualification_incomplete_docs || applicant.incomplete_docs || false;

            return `
                <tr class="${rowClass}">
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${applicant.address_line || applicant.ad_reference || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${applicant.contact_email || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${applicant.name || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${applicant.age || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${ipGroup}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${privateSchool}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${publicSchool}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${applicant.course || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${notIP ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${exceededIncome ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${incompleteDocs ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${applicant.remarks || ''}</td>
                </tr>
            `;
        }).join('');
    }

    function renderReplacementsTable(rows) {
        const tableBody = document.getElementById('replacementsTableBody');
        const countEl = document.getElementById('reportCount');
        if (!tableBody) return;
        if (countEl) countEl.textContent = `Total Replacements: ${rows.length}`;

        if (!rows.length) {
            updateReportSummary({});
            tableBody.innerHTML = `
                <tr>
                    <td colspan="19" class="border border-black px-4 py-8 text-center text-slate-600">
                        No replacements found
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate stats
        const stats = {
            'Total Replacements': rows.length
        };
        updateReportSummary(stats);

        tableBody.innerHTML = rows.map((row, index) => {
            const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';

            const addressLine = [
                row.province || '',
                row.municipality || '',
                row.barangay || '',
                row.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = row.is_female || false;
            const isMale = row.is_male || false;

            const isPrivate = row.is_private || false;
            const isPublic = row.is_public || false;
            const schoolType = (row.school_type || row.school1_type || '').toLowerCase();
            const schoolName = row.school_name || row.school1_name || row.school || '';

            const is1st = row.is_1st || false;
            const is2nd = row.is_2nd || false;
            const is3rd = row.is_3rd || false;
            const is4th = row.is_4th || false;
            const is5th = row.is_5th || false;

            return `
                <tr class="${rowClass}">
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${addressLine}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.contact_email || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${row.batch || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center font-medium">${row.no || (index + 1)}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.name || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${row.age || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${isFemale ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${isMale ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.ethnicity || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-white"
                               value="${(schoolType === 'private' || isPrivate) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">
                        <input type="text" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-white"
                               value="${(schoolType === 'public' || isPublic) ? schoolName : ''}" readonly>
                    </td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.course || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${is1st ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${is2nd ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${is3rd ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${is4th ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800 text-center">${is5th ? '✓' : ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.replaced_name || ''}</td>
                    <td class="border border-black px-2 py-2 text-xs text-slate-800">${row.replacement_reason || ''}</td>
                </tr>
            `;
        }).join('');
    }

    window.markWaitingAsChanged = markWaitingAsChanged;

    // ===== data loaders =====
    async function loadActiveReport() {
        setLoading(true);

        try {
            if (state.active === 'grantees') {
                const url = `{{ route('staff.grantees.report') }}`;
                const res = await fetch(url);
                const data = await res.json();
                state.grantees = (data && data.success && Array.isArray(data.grantees)) ? data.grantees : [];
                renderGranteesTable(state.grantees);
            } else if (state.active === 'pamana') {
                const url = `{{ route('staff.pamana.report') }}`;
                const res = await fetch(url);
                const data = await res.json();
                state.pamana = (data && data.success && Array.isArray(data.pamana)) ? data.pamana : [];
                renderPamanaTable(state.pamana);
            } else if (state.active === 'waiting') {
                const url = `{{ route('staff.waiting-list.report') }}`;
                const res = await fetch(url);
                const data = await res.json();
                state.waiting = (data && data.success && Array.isArray(data.waitingList)) ? data.waitingList : [];
                renderWaitingTable(state.waiting);
                // reset waiting state
                state.waitingDirty = false;
                const btn = document.getElementById('saveWaitingBtn');
                const indicator = document.getElementById('waitingUnsavedIndicator');
                if (btn) btn.disabled = true;
                if (indicator) indicator.classList.add('hidden');
            } else if (state.active === 'disqualified') {
                const url = `{{ route('staff.disqualified.report') }}`;
                const res = await fetch(url);
                const data = await res.json();
                const rows = (data && data.success && Array.isArray(data.disqualified)) ? data.disqualified : [];
                renderDisqualifiedTable(rows);
            } else {
                const url = `{{ route('staff.replacements.report') }}`;
                const res = await fetch(url);
                const data = await res.json();
                const rows = (data && data.success && Array.isArray(data.replacements)) ? data.replacements : [];
                renderReplacementsTable(rows);
            }
        } catch (e) {
            console.error(e);
            const countEl = document.getElementById('reportCount');
            if (countEl) countEl.textContent = 'Error loading report data.';
        } finally {
            setLoading(false);
        }
    }

    // ===== exports =====
    function exportGranteesExcel() {
        if (!state.grantees.length) {
            alert('No data to export');
            return;
        }

        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        const headerBg = '#6b8c2f';
        const headerText = '#ffffff';
        const border = '1px solid #4a5d1d';

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

        const bodyRows = state.grantees.map(grantee => {
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

            const yearVal = (flag) => flag ? '✓' : '';
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
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_1st)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_2nd)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_3rd)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_4th)}</td>
                    <td style="border:${border};padding:6px;font-size:11px;text-align:center;">${yearVal(grantee.is_5th)}</td>
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
                    th, td { mso-number-format:"\\@"; }
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

    function exportPamanaToCSV() {
        if (!state.pamana.length) {
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

        const rows = state.pamana.map(applicant => {
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            let genderValue = '';
            if (isFemale && isMale) genderValue = 'F, M';
            else if (isFemale) genderValue = 'F';
            else if (isMale) genderValue = 'M';

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

            const schoolType = (applicant.school_type || applicant.school1_type || '').toLowerCase();
            const schoolName = applicant.school_name || applicant.school1_name || applicant.school || '';

            const remarksStatus = window.normalizeRemarksStatus(applicant.remarks || '');

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
                '10,000',
                '10,000',
                remarksStatus
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
        const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
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

    async function saveWaitingChanges() {
        const btn = document.getElementById('saveWaitingBtn');
        if (!btn) return;

        const rsscInputs = document.querySelectorAll('.waiting-rssc-score');
        const applicants = [];
        rsscInputs.forEach(input => {
            const userId = parseInt(input.getAttribute('data-user-id'));
            const rsscValue = parseFloat(input.value);
            applicants.push({
                user_id: userId,
                rssc_score: isNaN(rsscValue) ? null : rsscValue,
            });
        });

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';

        try {
            const res = await fetch('{{ route("staff.waiting-list.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ applicants })
            });
            const data = await res.json();
            if (data && data.success) {
                state.waitingDirty = false;
                btn.innerHTML = originalHtml;
                btn.disabled = true;
                document.getElementById('waitingUnsavedIndicator')?.classList.add('hidden');
                alert(data.message || 'Saved!');
            } else {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                alert('Error saving changes: ' + (data.message || 'Unknown error'));
            }
        } catch (e) {
            console.error(e);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            alert('Error saving changes. Please try again.');
        }
    }

    function exportWaitingToCSV() {
        if (!state.waiting.length) {
            alert('No data to export');
            return;
        }

        const rsscInputs = document.querySelectorAll('.waiting-rssc-score');

        rsscInputs.forEach(input => {
            const userId = parseInt(input.getAttribute('data-user-id'));
            const rsscValue = parseFloat(input.value);
            const applicant = state.waiting.find(a => a.user_id === userId);
            if (!applicant) return;
            applicant.manual_rssc_score = isNaN(rsscValue) ? null : rsscValue;
        });

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
            'RSSC\'s Score',
            'Rank'
        ];

        const rows = state.waiting.map(applicant => {
            const addressLine = [
                applicant.province || '',
                applicant.municipality || '',
                applicant.barangay || '',
                applicant.ad_reference || ''
            ].filter(Boolean).join(', ');

            const isFemale = applicant.is_female || false;
            const isMale = applicant.is_male || false;
            let genderValue = '';
            if (isFemale && isMale) genderValue = 'F, M';
            else if (isFemale) genderValue = 'F';
            else if (isMale) genderValue = 'M';

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

            const rsscScore = applicant.manual_rssc_score !== null && applicant.manual_rssc_score !== undefined
                ? applicant.manual_rssc_score
                : (applicant.rssc_score || applicant.priority_score || 0);

            const rank = applicant.rank || applicant.priority_rank || '';

            const schoolType = (applicant.school_type || applicant.school1_type || '').toLowerCase();
            const schoolName = applicant.school_name || applicant.school1_name || applicant.school || '';
            const privateSchool = (schoolType === 'private' || applicant.is_private) ? schoolName : '';
            const publicSchool = (schoolType === 'public' || applicant.is_public) ? schoolName : '';

            return [
                addressLine,
                applicant.contact_email || '',
                applicant.batch || '',
                applicant.no || '',
                applicant.name || '',
                applicant.age || '',
                genderValue,
                applicant.ethnicity || '',
                privateSchool,
                publicSchool,
                applicant.course || '',
                yearValue,
                Number(rsscScore).toFixed(2),
                rank ? '#' + rank : ''
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
        const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
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

    function exportReplacementsExcel() {
        const table = document.getElementById('replacementsTable');
        if (!table) {
            alert('No data to export');
            return;
        }

        // Export the visible table as HTML Excel (similar to grantees)
        const html = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                  xmlns:x="urn:schemas-microsoft-com:office:excel"
                  xmlns="http://www.w3.org/TR/REC-html40">
            <head><meta charset="UTF-8"></head>
            <body>${table.outerHTML}</body>
            </html>
        `;

        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        const dateStr = new Date().toISOString().split('T')[0];
        link.download = `Replacements_Report_${dateStr}.xls`;
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // ===== init =====
    document.addEventListener('DOMContentLoaded', function() {
        // buttons
        document.getElementById('exportBtn')?.addEventListener('click', function() {
            if (state.active === 'grantees') exportGranteesExcel();
            else if (state.active === 'pamana') exportPamanaToCSV();
            else if (state.active === 'waiting') exportWaitingToCSV();
            else exportReplacementsExcel();
        });
        document.getElementById('saveWaitingBtn')?.addEventListener('click', saveWaitingChanges);

        // initial tab + initial load
        setActiveTab(state.active || 'grantees');
        loadActiveReport();
    });
</script>
@endpush


