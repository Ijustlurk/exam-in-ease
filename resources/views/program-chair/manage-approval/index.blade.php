@extends('layouts.ProgramChair.app')

@section('content')
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
                                       onclick="approveExam({{ $exam->exam_id }}, '{{ addslashes($exam->exam_title) }}')"
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2-open" style="font-size: 3rem;"></i>
                            <p class="mt-3">No exams found for approval</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    function approveExam(examId, examTitle) {
        if (confirm(`Are you sure you want to approve "${examTitle}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/programchair/manage-approval/${examId}/approve`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
@endsection