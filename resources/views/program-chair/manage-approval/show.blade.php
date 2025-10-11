@extends('layouts.ProgramChair.app')

@section('content')
<div class="min-vh-100 bg-light">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-teal text-white">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('programchair.manage-approval.index') }}" class="text-white text-decoration-none">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0 fw-bold">{{ $exam->exam_title }}</h1>
        </div>
        
        @if($exam->approval_status != 'approved')
            <button class="btn btn-dark fw-semibold px-4" onclick="approveExam({{ $exam->exam_id }})">
                Approve Exam
            </button>
        @else
            <span class="badge bg-success" style="font-size: 1rem; padding: 8px 16px;">
                <i class="bi bi-check-circle me-1"></i> Approved
            </span>
        @endif
    </div>

    <!-- Info Bar -->
    <div class="bg-white border-bottom px-4 py-2">
        <div class="row">
            <div class="col-md-3">
                <small class="text-muted d-block">Subject</small>
                <strong>{{ $exam->subject->subject_name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Total Items</small>
                <strong>{{ $exam->no_of_items }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Total Points</small>
                <strong>{{ $exam->total_points }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Duration</small>
                <strong>{{ $exam->duration }} minutes</strong>
            </div>
        </div>
    </div>

    <!-- Questions Tab -->
    <div class="bg-white border-bottom">
        <div class="text-center py-3">
            <h2 class="h5 fw-bold text-dark d-inline-block border-bottom border-4 border-teal pb-1 px-3">
                Questions
            </h2>
        </div>
    </div>

    <!-- Content Area -->
    <div class="container py-4">
        
        @forelse($exam->sections as $sectionIndex => $section)
            <!-- Section Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-1">
                        {{ $section->section_title ?: 'Part ' . ($sectionIndex + 1) }}.
                    </h5>
                    @if($section->section_directions)
                        <p class="text-muted mb-0">{{ $section->section_directions }}</p>
                    @endif
                </div>
            </div>

            <!-- Questions in this section -->
            @foreach($section->items as $itemIndex => $item)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="fw-bold mb-0">
                            Question {{ $itemIndex + 1 }}. <span class="fw-normal">{{ $item->question }}</span>
                        </p>
                        <span class="badge bg-secondary">{{ $item->points_awarded }} {{ $item->points_awarded == 1 ? 'pt' : 'pts' }}</span>
                    </div>

                    @if($item->item_type === 'mcq')
                        {{-- Multiple Choice --}}
                        @php 
                            $options = json_decode($item->options, true) ?? [];
                            $answers = json_decode($item->answer, true) ?? [];
                        @endphp
                        <div class="list-group">
                            @foreach($options as $key => $option)
                            <div class="list-group-item {{ in_array($key, $answers) ? 'list-group-item-success fw-semibold' : 'border' }} rounded mb-2">
                                {{ chr(65 + $key) }}. {{ $option }}
                                @if(in_array($key, $answers))
                                    <i class="bi bi-check-circle-fill text-success float-end"></i>
                                @endif
                            </div>
                            @endforeach
                        </div>

                    @elseif($item->item_type === 'torf')
                        {{-- True or False --}}
                        @php $answer = json_decode($item->answer, true); @endphp
                        <div class="list-group">
                            <div class="list-group-item {{ ($answer['correct'] ?? '') === 'true' ? 'list-group-item-success fw-semibold' : 'border' }} rounded mb-2">
                                A. True
                                @if(($answer['correct'] ?? '') === 'true')
                                    <i class="bi bi-check-circle-fill text-success float-end"></i>
                                @endif
                            </div>
                            <div class="list-group-item {{ ($answer['correct'] ?? '') === 'false' ? 'list-group-item-success fw-semibold' : 'border' }} rounded">
                                B. False
                                @if(($answer['correct'] ?? '') === 'false')
                                    <i class="bi bi-check-circle-fill text-success float-end"></i>
                                @endif
                            </div>
                        </div>

                    @elseif($item->item_type === 'iden')
                        {{-- Identification --}}
                        <div class="alert alert-info mb-0">
                            <strong>Expected Answer:</strong> {{ $item->expected_answer }}
                        </div>

                    @elseif($item->item_type === 'enum')
                        {{-- Enumeration --}}
                        @php $answers = json_decode($item->answer, true) ?? []; @endphp
                        <div class="alert alert-info mb-0">
                            <strong>Expected Answers:</strong>
                            <ol class="mb-0 mt-2">
                                @foreach($answers as $answer)
                                    <li>{{ $answer }}</li>
                                @endforeach
                            </ol>
                        </div>

                    @elseif($item->item_type === 'essay')
                        {{-- Essay --}}
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-textarea-t me-2"></i>
                            <strong>Essay Question</strong> - To be graded manually
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        @empty
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="bi bi-question-circle text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No questions found in this exam.</p>
                </div>
            </div>
        @endforelse

    </div>

    <!-- Sticky Bottom Action Bar -->
    @if($exam->approval_status != 'approved')
    <div class="position-fixed bottom-0 start-0 end-0 bg-white border-top p-3 shadow-lg" style="z-index: 1030;">
        <div class="container">
            <div class="d-flex justify-content-end gap-3">
                <button class="btn btn-outline-danger px-4" onclick="openRevisionModal({{ $exam->exam_id }})">
                    <i class="bi bi-x-circle me-1"></i> Send for Revision
                </button>
                <button class="btn btn-success px-4" onclick="approveExam({{ $exam->exam_id }})">
                    <i class="bi bi-check-circle me-1"></i> Approve Exam
                </button>
            </div>
        </div>
    </div>
    @endif

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
                <form id="revisionForm" method="POST" action="{{ route('programchair.manage-approval.revise', $exam->exam_id) }}">
                    @csrf
                    
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

<!-- Custom CSS -->
<style>
    .bg-teal { 
        background-color: #3BA5A4; 
    }
    .border-teal { 
        border-color: #3BA5A4 !important; 
    }
    .list-group-item {
        transition: all 0.2s;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>

@push('scripts')
<script>
    function openRevisionModal(examId) {
        const modal = new bootstrap.Modal(document.getElementById('revisionModal'));
        modal.show();
    }

    function approveExam(examId) {
        if (confirm('Are you sure you want to approve this exam?')) {
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