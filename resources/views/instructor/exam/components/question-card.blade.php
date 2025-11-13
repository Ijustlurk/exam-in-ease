{{-- Question Card Component --}}
{{-- Props: $item (ExamItem model) --}}

@php
    $questionId = $item->item_id ?? 'new';
    $questionText = $item->question ?? '';
    $itemType = $item->item_type ?? 'mcq';
    $points = $item->points_awarded ?? 1;
    $options = $item->options ? json_decode($item->options, true) : ['', '', '', ''];
    $answer = $item->answer ? json_decode($item->answer, true) : [];
    $expectedAnswer = $item->expected_answer ?? '';
    $enumType = $item->enum_type ?? 'ordered';
    
    // Ensure options is an array with at least 4 items for MCQ
    if (!is_array($options)) {
        $options = ['', '', '', ''];
    }
    while (count($options) < 4) {
        $options[] = '';
    }
    
    // Ensure answer is an array
    if (!is_array($answer)) {
        if ($itemType === 'torf') {
            $answer = ['correct' => 'true'];
        } elseif ($itemType === 'enum') {
            $answer = ['', '', ''];
        } else {
            $answer = [];
        }
    }
    
    // For enumeration, handle answers
    $enumAnswers = [];
    if ($itemType === 'enum' && is_array($answer)) {
        $enumAnswers = $answer;
    }
    if (count($enumAnswers) < 3) {
        while (count($enumAnswers) < 3) {
            $enumAnswers[] = '';
        }
    }
    
    // Type badge labels
    $typeLabels = [
        'mcq' => 'MCQ',
        'torf' => 'T/F',
        'iden' => 'IDEN',
        'enum' => 'ENUM',
        'essay' => 'ESSAY'
    ];
    $typeBadge = $typeLabels[$itemType] ?? 'MCQ';
@endphp

<div class="question-card" data-question-id="{{ $questionId }}" data-item-type="{{ $itemType }}" onclick="expandQuestionCard(this)">
    {{-- Collapsed State --}}
    <div class="question-card-collapsed">
        <div class="question-card-header-collapsed">
            <div class="question-type-badge badge-{{ $itemType }}">{{ $typeBadge }}</div>
            <div class="question-text-preview">
                {!! e($questionText) ?: 'Click to add question text' !!}
            </div>
            <div class="question-points-badge">{{ $points }} {{ $points == 1 ? 'point' : 'points' }}</div>
        </div>
        
        {{-- Question Type Specific Preview --}}
        @if($itemType === 'mcq' && count(array_filter($options)) > 0)
        <div class="question-options-preview">
            @foreach($options as $index => $option)
                @if($option)
                <div class="option-preview {{ in_array($index, $answer ?? []) ? 'correct' : '' }}">
                    <span class="option-letter">{{ chr(65 + $index) }}.</span>
                    <span class="option-text">{{ $option }}</span>
                    @if(in_array($index, $answer ?? []))
                    <i class="bi bi-check-circle-fill text-success"></i>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
        @elseif($itemType === 'torf' && isset($answer['correct']))
        <div class="question-answer-preview">
            <div class="answer-preview-item">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <span class="answer-text">Correct Answer: <strong>{{ ucfirst($answer['correct']) }}</strong></span>
            </div>
        </div>
        @elseif($itemType === 'iden' && $expectedAnswer)
        <div class="question-answer-preview">
            <div class="answer-preview-item">
                <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                <span class="answer-text">Expected: <strong>{{ $expectedAnswer }}</strong></span>
            </div>
        </div>
        @elseif($itemType === 'enum' && count(array_filter($enumAnswers)) > 0)
        <div class="question-answer-preview">
            <div class="answer-preview-label">
                <i class="bi bi-list-ol me-2"></i>
                <span>Expected Answers {{ $enumType === 'ordered' ? '(Ordered)' : '(Any Order)' }}:</span>
            </div>
            <ol class="enum-preview-list">
                @foreach($enumAnswers as $enumAnswer)
                    @if($enumAnswer)
                    <li>{{ $enumAnswer }}</li>
                    @endif
                @endforeach
            </ol>
        </div>
        @elseif($itemType === 'essay' && $expectedAnswer)
        @php
            $rubricData = json_decode($expectedAnswer, true);
        @endphp
        @if(is_array($rubricData) && count($rubricData) > 0)
        <div class="question-answer-preview">
            <div class="answer-preview-label">
                <i class="bi bi-clipboard-check me-2"></i>
                <span>Rubric ({{ count($rubricData) }} talking points):</span>
            </div>
            <div class="rubric-preview-list">
                @foreach(array_slice($rubricData, 0, 3) as $rubricItem)
                    @if(isset($rubricItem['talking_point']) && $rubricItem['talking_point'])
                    <div class="rubric-preview-item">
                        <span class="rubric-point">{{ $rubricItem['talking_point'] }}</span>
                        <span class="rubric-weight">{{ $rubricItem['weight'] ?? 0 }}pts</span>
                    </div>
                    @endif
                @endforeach
                @if(count($rubricData) > 3)
                <div class="rubric-more">+{{ count($rubricData) - 3 }} more...</div>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- Expanded State --}}
    <div class="question-card-expanded" onclick="event.stopPropagation()">
        <form class="question-edit-form">
            {{-- Question Type Selector --}}
            <div class="form-group">
                <label class="form-label fw-bold">Question Type</label>
                <div class="question-type-buttons">
                    <button type="button" class="type-btn {{ $itemType === 'mcq' ? 'active' : '' }}" data-type="mcq" onclick="switchQuestionType(this, 'mcq')">
                        <i class="bi bi-ui-radios"></i>
                        <span>Multiple Choice</span>
                    </button>
                    <button type="button" class="type-btn {{ $itemType === 'torf' ? 'active' : '' }}" data-type="torf" onclick="switchQuestionType(this, 'torf')">
                        <i class="bi bi-check2-square"></i>
                        <span>True/False</span>
                    </button>
                    <button type="button" class="type-btn {{ $itemType === 'iden' ? 'active' : '' }}" data-type="iden" onclick="switchQuestionType(this, 'iden')">
                        <i class="bi bi-pencil-square"></i>
                        <span>Identification</span>
                    </button>
                    <button type="button" class="type-btn {{ $itemType === 'enum' ? 'active' : '' }}" data-type="enum" onclick="switchQuestionType(this, 'enum')">
                        <i class="bi bi-list-ol"></i>
                        <span>Enumeration</span>
                    </button>
                    <button type="button" class="type-btn {{ $itemType === 'essay' ? 'active' : '' }}" data-type="essay" onclick="switchQuestionType(this, 'essay')">
                        <i class="bi bi-textarea-t"></i>
                        <span>Essay</span>
                    </button>
                </div>
            </div>

            {{-- Question Text --}}
            <div class="form-group mt-3">
                <label class="form-label fw-bold">Question</label>
                <textarea name="question" class="form-control question-input" rows="3" placeholder="Enter your question here..." required>{{ $questionText }}</textarea>
            </div>

            {{-- Points --}}
            <div class="form-group mt-3">
                <label class="form-label fw-bold">Points <span class="essay-points-note">(auto-calculated for Essay)</span></label>
                <input type="number" name="points_awarded" class="form-control points-input" min="1" value="{{ $points }}" required style="width: 120px;" {{ $itemType === 'essay' ? 'readonly' : '' }}>
            </div>

            {{-- MCQ Fields --}}
            <div class="question-type-fields mcq-fields {{ $itemType === 'mcq' ? '' : 'd-none' }}">
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Answer Options</label>
                    <div class="mcq-options-container">
                        @foreach($options as $index => $option)
                        <div class="mcq-option-row">
                            <div class="option-letter-label">{{ chr(65 + $index) }}</div>
                            <input type="text" class="form-control option-input" placeholder="Option {{ chr(65 + $index) }}" value="{{ $option }}" data-index="{{ $index }}">
                            <label class="correct-checkbox">
                                <input type="checkbox" class="correct-answer-checkbox" value="{{ $index }}" {{ in_array($index, $answer ?? []) ? 'checked' : '' }}>
                                <span>Correct</span>
                            </label>
                            @if($index >= 2)
                            <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMCQOption(this)">
                        <i class="bi bi-plus-circle"></i> Add Option
                    </button>
                </div>
            </div>

            {{-- True/False Fields --}}
            <div class="question-type-fields torf-fields {{ $itemType === 'torf' ? '' : 'd-none' }}">
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Correct Answer</label>
                    <div class="torf-options">
                        <label class="torf-option">
                            <input type="radio" name="torf_answer_{{ $questionId }}" value="true" {{ (isset($answer['correct']) && $answer['correct'] === 'true') || !isset($answer['correct']) ? 'checked' : '' }}>
                            <span>True</span>
                        </label>
                        <label class="torf-option">
                            <input type="radio" name="torf_answer_{{ $questionId }}" value="false" {{ isset($answer['correct']) && $answer['correct'] === 'false' ? 'checked' : '' }}>
                            <span>False</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Identification Fields --}}
            <div class="question-type-fields iden-fields {{ $itemType === 'iden' ? '' : 'd-none' }}">
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Expected Answer</label>
                    <input type="text" name="expected_answer" class="form-control" placeholder="Enter the expected answer" value="{{ $expectedAnswer }}">
                    <small class="form-text text-muted">The student's answer will be compared to this (case-insensitive)</small>
                </div>
            </div>

            {{-- Enumeration Fields --}}
            <div class="question-type-fields enum-fields {{ $itemType === 'enum' ? '' : 'd-none' }}">
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Enumeration Type</label>
                    <div class="enum-type-options">
                        <label class="enum-type-option">
                            <input type="radio" name="enum_type_{{ $questionId }}" value="ordered" {{ $enumType === 'ordered' ? 'checked' : '' }}>
                            <span>Ordered (sequence matters)</span>
                        </label>
                        <label class="enum-type-option">
                            <input type="radio" name="enum_type_{{ $questionId }}" value="unordered" {{ $enumType === 'unordered' ? 'checked' : '' }}>
                            <span>Unordered (any order acceptable)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Expected Answers</label>
                    <div class="enum-answers-container">
                        @foreach($enumAnswers as $index => $enumAnswer)
                        <div class="enum-answer-row">
                            <div class="enum-number">{{ $index + 1 }}.</div>
                            <input type="text" class="form-control enum-answer-input" placeholder="Answer {{ $index + 1 }}" value="{{ $enumAnswer }}" data-index="{{ $index }}">
                            @if($index >= 2)
                            <button type="button" class="btn-remove-enum" onclick="removeEnumAnswer(this)" title="Remove answer">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEnumAnswer(this)">
                        <i class="bi bi-plus-circle"></i> Add Answer
                    </button>
                </div>
            </div>

            {{-- Essay Fields --}}
            <div class="question-type-fields essay-fields {{ $itemType === 'essay' ? '' : 'd-none' }}">
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Rubric</label>
                    <div class="rubric-header">
                        <div class="rubric-header-field">Talking Points</div>
                        <div class="rubric-header-weight">Weight</div>
                        <div class="rubric-header-action"></div>
                    </div>
                    <div class="essay-rubrics-container">
                        @php
                            $rubricData = [];
                            if ($itemType === 'essay' && $expectedAnswer) {
                                $decoded = json_decode($expectedAnswer, true);
                                if (is_array($decoded)) {
                                    $rubricData = $decoded;
                                }
                            }
                            // Ensure at least 2 rubric items
                            if (count($rubricData) < 2) {
                                $rubricData = [
                                    ['talking_point' => '', 'weight' => 0],
                                    ['talking_point' => '', 'weight' => 0]
                                ];
                            }
                        @endphp
                        @foreach($rubricData as $index => $rubricItem)
                        <div class="essay-rubric-row">
                            <input type="text" class="form-control essay-talking-point" placeholder="Write a talking point here..." value="{{ $rubricItem['talking_point'] ?? '' }}" data-index="{{ $index }}" required>
                            <input type="number" class="form-control essay-weight" placeholder="0" min="1" value="{{ $rubricItem['weight'] ?? 0 }}" data-index="{{ $index }}" oninput="calculateEssayPointsInline(this)" required>
                            @if($index >= 2)
                            <button type="button" class="btn-remove-rubric-inline" onclick="removeEssayRubricInline(this)" title="Remove rubric">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEssayRubricInline(this)">
                        <i class="bi bi-plus-circle"></i> Add Talking Point
                    </button>
                </div>
                <div class="form-group mt-3">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Total points will be calculated from the sum of all weights. Essay questions can be graded manually or using AI.
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="form-actions mt-4">
                <button type="button" class="btn btn-secondary" onclick="collapseQuestionCard(this, false)">
                    <i class="bi bi-x-lg"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveQuestionInline(this, true)">
                    <i class="bi bi-check-lg"></i> Save Question
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Question Card Styles */
.question-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 3px;
    margin-top: 3px;
    transition: all 0.3s ease;
    cursor: pointer;
    overflow: hidden; /* Prevent content overflow */
}

.question-card:hover {
    border-color: #9ca3af;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Collaborators can't expand cards */
body.is-collaborator .question-card {
    cursor: default;
}

body.is-collaborator .question-card:hover {
    border-color: #9ca3af;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.question-card.expanded {
    border-color: #6b9aac;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    cursor: default;
}

/* Collapsed State */
.question-card-collapsed {
    padding: 20px;
    max-width: 100%;
    overflow: hidden;
}

.question-card.expanded .question-card-collapsed {
    display: none;
}

.question-card-header-collapsed {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    width: 100%;
    min-width: 0;
}

.question-type-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    flex-shrink: 0;
    min-width: 50px;
}

.badge-mcq { background: #dbeafe; color: #1e40af; }
.badge-torf { background: #d1fae5; color: #065f46; }
.badge-iden { background: #fef3c7; color: #92400e; }
.badge-enum { background: #e0e7ff; color: #3730a3; }
.badge-essay { background: #fce7f3; color: #9f1239; }

.question-text-preview {
    flex: 1 1 auto;
    font-size: 0.9rem;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
}

.question-points-badge {
    padding: 3px 8px;
    background: #f3f4f6;
    border-radius: 16px;
    font-size: 0.7rem;
    color: #6b7280;
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
    min-width: 60px;
    text-align: center;
}

.question-options-preview {
    padding-left: 24px;
}

.option-preview {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    font-size: 0.9rem;
    color: #6b7280;
}

.option-preview.correct {
    color: #059669;
    font-weight: 500;
}

.option-letter {
    font-weight: 600;
    min-width: 24px;
}

/* Answer Preview for TORF, IDEN, ENUM, ESSAY */
.question-answer-preview {
    padding-left: 24px;
    margin-top: 8px;
}

.answer-preview-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: #f0fdf4;
    border-left: 3px solid #10b981;
    border-radius: 4px;
    font-size: 0.9rem;
    color: #374151;
}

.answer-preview-label {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 8px;
}

.enum-preview-list {
    margin: 0;
    padding-left: 20px;
    font-size: 0.9rem;
    color: #374151;
}

.enum-preview-list li {
    padding: 2px 0;
}

.rubric-preview-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rubric-preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 10px;
    background: #fef3c7;
    border-radius: 4px;
    font-size: 0.85rem;
    gap: 8px;
}

.rubric-point {
    flex: 1;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rubric-weight {
    flex-shrink: 0;
    font-weight: 600;
    color: #92400e;
    background: #fde68a;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

.rubric-more {
    font-size: 0.8rem;
    color: #6b7280;
    font-style: italic;
    padding-left: 10px;
}

/* Expanded State */
.question-card-expanded {
    display: none;
    padding: 24px;
    overflow-x: hidden; /* Prevent horizontal overflow */
    max-width: 100%;
}

.question-card.expanded .question-card-expanded {
    display: block;
}

/* Ensure form doesn't overflow */
.question-edit-form {
    max-width: 100%;
    overflow: hidden;
}

.question-edit-form .form-control {
    max-width: 100%;
    box-sizing: border-box;
}

/* Type Selector Buttons */
.question-type-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.type-btn {
    flex: 1;
    min-width: 140px;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
}

.type-btn i {
    font-size: 1.5rem;
    color: #6b7280;
}

.type-btn span {
    font-size: 0.85rem;
    color: #374151;
    font-weight: 500;
}

.type-btn:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}

.type-btn.active {
    border-color: #6b9aac;
    background: #e8f4f8;
}

.type-btn.active i,
.type-btn.active span {
    color: #6b9aac;
}

/* MCQ Options */
.mcq-options-container {
    max-width: 100%;
    overflow: hidden;
}

.mcq-option-row {
    display: grid;
    grid-template-columns: 30px 1fr auto auto;
    gap: 8px;
    margin-bottom: 12px;
    align-items: center;
}

.option-letter-label {
    width: 30px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 6px;
    font-weight: 600;
    color: #374151;
}

.option-input {
    min-width: 0; /* Allow input to shrink */
    width: 100%;
}

.correct-checkbox {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    padding: 6px 10px;
    background: #f9fafb;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
    white-space: nowrap;
    font-size: 0.85rem;
    max-width: fit-content;
}

.correct-checkbox input[type="checkbox"] {
    cursor: pointer;
    margin: 0;
    flex-shrink: 0;
}

.correct-checkbox span {
    font-size: 0.85rem;
    color: #6b7280;
}

.btn-remove-option,
.btn-remove-enum {
    background: none;
    border: none;
    color: #ef4444;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
    width: 32px;
    height: 32px;
    min-width: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btn-remove-option:hover,
.btn-remove-enum:hover {
    color: #dc2626;
}

/* True/False Options */
.torf-options {
    display: flex;
    gap: 16px;
}

.torf-option {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.torf-option input[type="radio"] {
    cursor: pointer;
}

.torf-option:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}

.torf-option:has(input:checked) {
    border-color: #6b9aac;
    background: #e8f4f8;
}

.torf-option span {
    font-weight: 500;
    font-size: 1rem;
}

/* Enumeration */
.enum-type-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.enum-type-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.enum-type-option:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}

.enum-type-option:has(input:checked) {
    border-color: #6b9aac;
    background: #e8f4f8;
}

.enum-answers-container {
    max-width: 100%;
    overflow: hidden;
}

.enum-answer-row {
    display: grid;
    grid-template-columns: 30px 1fr auto;
    gap: 8px;
    margin-bottom: 12px;
    align-items: center;
}

.enum-number {
    width: 30px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 6px;
    font-weight: 600;
    color: #374151;
}

.enum-answer-input {
    min-width: 0; /* Allow input to shrink */
    width: 100%;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

/* Essay Rubric Styles */
.rubric-header {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 8px;
    padding: 8px 12px;
    background: #f3f4f6;
    border-radius: 6px;
    margin-bottom: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
}

.rubric-header-field {
    grid-column: 1;
}

.rubric-header-weight {
    grid-column: 2;
    text-align: center;
    width: 80px;
}

.rubric-header-action {
    grid-column: 3;
    width: 32px;
}

.essay-rubrics-container {
    max-width: 100%;
    overflow: hidden;
}

.essay-rubric-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 8px;
    margin-bottom: 12px;
    align-items: center;
}

.essay-talking-point {
    min-width: 0;
    width: 100%;
}

.essay-weight {
    width: 80px;
}

.btn-remove-rubric-inline {
    background: none;
    border: none;
    color: #ef4444;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-remove-rubric-inline:hover {
    color: #dc2626;
}

.essay-points-note {
    font-size: 0.75rem;
    font-weight: 400;
    color: #6b7280;
    display: none;
}

.essay-fields:not(.d-none) ~ .form-group .essay-points-note {
    display: inline;
}

.essay-fields:not(.d-none) ~ .form-group .points-input {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .question-type-buttons {
        flex-direction: column;
    }
    
    .type-btn {
        min-width: 100%;
    }
    
    /* Smaller badges on mobile */
    .question-type-badge {
        font-size: 0.65rem;
        padding: 3px 8px;
        min-width: 40px;
    }
    
    .question-points-badge {
        font-size: 0.7rem;
        padding: 3px 8px;
        min-width: 55px;
    }
    
    .question-text-preview {
        font-size: 0.9rem;
    }
    
    /* Stack MCQ options on smaller screens */
    .mcq-option-row {
        grid-template-columns: 30px 1fr;
        grid-template-areas: 
            "letter input"
            "checkbox checkbox"
            "remove remove";
    }
    
    .option-letter-label {
        grid-area: letter;
    }
    
    .option-input {
        grid-area: input;
    }
    
    .correct-checkbox {
        grid-area: checkbox;
        justify-content: center;
        margin-top: 4px;
    }
    
    .btn-remove-option {
        grid-area: remove;
        width: 100%;
        justify-content: center;
        margin-top: 4px;
    }
    
    /* Stack enum answers on smaller screens */
    .enum-answer-row {
        grid-template-columns: 30px 1fr;
        grid-template-areas:
            "number input"
            "remove remove";
    }
    
    .enum-number {
        grid-area: number;
    }
    
    .enum-answer-input {
        grid-area: input;
    }
    
    .btn-remove-enum {
        grid-area: remove;
        width: 100%;
        justify-content: center;
        margin-top: 4px;
    }
}
</style>
