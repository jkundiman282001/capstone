@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Applicants List</h1>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applicants as $applicant)
                    <tr>
                        <td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td>
                        <td>{{ $applicant->email }}</td>
                        <td>{{ optional($applicant->basicInfo)->created_at }}</td>
                        <td>
                            <a href="{{ route('staff.applications.view', $applicant->id) }}" class="btn btn-info">View Application</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-400">No applicants found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>
@endsection 