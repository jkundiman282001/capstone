@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Scholarship Application (Read-Only)</h1>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Personal Info</h2>
        <div><strong>Type of Assistance:</strong> {{ $basicInfo->type_assist }}</div>
        <div><strong>First Name:</strong> {{ $user->first_name }}</div>
        <div><strong>Middle Name:</strong> {{ $user->middle_name }}</div>
        <div><strong>Last Name:</strong> {{ $user->last_name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Contact Number:</strong> {{ $user->contact_num }}</div>
        <div><strong>Date of Birth:</strong> {{ $basicInfo->birthdate }}</div>
        <div><strong>Place of Birth:</strong> {{ $basicInfo->birthplace }}</div>
        <div><strong>Gender:</strong> {{ $basicInfo->gender }}</div>
        <div><strong>Civil Status:</strong> {{ $basicInfo->civil_status }}</div>
        <div><strong>Ethnolinguistic Group:</strong> {{ $ethno->ethnicity ?? '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Address</h2>
        <div class="mb-2"><strong>Mailing Address:</strong> {{ $mailing->house_num ?? '' }}, {{ $mailing->address_id ? (\App\Models\Address::find($mailing->address_id)->barangay ?? '') : '' }}, {{ $mailing->address_id ? (\App\Models\Address::find($mailing->address_id)->municipality ?? '') : '' }}, {{ $mailing->address_id ? (\App\Models\Address::find($mailing->address_id)->province ?? '') : '' }}</div>
        <div class="mb-2"><strong>Permanent Address:</strong> {{ $permanent->house_num ?? '' }}, {{ $permanent->address_id ? (\App\Models\Address::find($permanent->address_id)->barangay ?? '') : '' }}, {{ $permanent->address_id ? (\App\Models\Address::find($permanent->address_id)->municipality ?? '') : '' }}, {{ $permanent->address_id ? (\App\Models\Address::find($permanent->address_id)->province ?? '') : '' }}</div>
        <div class="mb-2"><strong>Place of Origin:</strong> {{ $origin->house_num ?? '' }}, {{ $origin->address_id ? (\App\Models\Address::find($origin->address_id)->barangay ?? '') : '' }}, {{ $origin->address_id ? (\App\Models\Address::find($origin->address_id)->municipality ?? '') : '' }}, {{ $origin->address_id ? (\App\Models\Address::find($origin->address_id)->province ?? '') : '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Education</h2>
        @foreach($education as $edu)
            <div class="mb-2">
                <strong>Category:</strong> {{ $edu->category }}<br>
                <strong>School Name:</strong> {{ $edu->school_name }}<br>
                <strong>School Type:</strong> {{ $edu->school_type }}<br>
                <strong>Year Graduate:</strong> {{ $edu->year_grad }}<br>
                <strong>Grade Average:</strong> {{ $edu->grade_ave }}<br>
                <strong>Rank:</strong> {{ $edu->rank }}
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Family</h2>
        <div class="mb-2"><strong>Father's Name:</strong> {{ $familyFather->name ?? '' }}</div>
        <div class="mb-2"><strong>Father's Status:</strong> {{ $familyFather->status ?? '' }}</div>
        <div class="mb-2"><strong>Father's Address:</strong> {{ $familyFather->address ?? '' }}</div>
        <div class="mb-2"><strong>Father's Occupation:</strong> {{ $familyFather->occupation ?? '' }}</div>
        <div class="mb-2"><strong>Father's Office Address:</strong> {{ $familyFather->office_address ?? '' }}</div>
        <div class="mb-2"><strong>Father's Educational Attainment:</strong> {{ $familyFather->educational_attainment ?? '' }}</div>
        <div class="mb-2"><strong>Father's Ethnolinguistic Group:</strong> {{ $familyFather->ethno_id ? (\App\Models\Ethno::find($familyFather->ethno_id)->ethnicity ?? '') : '' }}</div>
        <div class="mb-2"><strong>Father's Income:</strong> {{ $familyFather->income ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Name:</strong> {{ $familyMother->name ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Status:</strong> {{ $familyMother->status ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Address:</strong> {{ $familyMother->address ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Occupation:</strong> {{ $familyMother->occupation ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Office Address:</strong> {{ $familyMother->office_address ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Educational Attainment:</strong> {{ $familyMother->educational_attainment ?? '' }}</div>
        <div class="mb-2"><strong>Mother's Ethnolinguistic Group:</strong> {{ $familyMother->ethno_id ? (\App\Models\Ethno::find($familyMother->ethno_id)->ethnicity ?? '') : '' }}</div>
        <div class="mb-2"><strong>Mother's Income:</strong> {{ $familyMother->income ?? '' }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">Siblings</h2>
        @forelse($siblings as $sibling)
            <div class="mb-2 border-b pb-2">
                <strong>Name:</strong> {{ $sibling->name }}<br>
                <strong>Age:</strong> {{ $sibling->age }}<br>
                <strong>Scholarship:</strong> {{ $sibling->scholarship }}<br>
                <strong>Course/Year Level:</strong> {{ $sibling->course_year }}<br>
                <strong>Status:</strong> {{ $sibling->present_status }}
            </div>
        @empty
            <div class="text-gray-400">No siblings listed.</div>
        @endforelse
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-lg mb-2">School Preference</h2>
        <div class="mb-2"><strong>First Preference Address:</strong> {{ $schoolPref->address ?? '' }}</div>
        <div class="mb-2"><strong>First Preference Degree/Course:</strong> {{ $schoolPref->degree ?? '' }}</div>
        <div class="mb-2"><strong>First Preference School Type:</strong> {{ $schoolPref->school_type ?? '' }}</div>
        <div class="mb-2"><strong>First Preference No. of Years:</strong> {{ $schoolPref->num_years ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference Address:</strong> {{ $schoolPref->address2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference Degree/Course:</strong> {{ $schoolPref->degree2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference School Type:</strong> {{ $schoolPref->school_type2 ?? '' }}</div>
        <div class="mb-2"><strong>Second Preference No. of Years:</strong> {{ $schoolPref->num_years2 ?? '' }}</div>
        <div class="mb-2"><strong>Contribution to Community:</strong> {{ $schoolPref->ques_answer1 ?? '' }}</div>
        <div class="mb-2"><strong>Plans After Graduation:</strong> {{ $schoolPref->ques_answer2 ?? '' }}</div>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>
@endsection
