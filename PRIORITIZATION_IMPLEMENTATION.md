# Prioritization Implementation Locations

This document outlines where prioritization is implemented in the codebase. There are **two main prioritization systems**:

1. **Document Priority System** - First Come, First Serve (FCFS) for document review
2. **Applicant Scoring System** - Multi-criteria scoring for scholarship applicants

---

## 1. Document Priority System (First Come, First Serve)

### Service Layer
**File:** `app/Services/DocumentPriorityService.php`
- **Main Class:** `DocumentPriorityService`
- **Key Methods:**
  - `calculateDocumentPriority()` - Calculates priority score based on submission time (lines 43-89)
  - `recalculateAllPriorities()` - Recalculates all document priorities and assigns ranks (lines 94-158)
  - `getPrioritizedDocuments()` - Gets documents ordered by priority (lines 164-177)
  - `getTopPriorityDocuments()` - Gets top priority documents for review (lines 182-191)
  - `onDocumentUploaded()` - Handles priority calculation when document is uploaded (lines 196-209)
  - `getPriorityStatistics()` - Gets priority statistics (lines 214-246)

**Priority Logic:**
- Primary: Submission time (earlier = higher priority)
- Secondary: Priority indigenous groups (b'laan, bagobo, kalagan, kaulo)
- Tertiary: Document type weights (income_document = highest)
- Calculates `priority_score` and assigns `priority_rank`

### Controller Layer
**File:** `app/Http/Controllers/StaffDashboardController.php`
- **Method:** `index()` - Lines 119-144
  - Initializes document priorities on dashboard load
  - Gets prioritized documents for display
- **Method:** `viewApplication()` - Lines 286-292
  - Orders documents by priority when viewing application
- **Method:** `recalculateDocumentPriorities()` - Lines 513-531
  - API endpoint to recalculate all document priorities
- **Method:** `getPrioritizedDocuments()` - Lines 536-562
  - API endpoint to get prioritized documents
- **Method:** `getDocumentPriorityStatistics()` - Lines 567-576
  - API endpoint to get priority statistics

**File:** `app/Http/Controllers/DocumentController.php`
- **Method:** `store()` - Lines 44-46
  - Calculates priority when a new document is uploaded
  - Calls `DocumentPriorityService::onDocumentUploaded()`

### Model Layer
**File:** `app/Models/Document.php`
- **Fields:** `priority_rank`, `priority_score`, `submitted_at` (lines 18-20)
- **Accessor:** `getPriorityLevelAttribute()` - Returns priority level based on rank (lines 39-52)
- **Accessor:** `getWaitingHoursAttribute()` - Returns waiting time in hours (lines 57-64)

### Database Schema
**File:** `database/migrations/2025_11_03_050940_add_priority_to_documents_table.php`
- Adds `priority_rank` (integer, nullable)
- Adds `priority_score` (decimal 10,2, default 0)
- Adds `submitted_at` (timestamp, nullable)
- Creates indexes on priority fields (lines 19-21)

### Routes
**File:** `routes/web.php`
- Line 64: `POST /staff/documents/recalculate-priorities`
- Line 65: `GET /staff/documents/prioritized`
- Line 66: `GET /staff/documents/priority-statistics`

### Views
**File:** `resources/views/staff/dashboard.blade.php`
- Displays prioritized documents (lines 232-233, 391)
- Shows priority rank and submission time

**File:** `resources/views/staff/application-view.blade.php`
- Displays document priority rank (lines 185-187)
- Shows documents ordered by priority

---

## 2. Applicant Priority Scoring (Multi-Criteria Scoring)

### Service Layer
**File:** `app/Services/ApplicantPriorityService.php`
- **Main Class:** `ApplicantPriorityService`
- **Scoring Weights (current):**
  - IP Group rubric: 20%
  - GWA (75–100): 30%
  - Income Tax Return (ITR): 30%
  - Citations/Awards: 10%
  - Social Responsibility (essays): 10%

**Key Methods:**
- `getPrioritizedApplicants()` - Calculates scores for all applicants and assigns ranks (FCFS tiebreaker)
- `getTopPriorityApplicants()` - Gets top priority applicants
- `getPriorityStatistics()` - Counts coverage statistics for dashboard/overview
- `calculateApplicantPriority()` - Returns a single applicant’s priority breakdown (useful for student views)

**Priority Logic:**
- Calculates weighted total score (0–100)
- Assigns `priority_rank` based on total score (higher score = higher rank)
- Uses FCFS as tie-breaker (earlier submission wins)

### Controller Layer
**File:** `app/Http/Controllers/StaffDashboardController.php`
- **Method:** `calculateAllScores()` - Lines 436-453
  - API endpoint to calculate scores for all applicants
- **Method:** `getTopPriorityApplicants()` - Lines 458-468
  - API endpoint to get top priority applicants
- **Method:** `getScoringStatistics()` - Lines 473-482
  - API endpoint to get scoring statistics
- **Method:** `calculateApplicantScore()` - Lines 487-508
  - API endpoint to calculate score for a specific applicant
- **Method:** `applicantsList()` - Lines 364-382
  - Filters applicants by priority level (high/medium/low/very_low)
- **Method:** `viewApplication()` - Line 254
  - Loads applicant score when viewing application

### Data Sources
- **IP rubric inputs**: document statuses for `tribal_certificate`, `endorsement`, `birth_certificate` (+priority ethno bonus)
- **Academic input (GWA)**: `basic_info.gpa` (75–100 scale)
- **ITR**: approved `income_document`
- **Awards**: `education.rank`
- **Social responsibility**: `school_pref.ques_answer1` + `school_pref.ques_answer2`

### Routes
**File:** `routes/web.php`
- Line 58: `POST /staff/scores/calculate-all`
- Line 59: `GET /staff/scores/top-priority`
- Line 60: `GET /staff/scores/statistics`
- Line 61: `POST /staff/scores/calculate/{user}`

### Views
**File:** `resources/views/staff/application-view.blade.php`
- Displays applicant priority rank (lines 310-311)
- Shows geographic priority score (lines 407-410)

**File:** `resources/views/staff/applicants-list.blade.php`
- Displays applicant priority rank (lines 141-142)
- Shows priority in applicant list

---

## Key Implementation Details

### Document Priority Calculation
1. **Submission Time:** Uses `submitted_at` timestamp (falls back to `created_at`)
2. **Priority Score Formula:**
   - Base: `hoursSinceSubmission * 1000 + secondsSinceSubmission`
   - Adjustment: Subtracts document type weight * 0.01
   - Bonus: +500 for priority indigenous groups
3. **Ranking:** Sorts by `submitted_at ASC` (oldest first = highest priority)
4. **Tie-breakers:**
   - Priority indigenous group status
   - `created_at` timestamp

### Applicant Score Calculation
1. **Weighted Scoring:** Multiplies each normalized criterion score by its weight
2. **Total Score:** Sum of weighted scores (0–100 scale)
3. **Ranking:** Sorts by `priority_score DESC` (highest score = rank #1)
4. **Tie-breaker:** FCFS via application submission timestamp

### Priority Initialization
- Document priorities are initialized in `StaffDashboardController::index()` when dashboard loads
- Applicant scores are calculated on-demand via API endpoints
- Document priority is automatically calculated when document is uploaded

### Database Indexes
- Documents: Indexed on `[priority_rank, status]`, `[priority_score, status]`, and `submitted_at`
- Applicant Scores: Indexed on `[total_score, priority_rank]` and `user_id`

---

## Summary

Prioritization is implemented in:

1. **Service Classes:** `DocumentPriorityService` and `ApplicantScoringService`
2. **Controllers:** `StaffDashboardController` and `DocumentController`
3. **Models:** `Document` and `ApplicantScore`
4. **Database:** Two migration files adding priority fields
5. **Routes:** 7 API endpoints for priority management
6. **Views:** Staff dashboard, application view, and applicants list

The system uses **FCFS for documents** and **multi-criteria scoring for applicants**, with automatic priority calculation and ranking assignment.

