{{-- resources/views/instructor/exams/create.blade.php --}}
@extends('layouts.Instructor.app')

@section('content')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #e8eef2;
    }
    
    .exam-builder-container {
        background-color: #e8eef2;
        min-height: 100vh;
        padding-bottom: 80px;
    }
    
    /* Header Styles */
    .builder-header {
        background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%);
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 32px;
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: transform 0.3s ease-in-out;
    }
    
    .builder-header.header-hidden {
        transform: translateY(-100%);
    }
    
    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .exam-icon {
        width: 48px;
        height: 48px;
        background-color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #6b9aac;
    }
    
    .exam-title-section {
        display: flex;
        flex-direction: column;
    }
    
    .exam-title-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        padding: 0;
        outline: none;
        border-bottom: 2px solid transparent;
        transition: border-color 0.2s;
        width: 100%;
        min-width: 200px;
    }
    
    .exam-title-input:focus {
        border-bottom-color: white;
    }
    
    .exam-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
        font-weight: 400;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .header-icon-btn {
        background-color: transparent;
        border: none;
        color: white;
        font-size: 1.3rem;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }
    
    .header-icon-btn:hover {
        background-color: rgba(255,255,255,0.2);
    }
    
    .approval-btn {
        background-color: rgba(0,0,0,0.3);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .approval-btn:hover {
        background-color: rgba(0,0,0,0.4);
    }
    
    .approval-btn.pending {
        background-color: rgba(220,38,38,0.3);
    }
    
    .approval-btn.approved {
        background-color: rgba(34,197,94,0.3);
    }
    
    .back-btn {
        background-color: rgba(0,0,0,0.2);
        color: white;
        border: 2px solid white;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .back-btn:hover {
        background-color: white;
        color: #6b9aac;
    }
    
    /* Section Styles */
    .section-card {
        background-color: white;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .section-header {
        background-color: #7ca5b8;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title {
        color: white;
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }
    
    .section-delete-btn {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .section-delete-btn:hover {
        background-color: rgba(0,0,0,0.2);
    }
    
    .section-body {
        padding: 24px;
    }
    
    .section-title-input {
        width: 100%;
        border: none;
        border-bottom: 1px solid #d1d5db;
        padding: 12px 0;
        font-size: 1rem;
        font-style: italic;
        color: #9ca3af;
        outline: none;
        margin-bottom: 16px;
    }
    
    .section-title-input:focus {
        color: #212529;
        border-bottom-color: #7ca5b8;
    }
    
    .section-directions {
        width: 100%;
        border: none;
        border-bottom: 1px solid #d1d5db;
        padding: 12px 0;
        font-size: 0.95rem;
        color: #9ca3af;
        outline: none;
        resize: none;
        min-height: 40px;
    }
    
    .section-directions:focus {
        color: #212529;
        border-bottom-color: #7ca5b8;
    }
    
    /* Question Card Styles */
    .question-wrapper {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        position: relative;
    }
    
    .question-card {
        flex: 1;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .question-card.active {
        box-shadow: 0 4px 12px rgba(124,165,184,0.3);
        border: 2px solid #7ca5b8;
    }
    
    .question-header {
        background-color: #7ca5b8;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .question-header-title {
        color: white;
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }
    
    .question-header-actions {
        display: flex;
        gap: 8px;
    }
    
    .question-header-btn {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .question-header-btn:hover {
        background-color: rgba(0,0,0,0.2);
    }
    
    .question-body {
        padding: 24px;
        position: relative;
    }
    
    .question-type-badge {
        position: absolute;
        top: 24px;
        right: 24px;
        background-color: #f3f4f6;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .question-text {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 20px;
        padding-right: 100px;
    }
    
    .options-list {
        list-style: none;
        padding: 0;
        margin: 16px 0;
    }
    
    .option-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #e5e7eb;
        gap: 12px;
    }
    
    .option-text {
        flex: 1;
        color: #374151;
        font-size: 0.9rem;
    }
    
    .correct-badge {
        background: #d1fae5;
        color: #065f46;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .expected-answer-box {
        background: #f9fafb;
        padding: 16px;
        border-radius: 8px;
        margin-top: 12px;
        border: 1px solid #e5e7eb;
    }
    
    .expected-answer-box strong {
        color: #374151;
        font-size: 0.9rem;
    }
    
    .expected-answer-box span {
        color: #6b7280;
        margin-left: 8px;
    }
    
    .points-display {
        text-align: right;
        color: #6b7280;
        font-size: 0.9rem;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        font-style: italic;
    }
    
    .points-display strong {
        color: #374151;
    }
    
    /* Floating Action Pane */
    .floating-action-pane {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        padding: 8px;
        display: none;
        flex-direction: column;
        gap: 4px;
    }
    
    .question-card.active + .floating-action-pane {
        display: flex;
    }
    
    .floating-btn {
        background: transparent;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        font-size: 1.2rem;
        color: #374151;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }
    
    .floating-btn:hover {
        background-color: #f3f4f6;
    }
    
    /* Add Section */
    .add-section {
        text-align: center;
        margin-top: 24px;
    }
    
    .add-dropdown-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .add-main-btn {
        background-color: white;
        border: 1px solid #d1d5db;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #374151;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .add-main-btn:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
    
    .add-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 8px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 220px;
        display: none;
        z-index: 100;
    }
    
    .add-dropdown.show {
        display: block;
    }
    
    .dropdown-item {
        padding: 12px 16px;
        cursor: pointer;
        font-size: 0.9rem;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background-color 0.15s;
        border: none;
        width: 100%;
        text-align: left;
        background: transparent;
    }
    
    .dropdown-item:hover {
        background-color: #f9fafb;
    }
    
    .dropdown-item i {
        color: #6b7280;
        font-size: 1rem;
    }
    
    .no-questions-yet {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    .no-questions-yet i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    
    .no-questions-yet p {
        color: #6b7280;
        font-size: 1rem;
    }

    /* Modal Styles */
    .modal-header-custom {
        background-color: #7ca5b8;
        color: white;
        padding: 16px 24px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .modal-title-custom {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .form-label-custom {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.9rem;
        display: block;
    }
    
    .form-control-custom {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        width: 100%;
        outline: none;
        transition: border-color 0.3s;
    }
    
    .form-control-custom:focus {
        border-color: #7ca5b8;
        box-shadow: 0 0 0 3px rgba(124,165,184,0.1);
    }
    
    .option-input-group {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .option-input-group input[type="text"] {
        flex: 1;
    }
    
    .correct-checkbox {
        width: 24px;
        height: 24px;
        cursor: pointer;
        accent-color: #10b981;
    }
    
    .btn-add-option {
        background: #7ca5b8;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-add-option:hover {
        background: #6a94a6;
    }
    
    .btn-save-question {
        background: #7ca5b8;
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-save-question:hover {
        background: #6a94a6;
    }

    /* Class Dropdown Styles */
    .class-dropdown {
        position: relative;
    }

    .class-dropdown-list .class-item:hover {
        background-color: #f3f4f6;
    }

    @media (max-width: 768px) {
        .builder-header {
            flex-direction: column;
            gap: 16px;
        }
        
        .floating-action-pane {
            position: static;
            flex-direction: row;
            margin-top: 12px;
        }
    }
</style>

<div class="exam-builder-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show container" style="max-width: 1400px; margin: 0 auto 20px;" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="builder-header">
        <div class="header-left">
            <div class="exam-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <div class="exam-title-section">
                <input type="text" 
                       class="exam-title-input" 
                       value="{{ $exam->exam_title }}" 
                       id="examTitle"
                       @if($exam->status === 'for approval' || $exam->status === 'approved')
                       readonly
                       style="cursor: not-allowed;"
                       @else
                       onchange="updateExamTitle()"
                       @endif>
                <span class="exam-subtitle" id="examSubtitle">{{ $exam->subject->subject_name ?? 'Edit Exam.' }}</span>
            </div>
            @if($exam->status !== 'for approval' && $exam->status !== 'approved')
            <button class="header-icon-btn" title="Edit Title">
                <i class="bi bi-pencil"></i>
            </button>
            @endif
        </div>
        <div class="header-actions">
            <a href="{{ route('instructor.exams.index') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                <span>Back</span>
            </a>
            <button class="header-icon-btn" title="Save">
                <i class="bi bi-floppy"></i>
            </button>
            <button class="header-icon-btn" title="Settings" onclick="openExamSettings()">
                <i class="bi bi-gear"></i>
            </button>
            <button class="header-icon-btn" title="Download">
                <i class="bi bi-download"></i>
            </button>
            <button class="approval-btn" id="approvalBtn">Request for Approval</button>
        </div>
    </div>
    
    @if($exam->status === 'for approval')
    <div class="container-fluid" style="max-width: 1400px; margin-bottom: 20px;">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-lock-fill me-2"></i>
            <div>
                <strong>Read-Only Mode:</strong> This exam is currently under review for approval. No changes can be made until the approval request is cancelled or the exam is approved/rejected.
            </div>
        </div>
    </div>
    @elseif($exam->status === 'approved')
    <div class="container-fluid" style="max-width: 1400px; margin-bottom: 20px;">
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>
                <strong>Approved:</strong> This exam has been approved and is now read-only. No further changes can be made.
            </div>
        </div>
    </div>
    @endif
    
    <div class="container-fluid" style="max-width: 1400px;">
        @forelse($exam->sections as $section)
        <!-- Section Card -->
        <div class="section-card" data-section-id="{{ $section->section_id }}">
            <div class="section-header">
                <h3 class="section-title">Section {{ $loop->iteration }} of {{ $exam->sections->count() }}</h3>
                @if($exam->status !== 'for approval' && $exam->status !== 'approved')
                <button class="section-delete-btn" onclick="deleteSection({{ $section->section_id }})">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
            </div>
            <div class="section-body">
                <input type="text" 
                       class="section-title-input" 
                       placeholder="(Write your exam title here or exam section title: eg. Part I)"
                       value="{{ $section->section_title ?? '' }}"
                       @if($exam->status === 'for approval' || $exam->status === 'approved')
                       readonly
                       style="cursor: not-allowed; color: #6b7280;"
                       @else
                       onchange="updateSection({{ $section->section_id }}, 'section_title', this.value)"
                       @endif>
                <textarea class="section-directions" 
                          placeholder="You can put your directions here."
                          @if($exam->status === 'for approval' || $exam->status === 'approved')
                          readonly
                          style="cursor: not-allowed; color: #6b7280;"
                          @else
                          onchange="updateSection({{ $section->section_id }}, 'section_directions', this.value)"
                          @endif>{{ $section->section_directions ?? '' }}</textarea>
            </div>
        </div>

        <!-- Question Cards -->
        @forelse($section->items->sortBy('order') as $item)
        <div class="question-wrapper">
            <div class="question-card" 
                 data-item-id="{{ $item->item_id }}"
                 onclick="setActiveQuestion(this)">
                <div class="question-header">
                    <h4 class="question-header-title">Exam Item {{ $loop->iteration }}</h4>
                    @if($exam->status !== 'for approval' && $exam->status !== 'approved')
                    <div class="question-header-actions">
                        <button class="question-header-btn" onclick="event.stopPropagation(); editQuestion({{ $item->item_id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($item->item_type !== 'enum' || ($item->enum_type ?? 'ordered') === 'ordered')
                        <button class="question-header-btn" onclick="event.stopPropagation(); duplicateQuestion({{ $exam->exam_id }}, {{ $item->item_id }})" title="Duplicate">
                            <i class="bi bi-files"></i>
                        </button>
                        @endif
                        <button class="question-header-btn" onclick="event.stopPropagation(); deleteQuestion({{ $exam->exam_id }}, {{ $item->item_id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    @endif
                </div>
                <div class="question-body">
                    @php
                        $displayType = strtoupper($item->item_type);
                        if ($item->item_type === 'enum') {
                            $enumType = $item->enum_type ?? 'ordered';
                            $displayType = $enumType === 'ordered' ? 'ORDERED ENUMERATION' : 'UNORDERED ENUMERATION';
                        }
                    @endphp
                    <div class="question-type-badge">{{ $displayType }}</div>
                    
                    <div class="question-text">{{ $item->question }}</div>

                    @if($item->item_type === 'mcq')
                        @php 
                            $options = json_decode($item->options, true); 
                            $answers = json_decode($item->answer, true);
                        @endphp
                        <ul class="options-list">
                            @foreach($options as $key => $option)
                            <li class="option-item">
                                <span class="option-text">{{ chr(65 + $key) }}. {{ $option }}</span>
                                @if(in_array($key, $answers ?? []))
                                <span class="correct-badge">✓ Correct</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @elseif($item->item_type === 'torf')
                        @php $answer = json_decode($item->answer, true); @endphp
                        <ul class="options-list">
                            <li class="option-item">
                                <span class="option-text">True</span>
                                @if($answer['correct'] === 'true')
                                <span class="correct-badge">✓ Correct</span>
                                @endif
                            </li>
                            <li class="option-item">
                                <span class="option-text">False</span>
                                @if($answer['correct'] === 'false')
                                <span class="correct-badge">✓ Correct</span>
                                @endif
                            </li>
                        </ul>
                    @elseif($item->item_type === 'iden')
                        <div class="expected-answer-box">
                            <strong>Expected Answer:</strong> 
                            <span>{{ $item->expected_answer }}</span>
                        </div>
                    @elseif($item->item_type === 'enum')
                        @php 
                            $answers = json_decode($item->answer, true);
                            $enumType = $item->enum_type ?? 'ordered';
                        @endphp
                        <ul class="options-list">
                            @foreach($answers as $index => $answer)
                            <li class="option-item">
                                @if($enumType === 'ordered')
                                    <span class="option-text">
                                        <strong>{{ $index + 1 }}.</strong> {{ $answer }}
                                    </span>
                                @else
                                    <span class="option-text">{{ $answer }}</span>
                                    <span style="color: #9ca3af; font-size: 0.9rem; margin-left: auto;">
                                        <i class="bi bi-grip-vertical"></i>
                                    </span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @elseif($item->item_type === 'essay')
                        <div class="expected-answer-box">
                            <span style="font-style: italic; color: #6b7280;">Essay question - Student will provide written answer</span>
                        </div>
                    @endif

                    <div class="points-display">
                        @if($item->item_type === 'enum' && ($item->enum_type ?? 'ordered') === 'unordered')
                            <strong>Point per correct answer:</strong> {{ $item->points_awarded }}
                        @else
                            <strong>Points:</strong> {{ $item->points_awarded }}
                        @endif
                    </div>
                </div>
            </div>
            
            @if($exam->status !== 'for approval' && $exam->status !== 'approved')
            <div class="floating-action-pane">
                <button class="floating-btn" title="Add Question" onclick="event.stopPropagation(); openQuestionModal('mcq', {{ $section->section_id }})">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="floating-btn" title="Duplicate" onclick="event.stopPropagation(); duplicateQuestion({{ $exam->exam_id }}, {{ $item->item_id }})">
                    <i class="bi bi-files"></i>
                </button>
                <button class="floating-btn" title="Move Up" onclick="event.stopPropagation(); moveQuestion({{ $item->item_id }}, 'up')">
                    <i class="bi bi-arrow-up"></i>
                </button>
                <button class="floating-btn" title="Move Down" onclick="event.stopPropagation(); moveQuestion({{ $item->item_id }}, 'down')">
                    <i class="bi bi-arrow-down"></i>
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="no-questions-yet">
            <i class="bi bi-question-circle"></i>
            <p>No questions yet. Click "Add" below to create your first question!</p>
        </div>
        @endforelse
        @empty
        <div class="no-questions-yet">
            <i class="bi bi-folder2-open"></i>
            <p>Creating default section...</p>
        </div>
        @endforelse
        
        <!-- Add Section -->
        @if($exam->status !== 'for approval' && $exam->status !== 'approved')
        <div class="add-section">
            <div class="add-dropdown-wrapper">
                <button class="add-main-btn" onclick="toggleAddDropdown()">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add</span>
                    <i class="bi bi-caret-down-fill" style="font-size: 0.8rem;"></i>
                </button>
                <div class="add-dropdown" id="addDropdown">
                    @if($exam->sections->count() > 0)
                    <button class="dropdown-item" onclick="addNewSection()">
                        <i class="bi bi-file-earmark-plus"></i>
                        <span>New Section</span>
                    </button>
                    <button class="dropdown-item" onclick="openQuestionModal('mcq', {{ $exam->sections->first()->section_id }})">
                        <i class="bi bi-ui-radios"></i>
                        <span>New MCQ</span>
                    </button>
                    <button class="dropdown-item" onclick="openQuestionModal('torf', {{ $exam->sections->first()->section_id }})">
                        <i class="bi bi-toggle-on"></i>
                        <span>New True or False</span>
                    </button>
                    <button class="dropdown-item" onclick="openQuestionModal('iden', {{ $exam->sections->first()->section_id }})">
                        <i class="bi bi-pencil-square"></i>
                        <span>New Identification</span>
                    </button>
                    <button class="dropdown-item" onclick="openQuestionModal('enum', {{ $exam->sections->first()->section_id }})">
                        <i class="bi bi-list-ol"></i>
                        <span>New Enumeration</span>
                    </button>
                    <button class="dropdown-item" onclick="openQuestionModal('essay', {{ $exam->sections->first()->section_id }})">
                        <i class="bi bi-textarea-t"></i>
                        <span>New Essay</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Exam Settings Modal -->
<div class="modal fade" id="examSettingsModal" tabindex="-1" aria-labelledby="examSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <div class="modal-title-custom">
                    <i class="bi bi-gear-fill"></i>
                    <span>Exam Settings</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="examSettingsForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label-custom">Exam Title</label>
                        <input type="text" class="form-control-custom" name="exam_title" id="settingsExamTitle" value="{{ $exam->exam_title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Exam Description</label>
                        <input type="text" class="form-control-custom" name="exam_desc" id="settingsExamDesc" value="{{ $exam->exam_desc }}">
                    </div>

                    <label class="form-label-custom" style="margin-bottom: 12px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; display: block;">Settings</label>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Subject</label>
                            <select class="form-control-custom" name="subject_id" id="settingsSubjectSelect" required onchange="loadClassesBySubjectSettings()">
                                <option value="">Select Subject</option>
                                @foreach(\App\Models\Subject::all() as $subject)
                                <option value="{{ $subject->subject_id }}" {{ $exam->subject_id == $subject->subject_id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Class Assignment</label>
                            <div class="class-dropdown">
                                <button type="button" class="form-control-custom" onclick="toggleClassDropdownSettings(event)" style="text-align: left; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                                    <span id="settingsSelectedClassesText">Select Classes</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <input type="hidden" name="class_ids" id="settingsSelectedClassesInput">
                                <div class="class-dropdown-list" id="settingsClassDropdownList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; max-height: 300px; margin-top: 4px;">
                                    <div class="class-search" style="padding: 8px; border-bottom: 1px solid #e5e7eb;">
                                        <input type="text" id="settingsClassSearchInput" placeholder="Search classes..." onkeyup="filterClassesSettings(this)" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem;">
                                    </div>
                                    <div class="class-list" id="settingsClassCheckboxList" style="max-height: 250px; overflow-y: auto; padding: 8px;">
                                        <div class="p-3 text-muted">Select subject first</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Term</label>
                            <select class="form-control-custom" name="term" id="settingsTermSelect">
                                <option value="prelim" {{ $exam->term == 'prelim' ? 'selected' : '' }}>Prelim</option>
                                <option value="midterm" {{ $exam->term == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                <option value="finals" {{ $exam->term == 'finals' ? 'selected' : '' }}>Finals</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Duration (minutes)</label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="number" class="form-control-custom" name="duration" id="settingsDuration" value="{{ $exam->duration }}" required min="1" style="flex: 1;">
                                <span style="color: #6b7280;">mins</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Schedule Start</label>
                            <input type="datetime-local" class="form-control-custom" name="schedule_start" id="settingsScheduleStart" value="{{ $exam->schedule_start ? $exam->schedule_start->format('Y-m-d\TH:i') : '' }}" required>
                            <small class="text-danger" id="settingsStartDateError" style="display: none;"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Schedule End</label>
                            <input type="datetime-local" class="form-control-custom" name="schedule_end" id="settingsScheduleEnd" value="{{ $exam->schedule_end ? $exam->schedule_end->format('Y-m-d\TH:i') : '' }}" required>
                            <small class="text-danger" id="settingsEndDateError" style="display: none;"></small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 24px;">Cancel</button>
                        <button type="submit" class="btn-save-question">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('instructor.exam.question-modal')

@endsection

@push('scripts')
<script>
const examId = {{ $exam->exam_id }};
let approvalStatus = '{{ $exam->status }}';
const isLocked = (approvalStatus === 'for approval' || approvalStatus === 'approved');

// Header Scroll Behavior
let lastScrollTop = 0;
const header = document.querySelector('.builder-header');
const scrollThreshold = 5; // Minimum scroll distance to trigger

window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    
    // Prevent negative scrolling
    if (currentScroll <= 0) {
        header.classList.remove('header-hidden');
        return;
    }
    
    // Check if scroll distance is significant enough
    if (Math.abs(currentScroll - lastScrollTop) < scrollThreshold) {
        return;
    }
    
    // Scrolling down
    if (currentScroll > lastScrollTop && currentScroll > 80) {
        header.classList.add('header-hidden');
    } 
    // Scrolling up
    else {
        header.classList.remove('header-hidden');
    }
    
    lastScrollTop = currentScroll;
}, false);

// Set Active Question
function setActiveQuestion(card) {
    if (isLocked) return;
    document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
}

// Toggle Add Dropdown
function toggleAddDropdown() {
    if (isLocked) return;
    document.getElementById('addDropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.add-dropdown-wrapper')) {
        document.getElementById('addDropdown').classList.remove('show');
    }
});

// Update Exam Title
function updateExamTitle() {
    if (isLocked) {
        alert('Cannot edit exam while it is under approval or approved.');
        return;
    }
    
    const title = document.getElementById('examTitle').value;
    
    fetch(`/instructor/exams/${examId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ exam_title: title })
    })
    .then(response => response.json())
    .then(result => {
        if (!result.success) {
            alert('Error updating exam title');
        }
    });
}

// Update Section
function updateSection(sectionId, field, value) {
    if (isLocked) {
        alert('Cannot edit exam while it is under approval or approved.');
        return;
    }
    
    fetch(`/instructor/exams/${examId}/sections/${sectionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ [field]: value })
    });
}

// Delete Section
function deleteSection(sectionId) {
    if (isLocked) {
        alert('Cannot delete sections while exam is under approval or approved.');
        return;
    }
    
    if (confirm('This will delete all questions in this section. Are you sure?')) {
        fetch(`/instructor/exams/${examId}/sections/${sectionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            }
        });
    }
}

// Duplicate Question
function duplicateQuestion(examId, itemId) {
    if (isLocked) {
        alert('Cannot duplicate questions while exam is under approval or approved.');
        return;
    }
    
    fetch(`/instructor/exams/${examId}/questions/${itemId}/duplicate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        }
    });
}

// Delete Question
function deleteQuestion(examId, itemId) {
    if (isLocked) {
        alert('Cannot delete questions while exam is under approval or approved.');
        return;
    }
    
    if (confirm('Are you sure you want to delete this question?')) {
        fetch(`/instructor/exams/${examId}/questions/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            }
        });
    }
}

// Move Question
function moveQuestion(itemId, direction) {
    if (isLocked) {
        alert('Cannot reorder questions while exam is under approval or approved.');
        return;
    }
    
    fetch(`/instructor/exams/${examId}/questions/reorder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ item_id: itemId, direction: direction })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        }
    });
}

// Add New Section
function addNewSection() {
    if (isLocked) {
        alert('Cannot add sections while exam is under approval or approved.');
        return;
    }
    
    fetch(`/instructor/exams/${examId}/sections`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            section_title: '',
            section_directions: ''
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        }
    });
    
    document.getElementById('addDropdown').classList.remove('show');
}

// Approval Button
document.getElementById('approvalBtn').addEventListener('click', function() {
    if (approvalStatus === 'draft') {
        if (confirm('Are you sure you want to request approval for this exam?')) {
            updateExamStatus('for approval');
        }
    } else if (approvalStatus === 'for approval') {
        if (confirm('Are you sure you want to cancel the approval request?')) {
            updateExamStatus('draft');
        }
    }
});

// Update Exam Status
function updateExamStatus(newStatus) {
    fetch(`/instructor/exams/${examId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            approvalStatus = newStatus;
            // Reload page to reflect read-only state
            location.reload();
        } else {
            alert(result.message || 'Error updating exam status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating exam status');
    });
}

// Update Approval Button UI
function updateApprovalButton() {
    const btn = document.getElementById('approvalBtn');
    const subtitle = document.getElementById('examSubtitle');
    
    if (approvalStatus === 'draft') {
        btn.textContent = 'Request for Approval';
        btn.classList.remove('pending', 'approved');
        subtitle.textContent = '{{ $exam->subject->subject_name ?? "Edit Exam." }}';
    } else if (approvalStatus === 'for approval') {
        btn.textContent = 'Cancel Approval Request';
        btn.classList.add('pending');
        btn.classList.remove('approved');
        subtitle.textContent = 'Waiting for approval...';
    } else if (approvalStatus === 'approved') {
        btn.textContent = 'Approved';
        btn.classList.add('approved');
        btn.classList.remove('pending');
        btn.disabled = true;
        subtitle.textContent = 'Read Only.';
    }
}

// Initialize button state on page load
updateApprovalButton();

// Open Exam Settings Modal
function openExamSettings() {
    if (isLocked) {
        alert('Cannot edit exam settings while it is under approval or approved.');
        return;
    }
    
    // Load existing exam data
    fetch(`/instructor/api/exams/${examId}/details`)
        .then(response => response.json())
        .then(data => {
            const exam = data.exam;
            
            // Populate form fields
            document.getElementById('settingsExamTitle').value = exam.exam_title;
            document.getElementById('settingsExamDesc').value = exam.exam_desc || '';
            document.getElementById('settingsSubjectSelect').value = exam.subject_id;
            document.getElementById('settingsTermSelect').value = exam.term;
            document.getElementById('settingsDuration').value = exam.duration;
            
            // Format and set dates
            if (exam.schedule_start) {
                document.getElementById('settingsScheduleStart').value = 
                    new Date(exam.schedule_start).toISOString().slice(0, 16);
            }
            if (exam.schedule_end) {
                document.getElementById('settingsScheduleEnd').value = 
                    new Date(exam.schedule_end).toISOString().slice(0, 16);
            }

            // Load classes for the selected subject
            loadClassesBySubjectSettings().then(() => {
                // Set selected classes
                const selectedClasses = exam.class_assignments || [];
                selectedClasses.forEach(classId => {
                    const checkbox = document.querySelector(`#settingsClassCheckboxList input[value="${classId}"]`);
                    if (checkbox) checkbox.checked = true;
                });
                updateSelectedClassesSettings();
            });
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('examSettingsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching exam details:', error);
            alert('Failed to load exam details');
        });
}

// Load Classes by Subject for Settings Modal
function loadClassesBySubjectSettings() {
    const subjectSelect = document.getElementById('settingsSubjectSelect');
    const subjectId = subjectSelect.value;
    
    const classCheckboxList = document.getElementById('settingsClassCheckboxList');
    const selectedClassesText = document.getElementById('settingsSelectedClassesText');
    const selectedClassesInput = document.getElementById('settingsSelectedClassesInput');
    
    // Reset selection
    selectedClassesText.textContent = 'Select Classes';
    selectedClassesInput.value = '';
    
    if (!subjectId) {
        classCheckboxList.innerHTML = '<div class="p-3 text-muted">Select subject first</div>';
        return Promise.reject();
    }
    
    classCheckboxList.innerHTML = '<div class="p-3 text-muted">Loading classes...</div>';
    
    return fetch(`/instructor/api/classes?subject_id=${subjectId}`)
        .then(response => response.json())
        .then(classes => {
            if (classes.length === 0) {
                classCheckboxList.innerHTML = '<div class="p-3 text-muted">No classes available for this subject</div>';
            } else {
                classCheckboxList.innerHTML = classes.map(cls => `
                    <div class="class-item" style="display: flex; align-items: center; padding: 8px; cursor: pointer; border-radius: 4px;">
                        <input type="checkbox" id="settings_class_${cls.class_id}" name="classes[]" 
                               value="${cls.class_id}" 
                               onchange="updateSelectedClassesSettings()"
                               class="form-check-input me-2">
                        <label for="settings_class_${cls.class_id}" class="form-check-label" style="cursor: pointer; flex: 1;">${cls.display}</label>
                    </div>
                `).join('');
            }
            return classes;
        })
        .catch(error => {
            console.error('Error loading classes:', error);
            classCheckboxList.innerHTML = '<div class="p-3 text-danger">Error loading classes. Please try again.</div>';
            throw error;
        });
}

// Toggle Class Dropdown for Settings Modal
function toggleClassDropdownSettings(event) {
    event.preventDefault();
    
    const dropdown = document.getElementById('settingsClassDropdownList');
    const subjectSelect = document.getElementById('settingsSubjectSelect');
    const subjectId = subjectSelect.value;
    
    if (!subjectId) {
        alert('Please select a subject first');
        return;
    }
    
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
        const searchInput = document.getElementById('settingsClassSearchInput');
        if (searchInput) searchInput.focus();
    } else {
        dropdown.style.display = 'none';
    }
}

// Update Selected Classes for Settings Modal
function updateSelectedClassesSettings() {
    const checkboxes = document.querySelectorAll('#settingsClassCheckboxList input[type="checkbox"]:checked');
    const selectedClassesText = document.getElementById('settingsSelectedClassesText');
    const selectedClassesInput = document.getElementById('settingsSelectedClassesInput');
    
    const selectedClasses = Array.from(checkboxes).map(cb => cb.value);
    const count = selectedClasses.length;
    
    // Update hidden input value
    selectedClassesInput.value = selectedClasses.join(',');
    
    // Update display text
    if (count === 0) {
        selectedClassesText.textContent = 'Select Classes';
    } else {
        selectedClassesText.textContent = `${count} Class${count > 1 ? 'es' : ''} Selected`;
    }
}

// Filter Classes for Settings Modal
function filterClassesSettings(input) {
    const filter = input.value.toLowerCase();
    const items = document.querySelectorAll('#settingsClassCheckboxList .class-item');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? '' : 'none';
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('settingsClassDropdownList');
    if (dropdown && !e.target.closest('.class-dropdown')) {
        dropdown.style.display = 'none';
    }
});

// Handle Exam Settings Form Submit
document.getElementById('examSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (isLocked) {
        alert('Cannot edit exam settings while it is under approval or approved.');
        return;
    }
    
    const formData = new FormData(this);
    const data = {
        exam_title: formData.get('exam_title'),
        exam_desc: formData.get('exam_desc'),
        subject_id: formData.get('subject_id'),
        term: formData.get('term'),
        duration: formData.get('duration'),
        schedule_start: formData.get('schedule_start'),
        schedule_end: formData.get('schedule_end'),
        class_ids: formData.get('class_ids')
    };
    
    fetch(`/instructor/exams/${examId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ...data,
            _method: 'PUT'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('examSettingsModal'));
            modal.hide();
            
            // Update the title in the header
            document.getElementById('examTitle').value = data.exam_title;
            
            // Show success message
            alert('Exam settings updated successfully!');
            
            // Optionally reload to reflect all changes
            location.reload();
        } else {
            alert('Error updating exam settings: ' + (result.message || result.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Failed to update exam settings: ' + error.message);
    });
});
</script>
@endpush