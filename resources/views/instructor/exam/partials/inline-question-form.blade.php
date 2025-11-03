{{-- Inline Question Form Component (replaces modals) --}}
<div class="inline-question-form" id="inlineQuestionForm{{ $uniqueId ?? 'new' }}"
     data-section-id="{{ $sectionId ?? '' }}"
     data-question-id="{{ $questionId ?? '' }}"
     data-after-item-id="{{ $afterItemId ?? '' }}"
     style="display: none;">

    <div class="question-form-container">
        <form class="question-form" onsubmit="return false;">
            @csrf

            {{-- Question Type Selector --}}
            <div class="form-header">
                <div class="form-title">
                    <i class="bi bi-question-circle"></i>
                    <span class="question-type-label">{{ $isEdit ?? false ? 'Edit' : 'New' }} Question</span>
                </div>
                <div class="form-header-actions">
                    <select class="question-type-selector" name="item_type" onchange="switchQuestionTypeInline(this)" required>
                        <option value="mcq">Multiple Choice</option>
                        <option value="torf">True or False</option>
                        <option value="iden">Identification</option>
                        <option value="enum">Enumeration</option>
                        <option value="essay">Essay</option>
                    </select>
                    <button type="button" class="btn-close-inline" onclick="closeInlineQuestionForm(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Question Text (Common to all types) --}}
            <div class="form-group">
                <label class="form-label">Question Text <span class="text-danger">*</span></label>
                <textarea
                    class="form-input"
                    name="question"
                    rows="3"
                    placeholder="Enter your question here..."
                    required
                ></textarea>
            </div>

            {{-- MCQ Options --}}
            <div class="question-type-section mcq-section">
                <label class="form-label">Options</label>
                <div class="options-list" id="mcqOptionsList">
                    {{-- Options will be dynamically added --}}
                </div>
                <button type="button" class="btn-add-option" onclick="addMCQOption(this)">
                    <i class="bi bi-plus-circle"></i> Add Option
                </button>
            </div>

            {{-- True/False Options --}}
            <div class="question-type-section torf-section" style="display: none;">
                <label class="form-label">Correct Answer</label>
                <div class="torf-options">
                    <label class="radio-option">
                        <input type="radio" name="torf_answer" value="true" required>
                        <span>True</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="torf_answer" value="false" required>
                        <span>False</span>
                    </label>
                </div>
            </div>

            {{-- Identification Answer --}}
            <div class="question-type-section iden-section" style="display: none;">
                <label class="form-label">Expected Answer <span class="text-danger">*</span></label>
                <input
                    type="text"
                    class="form-input"
                    name="iden_answer"
                    placeholder="Enter the correct answer..."
                >
            </div>

            {{-- Enumeration Answers --}}
            <div class="question-type-section enum-section" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Enumeration Type</label>
                    <select class="form-input" name="enum_type">
                        <option value="ordered">Ordered (must be in sequence)</option>
                        <option value="unordered">Unordered (any order)</option>
                    </select>
                </div>
                <label class="form-label">Answers</label>
                <div class="enum-list" id="enumAnswersList">
                    {{-- Enum answers will be dynamically added --}}
                </div>
                <button type="button" class="btn-add-option" onclick="addEnumAnswer(this)">
                    <i class="bi bi-plus-circle"></i> Add Answer
                </button>
            </div>

            {{-- Essay Rubric --}}
            <div class="question-type-section essay-section" style="display: none;">
                <label class="form-label">Rubric / Talking Points</label>
                <div class="rubric-list" id="essayRubricList">
                    {{-- Rubric items will be dynamically added --}}
                </div>
                <button type="button" class="btn-add-option" onclick="addRubricItem(this)">
                    <i class="bi bi-plus-circle"></i> Add Talking Point
                </button>
            </div>

            {{-- Points Awarded --}}
            <div class="form-group">
                <label class="form-label">Points <span class="text-danger">*</span></label>
                <input
                    type="number"
                    class="form-input points-input"
                    name="points_awarded"
                    min="1"
                    value="1"
                    required
                >
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeInlineQuestionForm(this)">
                    Cancel
                </button>
                <button type="submit" class="btn-save" onclick="saveInlineQuestion(this)">
                    <i class="bi bi-check-circle"></i>
                    {{ $isEdit ?? false ? 'Update' : 'Save' }} Question
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.inline-question-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    margin: 16px 0;
    overflow: hidden;
    animation: slideDown 0.3s ease-out;
    border: 2px solid #6b9aac;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.question-form-container {
    padding: 24px;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.form-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
}

.form-title i {
    color: #6b9aac;
    font-size: 1.4rem;
}

.form-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.question-type-selector {
    padding: 8px 12px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #374151;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.question-type-selector:focus {
    outline: none;
    border-color: #6b9aac;
    box-shadow: 0 0 0 3px rgba(107, 154, 172, 0.1);
}

.btn-close-inline {
    background: transparent;
    border: none;
    font-size: 1.2rem;
    color: #6b7280;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-close-inline:hover {
    background: #f3f4f6;
    color: #ef4444;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s;
    font-family: inherit;
}

.form-input:focus {
    outline: none;
    border-color: #6b9aac;
    box-shadow: 0 0 0 3px rgba(107, 154, 172, 0.1);
}

textarea.form-input {
    resize: vertical;
    min-height: 80px;
}

.points-input {
    max-width: 120px;
}

/* Question Type Sections */
.question-type-section {
    margin-bottom: 20px;
}

/* MCQ Options */
.options-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 12px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
    transition: all 0.2s;
}

.option-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.option-item.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.drag-handle {
    cursor: grab;
    color: #9ca3af;
    font-size: 1.2rem;
}

.drag-handle:hover {
    color: #6b7280;
}

.option-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #10b981;
}

.option-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 6px 10px;
    font-size: 0.95rem;
    border-radius: 6px;
    transition: background 0.2s;
}

.option-input:focus {
    outline: none;
    background: white;
}

.btn-remove-option {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
}

.btn-remove-option:hover {
    background: #fee2e2;
}

.btn-add-option {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: #f3f4f6;
    border: 1.5px dashed #d1d5db;
    border-radius: 8px;
    color: #6b7280;
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
}

.btn-add-option:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
    color: #374151;
}

/* True/False Options */
.torf-options {
    display: flex;
    gap: 16px;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.radio-option:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #10b981;
}

.radio-option input[type="radio"]:checked + span {
    color: #10b981;
}

/* Enumeration List */
.enum-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 12px;
}

.enum-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
}

.enum-number {
    min-width: 30px;
    text-align: center;
    font-weight: 600;
    color: #6b7280;
}

/* Rubric List */
.rubric-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 12px;
}

.rubric-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
}

.rubric-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rubric-weight {
    max-width: 100px;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 2px solid #e5e7eb;
}

.btn-cancel {
    padding: 10px 20px;
    background: #f3f4f6;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.btn-save {
    padding: 10px 20px;
    background: #10b981;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-save:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-save:active {
    transform: translateY(0);
}

/* Drag and Drop Styles */
.draggable {
    cursor: grab;
}

.draggable:active {
    cursor: grabbing;
}

.drag-over {
    border-color: #6b9aac !important;
    background: #f0f9ff !important;
}

.drag-placeholder {
    height: 60px;
    border: 2px dashed #6b9aac;
    border-radius: 8px;
    background: #f0f9ff;
    margin: 8px 0;
}
</style>
