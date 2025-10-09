@extends('layouts.Instructor.app')

@section('content')
<style>
    body {
        background-color: #e8eef2;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .exam-builder-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }
    /* Header */
    .exam-header {
        background: linear-gradient(135deg, #6ba5b3 0%, #5a94a6 100%);
        border-radius: 12px;
        padding: 20px 30px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .exam-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .exam-title-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .exam-icon {
        font-size: 2.5rem;
    }
    .exam-title-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        border-bottom: 2px solid transparent;
        padding: 5px 0;
        transition: border-color 0.3s;
    }
    .exam-title-input:focus {
        outline: none;
        border-bottom-color: white;
    }
    .exam-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .header-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    .header-icon-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.3s;
    }
    .header-icon-btn:hover {
        background: rgba(255,255,255,0.3);
    }
    .request-approval-btn {
        background: rgba(255,255,255,0.25);
        border: 2px solid white;
        color: white;
        padding: 10px 24px;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .request-approval-btn:hover {
        background: white;
        color: #6ba5b3;
    }
    
    /* Section Card */
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .section-header {
        background: #6ba5b3;
        color: white;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: -24px -24px 20px -24px;
    }
    .section-title {
        font-weight: 600;
        font-size: 1rem;
    }
    .section-input {
        border: none;
        border-bottom: 2px solid #e5e7eb;
        padding: 12px 0;
        width: 100%;
        font-size: 0.95rem;
        margin-bottom: 12px;
        transition: border-color 0.3s;
    }
    .section-input:focus {
        outline: none;
        border-bottom-color: #6ba5b3;
    }
    .section-input::placeholder {
        color: #9ca3af;
        font-style: italic;
    }
    
    /* Question Card */
    .question-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        position: relative;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    .question-card.active {
        border-color: #6ba5b3;
        box-shadow: 0 4px 12px rgba(107,165,187,0.2);
    }
    .question-card.view-mode {
        cursor: pointer;
    }
    .question-card.view-mode:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .question-header {
        background: #6ba5b3;
        color: white;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: -24px -24px 20px -24px;
    }
    .question-actions {
        display: flex;
        gap: 12px;
    }
    .question-action-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s;
    }
    .question-action-btn:hover {
        background: rgba(255,255,255,0.3);
    }
    .question-type-badge {
        position: absolute;
        top: 90px;
        right: 24px;
        background: #f3f4f6;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }
    .question-content {
        font-size: 0.95rem;
        color: #374151;
        margin-bottom: 16px;
    }
    .options-list {
        list-style: none;
        padding: 0;
        margin: 16px 0;
    }
    .option-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 8px;
        gap: 12px;
    }
    .option-text {
        flex: 1;
        color: #374151;
    }
    .correct-badge {
        background: #d1fae5;
        color: #065f46;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .points-display {
        text-align: right;
        color: #6b7280;
        font-size: 0.9rem;
        margin-top: 16px;
    }
    .points-display strong {
        color: #374151;
    }
    
    /* Floating Action Pane */
    .floating-action-pane {
        position: absolute;
        right: -60px;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 8px;
        display: none;
        flex-direction: column;
        gap: 8px;
        z-index: 10;
    }
    .question-card.active .floating-action-pane {
        display: flex;
    }
    .float-action-btn {
        width: 44px;
        height: 44px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        color: #6b7280;
        font-size: 1.2rem;
    }
    .float-action-btn:hover {
        background: #6ba5b3;
        color: white;
        border-color: #6ba5b3;
    }
    
    /* Add Button */
    .add-section-wrapper {
        text-align: center;
        margin: 32px 0;
    }
    .add-dropdown {
        position: relative;
        display: inline-block;
    }
    .add-btn {
        background: white;
        border: 2px solid #d1d5db;
        color: #6b7280;
        padding: 12px 32px;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .add-btn:hover {
        border-color: #6ba5b3;
        color: #6ba5b3;
    }
    .dropdown-menu-custom {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        min-width: 240px;
        display: none;
        margin-top: 8px;
        overflow: hidden;
        z-index: 100;
    }
    .dropdown-menu-custom.show {
        display: block;
    }
    .dropdown-item-custom {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        color: #374151;
    }
    .dropdown-item-custom:hover {
        background: #f9fafb;
    }
    .dropdown-item-custom i {
        font-size: 1.1rem;
        color: #6b7280;
    }

    /* Modal Styles */
    .modal-header-custom {
        background: #6ba5b3;
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
    }
    .modal-title-custom {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .form-label-custom {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .form-control-custom {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        transition: border-color 0.3s;
    }
    .form-control-custom:focus {
        outline: none;
        border-color: #6ba5b3;
        box-shadow: 0 0 0 3px rgba(107,165,187,0.1);
    }
    .option-input-group {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 12px;
    }
    .option-input-group input {
        flex: 1;
    }
    .correct-checkbox {
        width: 24px;
        height: 24px;
        cursor: pointer;
        accent-color: #10b981;
    }
    .btn-add-option {
        background: #6ba5b3;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-add-option:hover {
        background: #5a94a6;
    }
    .btn-save-question {
        background: #6ba5b3;
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-save-question:hover {
        background: #5a94a6;
    }
</style>

<div class="exam-builder-container">
    <!-- Header -->
    <div class="exam-header">
        <div class="exam-header-content">
            <div class="exam-title-section">
                <i class="bi bi-clipboard2 exam-icon"></i>
                <div>
                    <input type="text" 
                           class="exam-title-input" 
                           value="{{ $exam->exam_title }}" 
                           id="examTitle"
                           onchange="updateExamTitle()">
                    <div class="exam-subtitle">Edit Exam.</div>
                </div>
            </div>
            <div class="header-actions">
                <button class="header-icon-btn" title="Edit"><i class="bi bi-pencil-square"></i></button>
                <button class="header-icon-btn" title="Settings"><i class="bi bi-gear"></i></button>
                <button class="header-icon-btn" title="Download"><i class="bi bi-download"></i></button>
                <button class="request-approval-btn">Request for Approval</button>
            </div>
        </div>
    </div>

    <!-- Sections and Questions -->
    <div id="examContent">
        @foreach($exam->sections as $section)
        <!-- Section Card -->
        <div class="section-card" data-section-id="{{ $section->section_id }}">
            <div class="section-header">
                <span class="section-title">Section {{ $loop->iteration }} of {{ $exam->sections->count() }}</span>
                <button class="question-action-btn" onclick="deleteSection({{ $section->section_id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <input type="text" 
                   class="section-input" 
                   placeholder="(Write your exam title here or exam section title: eg. Part I)"
                   value="{{ $section->section_title }}"
                   onchange="updateSection({{ $section->section_id }}, 'section_title', this.value)">
            <input type="text" 
                   class="section-input" 
                   placeholder="You can put your directions here."
                   value="{{ $section->section_directions }}"
                   onchange="updateSection({{ $section->section_id }}, 'section_directions', this.value)">
        </div>

        <!-- Question Cards -->
        @foreach($section->items->sortBy('order') as $item)
        <div class="question-card view-mode" 
             data-item-id="{{ $item->item_id }}"
             onclick="makeCardActive(this, event)">
            <div class="question-header">
                <span>Exam Item {{ $loop->iteration }}</span>
                <div class="question-actions">
                    <button class="question-action-btn" onclick="event.stopPropagation(); editQuestion({{ $item->item_id }})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="question-action-btn" onclick="event.stopPropagation(); duplicateQuestion({{ $exam->exam_id }}, {{ $item->item_id }})">
                        <i class="bi bi-files"></i>
                    </button>
                    <button class="question-action-btn" onclick="event.stopPropagation(); deleteQuestion({{ $exam->exam_id }}, {{ $item->item_id }})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div class="question-type-badge">{{ strtoupper($item->item_type) }}</div>

            <div class="question-content">
                <strong>{{ $item->question }}</strong>
            </div>

            @if($item->item_type === 'mcq')
                @php $options = json_decode($item->options, true); @endphp
                @php $answers = json_decode($item->answer, true); @endphp
                <ul class="options-list">
                    @foreach($options as $key => $option)
                    <li class="option-item">
                        <span class="option-text">{{ $option }}</span>
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
                <div class="expected-answer" style="background: #f9fafb; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <strong>Expected Answer:</strong> {{ $item->expected_answer }}
                </div>
            @elseif($item->item_type === 'enum')
                @php $answers = json_decode($item->answer, true); @endphp
                <ul class="options-list">
                    @foreach($answers as $index => $answer)
                    <li class="option-item">
                        <span style="font-weight: 600; margin-right: 8px;">{{ $index + 1 }}.</span>
                        <span class="option-text">{{ $answer }}</span>
                    </li>
                    @endforeach
                </ul>
            @endif

            <div class="points-display">
                <strong>Points:</strong> {{ $item->points_awarded }}
            </div>

            <!-- Floating Action Pane -->
            <div class="floating-action-pane">
                <button class="float-action-btn" 
                        onclick="event.stopPropagation(); openQuestionModal('mcq', {{ $section->section_id }})"
                        title="Add Question">
                    <i class="bi bi-plus-circle"></i>
                </button>
                <button class="float-action-btn" 
                        onclick="event.stopPropagation(); duplicateQuestion({{ $exam->exam_id }}, {{ $item->item_id }})"
                        title="Duplicate">
                    <i class="bi bi-files"></i>
                </button>
                <button class="float-action-btn" 
                        onclick="event.stopPropagation(); reorderQuestion({{ $exam->exam_id }}, {{ $item->item_id }}, 'up')"
                        title="Move Up">
                    <i class="bi bi-arrow-up"></i>
                </button>
                <button class="float-action-btn" 
                        onclick="event.stopPropagation(); reorderQuestion({{ $exam->exam_id }}, {{ $item->item_id }}, 'down')"
                        title="Move Down">
                    <i class="bi bi-arrow-down"></i>
                </button>
            </div>
        </div>
        @endforeach
        @endforeach
    </div>

    <!-- Add Button -->
    <div class="add-section-wrapper">
        <div class="add-dropdown">
            <button class="add-btn" onclick="toggleAddDropdown()">
                <i class="bi bi-plus-circle"></i>
                <span>Add</span>
                <i class="bi bi-caret-down"></i>
            </button>
            <div class="dropdown-menu-custom" id="addDropdown">
                <button class="dropdown-item-custom" onclick="addNewSection()">
                    <i class="bi bi-layout-text-sidebar"></i>
                    <span>New Section</span>
                </button>
                <button class="dropdown-item-custom" onclick="openQuestionModal('mcq', {{ $exam->sections->first()->section_id ?? null }})">
                    <i class="bi bi-question-circle"></i>
                    <span>New MCQ</span>
                </button>
                <button class="dropdown-item-custom" onclick="openQuestionModal('torf', {{ $exam->sections->first()->section_id ?? null }})">
                    <i class="bi bi-check2-circle"></i>
                    <span>New True or False</span>
                </button>
                <button class="dropdown-item-custom" onclick="openQuestionModal('iden', {{ $exam->sections->first()->section_id ?? null }})">
                    <i class="bi bi-pencil-square"></i>
                    <span>New Identification</span>
                </button>
                <button class="dropdown-item-custom" onclick="openQuestionModal('enum', {{ $exam->sections->first()->section_id ?? null }})">
                    <i class="bi bi-list-ol"></i>
                    <span>New Enumeration</span>
                </button>
                <button class="dropdown-item-custom" onclick="openQuestionModal('essay', {{ $exam->sections->first()->section_id ?? null }})">
                    <i class="bi bi-textarea-t"></i>
                    <span>New Essay</span>
                </button>
            </div>
        </div>
    </div>
</div>
@include ('instructor.exam.question-model');
<!-- Continue in next artifact due to length... -->
@endsection