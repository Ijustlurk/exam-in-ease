@extends('layouts.ProgramChair.app')

@section('main-content')
<div style="padding: 2rem;">
<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header with Search -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Exams for Approval</h2>

        <!-- Search -->
        <form method="GET" action="{{ route('programchair.manage-approval.index') }}" class="position-relative" style="width: 350px;">
            <input type="text" name="search" class="form-control ps-3 pe-5 rounded-pill shadow-sm" 
                   placeholder="Search for exams" value="{{ request('search') }}">
            <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-muted">
                <i class="bi bi-search"></i>
            </span>
        </form>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">
                            <input type="checkbox" class="form-check-input">
                        </th>
                        <th scope="col">Exam Name <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Subject <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Schedule <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Approval Status <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="fw-semibold">{{ $exam->exam_title }}</div>
                            <small class="text-muted fst-italic">
                                {{-- FIXED: Changed from $exam->user to $exam->teacher --}}
                                @if($exam->teacher)
                                    {{ $exam->teacher->first_name }} {{ $exam->teacher->last_name }}
                                @else
                                    Unknown Author
                                @endif
                                @if($exam->collaborations->count() > 0)
                                    , {{ $exam->collaborations->count() }} 
                                    {{ $exam->collaborations->count() == 1 ? 'other' : 'others' }}
                                @endif
                            </small>
                        </td>
                        <td>{{ $exam->subject->subject_name ?? 'N/A' }}</td>
                        <td>
                            @if($exam->schedule_start && $exam->schedule_end)
                                <div class="small">
                                    <div><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($exam->schedule_start)->format('M d, Y') }}</div>
                                    <div class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($exam->schedule_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($exam->schedule_end)->format('h:i A') }}</div>
                                </div>
                            @else
                                <span class="text-muted">Not scheduled</span>
                            @endif
                        </td>
                        <td>
                            @if($exam->approval_status == 'approved')
                                <span class="badge bg-success px-3 py-2">Approved</span>
                            @elseif($exam->approval_status == 'rejected')
                                <span class="badge bg-danger px-3 py-2">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($exam->approval_status == 'approved')
                                <!-- Approved exam actions -->
                                <div class="d-flex gap-3">
                                    <a href="javascript:void(0)" 
                                       onclick="openRescindModal({{ $exam->exam_id }}, '{{ addslashes($exam->exam_title) }}')"
                                       class="text-secondary text-decoration-none">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Rescind
                                    </a>
                                    <a href="{{ route('programchair.manage-approval.show', $exam->exam_id) }}" 
                                       class="text-secondary text-decoration-none">
                                        <i class="bi bi-search me-1"></i> View
                                    </a>
                                </div>
                            @else
                                <!-- Pending exam actions -->
                                <div class="d-flex gap-3">
                                    <a href="javascript:void(0)" 
                                       onclick="openApprovalModal({{ $exam->exam_id }})"
                                       class="text-success text-decoration-none">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </a>
                                    <a href="javascript:void(0)" 
                                       onclick="openRevisionModal({{ $exam->exam_id }}, '{{ addslashes($exam->exam_title) }}')"
                                       class="text-danger text-decoration-none">
                                        <i class="bi bi-x-circle me-1"></i> Revise
                                    </a>
                                    <a href="{{ route('programchair.manage-approval.show', $exam->exam_id) }}" 
                                       class="text-secondary text-decoration-none">
                                        <i class="bi bi-search me-1"></i> View
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2-open" style="font-size: 3rem;"></i>
                            <p class="mt-3">No exams found for approval</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #6ba5b3; color: white; border-radius: 12px 12px 0 0; padding: 20px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="revisionModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    <span>Slate for revision</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="revisionForm" method="POST">
                    @csrf
                    <input type="hidden" id="revision_exam_id" name="exam_id">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Add Notes</label>
                        <textarea class="form-control" name="notes" rows="4" 
                                  placeholder="Enter revision notes for the instructor..."
                                  required style="border-radius: 8px; border: 1px solid #d1d5db;"></textarea>
                        <small class="text-muted">Minimum 10 characters required</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" 
                                style="background-color: #6ba5b3; border: none; border-radius: 8px; padding: 10px 28px; font-weight: 500;">
                            For Revision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #28a745; color: white; border-radius: 12px 12px 0 0; padding: 20px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="approvalModalLabel">
                    <i class="bi bi-check-circle-fill"></i>
                    <span id="approvalModalTitle">Approve Exam</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="approvalForm" method="POST">
                    @csrf
                    <input type="hidden" id="approval_exam_id" name="exam_id">
                    
                    <!-- Exam Details Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3">Exam Details</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Exam Title</label>
                                <p class="form-control-plaintext" id="approval_exam_title"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Subject</label>
                                <p class="form-control-plaintext" id="approval_subject"></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Description</label>
                            <p class="form-control-plaintext" id="approval_description"></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Assigned Classes</label>
                            <p class="form-control-plaintext" id="approval_classes"></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Editable Fields Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3">Exam Configuration</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="approval_duration" name="duration" 
                                       min="1" required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Schedule Start <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="approval_schedule_start" 
                                       name="schedule_start" required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Schedule End <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="approval_schedule_end" 
                                       name="schedule_end" required style="border-radius: 8px; border: 1px solid #d1d5db;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exam Password (Optional)</label>
                            <input type="text" class="form-control" id="approval_password" name="exam_password" 
                                   placeholder="Leave empty if no password required" 
                                   style="border-radius: 8px; border: 1px solid #d1d5db;">
                            <small class="text-muted">Students will need this password to access the exam</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                                style="border-radius: 8px; padding: 10px 20px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" 
                                style="border-radius: 8px; padding: 10px 28px; font-weight: 500;">
                            <i class="bi bi-check-circle me-1"></i> Approve Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Rescind Modal -->
<div class="modal fade" id="rescindModal" tabindex="-1" aria-labelledby="rescindModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: #dc3545; color: white; border-radius: 12px 12px 0 0; padding: 20px 24px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="rescindModalLabel">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Rescind Approval</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="rescindForm" method="POST">
                    @csrf
                    <input type="hidden" id="rescind_exam_id" name="exam_id">
                    
                    <div class="mb-4">
                        <p class="text-muted">Are you sure you want to rescind approval for this exam?</p>
                        <label class="form-label fw-semibold">Reason (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Enter reason for rescinding approval..."
                                  style="border-radius: 8px; border: 1px solid #d1d5db;"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                                style="border-radius: 8px; padding: 10px 20px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" 
                                style="border-radius: 8px; padding: 10px 28px; font-weight: 500;">
                            Rescind Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .table thead th {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #555;
    }
</style>

@push('scripts')
<script>
    function openApprovalModal(examId) {
        // Fetch exam details via AJAX
        fetch(`/programchair/manage-approval/programchair/${examId}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Populate modal header
                document.getElementById('approvalModalTitle').textContent = 
                    `Approve: ${data.exam_title} by ${data.author}`;
                
                // Populate exam details (read-only)
                document.getElementById('approval_exam_title').textContent = data.exam_title;
                document.getElementById('approval_subject').textContent = data.subject || 'N/A';
                document.getElementById('approval_description').textContent = data.exam_desc || 'No description provided';
                document.getElementById('approval_classes').textContent = data.classes || 'Not assigned to any class yet';
                
                // Populate editable fields with existing values
                document.getElementById('approval_exam_id').value = examId;
                document.getElementById('approval_duration').value = data.duration || '';
                document.getElementById('approval_schedule_start').value = data.schedule_start || '';
                document.getElementById('approval_schedule_end').value = data.schedule_end || '';
                document.getElementById('approval_password').value = '';
                
                // Set form action
                document.getElementById('approvalForm').action = `/programchair/manage-approval/${examId}/approve`;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching exam details:', error);
                alert('Failed to load exam details: ' + error.message);
            });
    }

    function openRevisionModal(examId, examTitle) {
        document.getElementById('revision_exam_id').value = examId;
        document.getElementById('revisionForm').action = `/programchair/manage-approval/${examId}/revise`;
        
        const modal = new bootstrap.Modal(document.getElementById('revisionModal'));
        modal.show();
    }

    function openRescindModal(examId, examTitle) {
        document.getElementById('rescind_exam_id').value = examId;
        document.getElementById('rescindForm').action = `/programchair/manage-approval/${examId}/rescind`;
        
        const modal = new bootstrap.Modal(document.getElementById('rescindModal'));
        modal.show();
    }
</script>
@endpush

</div>
@endsection