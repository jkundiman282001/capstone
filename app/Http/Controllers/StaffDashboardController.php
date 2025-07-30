<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::guard('staff')->user();
        if (!$user) {
            return redirect()->route('staff.login'); // Adjust this route if your staff login route is different
        }
        $name = $user->name ?? 'Staff';
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Sample data (replace with real queries)
        $barangays = collect([
            (object)['id' => 1, 'name' => 'Barangay 1'],
            (object)['id' => 2, 'name' => 'Barangay 2'],
        ]);
        $ipGroups = collect([
            (object)['id' => 1, 'name' => 'IP Group A'],
            (object)['id' => 2, 'name' => 'IP Group B'],
        ]);
        $semesters = collect([
            (object)['id' => 1, 'name' => '1st Sem 2024'],
            (object)['id' => 2, 'name' => '2nd Sem 2024'],
        ]);

        // Example metrics
        $totalScholars = 120;
        $newApplicants = 15;
        $activeScholars = 100;
        $inactiveScholars = 20;

        // Example alerts
        $alerts = [
            (object)[
                'scholar_name' => 'Juan Dela Cruz',
                'message' => 'Renewal deadline approaching',
                'due_date' => '2024-07-01',
                'status' => 'Pending'
            ],
            (object)[
                'scholar_name' => 'Maria Santos',
                'message' => 'Missing grade report',
                'due_date' => '2024-06-25',
                'status' => 'Urgent'
            ],
        ];

        // Example chart data
        $barChartData = [
            'labels' => ['Barangay 1', 'Barangay 2'],
            'datasets' => [[
                'label' => 'Scholars',
                'backgroundColor' => ['#3b82f6', '#f59e42'],
                'data' => [70, 50]
            ]]
        ];
        $pieChartData = [
            'labels' => ['New', 'Approved', 'Rejected', 'Renewal'],
            'datasets' => [[
                'backgroundColor' => ['#3b82f6', '#10b981', '#ef4444', '#f59e42'],
                'data' => [15, 80, 5, 20]
            ]]
        ];
        $performanceChartData = [
            'labels' => ['1st Sem', '2nd Sem'],
            'datasets' => [[
                'label' => 'Average GWA',
                'borderColor' => '#6366f1',
                'backgroundColor' => 'rgba(99,102,241,0.2)',
                'data' => [1.75, 1.85]
            ]]
        ];

        // Prioritized pending requirements sample data
        $pendingRequirements = [
            (object)[
                'scholar_name' => 'Maria Santos',
                'missing_document' => 'Certificate of Low Income',
                'is_overdue' => true,
                'priority' => 1,
                'submitted_documents' => []
            ],
            (object)[
                'scholar_name' => 'Juan Dela Cruz',
                'missing_document' => 'Grade Slip',
                'is_overdue' => false,
                'priority' => 2,
                'submitted_documents' => ['Certificate of Low Income']
            ],
            (object)[
                'scholar_name' => 'Pedro Reyes',
                'missing_document' => 'Renewal Form',
                'is_overdue' => false,
                'priority' => 3,
                'submitted_documents' => ['Grade Slip']
            ],
        ];

        // Demo feedbacks (in a real app, fetch from DB)
        $feedbacks = session('feedbacks', []);

        // Fetch unread notifications for staff
        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.dashboard', compact(
            'name', 'assignedBarangay', 'barangays', 'ipGroups', 'semesters',
            'totalScholars', 'newApplicants', 'activeScholars', 'inactiveScholars',
            'alerts', 'barChartData', 'pieChartData', 'performanceChartData',
            'pendingRequirements',
            'feedbacks',
            'notifications'
        ));
    }

    public function downloadReport(Request $request)
    {
        // Implement report generation and download logic here
        return back()->with('success', 'Report download feature coming soon!');
    }

    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'feedback_text' => 'required|string|max:1000',
        ]);
        $feedbacks = session('feedbacks', []);
        $feedbacks[] = [
            'text' => $validated['feedback_text'],
            'submitted_at' => now()->toDateTimeString(),
        ];
        session(['feedbacks' => $feedbacks]);
        return back()->with('success', 'Thank you for your feedback!');
    }

    public function markNotificationsRead(Request $request)
    {
        $user = \Auth::guard('staff')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function viewApplication($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $basicInfo = \App\Models\BasicInfo::where('user_id', $userId)->first();
        $schoolPref = $basicInfo ? \App\Models\SchoolPref::find($basicInfo->school_pref_id) : null;
        $education = $basicInfo ? \App\Models\Education::where('basic_info_id', $basicInfo->id)->get() : collect();
        $siblings = $basicInfo ? \App\Models\FamSiblings::where('basic_info_id', $basicInfo->id)->get() : collect();
        $ethno = $user->ethno_id ? \App\Models\Ethno::find($user->ethno_id) : null;

        // Address fetching (simplified, adjust as needed)
        $fullAddress = $basicInfo ? \DB::table('full_address')->where('id', $basicInfo->full_address_id)->first() : null;
        $mailing = $fullAddress ? \DB::table('mailing_address')->where('id', $fullAddress->mailing_address_id)->first() : null;
        $permanent = $fullAddress ? \DB::table('permanent_address')->where('id', $fullAddress->permanent_address_id)->first() : null;
        $origin = $fullAddress ? \DB::table('origin')->where('id', $fullAddress->origin_id)->first() : null;

        // Family (if you have a Family model, otherwise adjust)
        $familyFather = $basicInfo ? \App\Models\Family::where('basic_info_id', $basicInfo->id)->where('fam_type', 'father')->first() : null;
        $familyMother = $basicInfo ? \App\Models\Family::where('basic_info_id', $basicInfo->id)->where('fam_type', 'mother')->first() : null;

        // Documents
        $documents = \App\Models\Document::where('user_id', $userId)->latest()->get();
        $requiredTypes = [
            'birth_certificate' => 'Certified Birth Certificate',
            'income_document' => 'Income Tax Return/Tax Exemption/Indigency',
            'tribal_certificate' => 'Certificate of Tribal Membership/Confirmation',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral',
            'grades' => 'Latest Copy of Grades',
        ];

        return view('staff.application-view', compact(
            'user', 'basicInfo', 'schoolPref', 'education', 'siblings', 'ethno',
            'mailing', 'permanent', 'origin', 'familyFather', 'familyMother',
            'documents', 'requiredTypes'
        ));
    }

    public function applicantsList()
    {
        // Get all users who have a BasicInfo record (i.e., submitted an application)
        $applicants = \App\Models\User::whereHas('basicInfo')->get();
        return view('staff.applicants-list', compact('applicants'));
    }

    public function updateDocumentStatus(Request $request, $documentId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $document = \App\Models\Document::findOrFail($documentId);
        $document->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Document status updated successfully',
            'new_status' => $request->status
        ]);
    }
} 