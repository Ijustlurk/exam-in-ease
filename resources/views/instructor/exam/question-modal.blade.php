{{-- resources/views/instructor/exam/question-modal.blade.php --}}

<!-- MCQ Modal -->
<div class="modal fade" id="mcqModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-ui-radios"></i>
                    <span>Multiple Choice Question</span>
                </h5>
                <select class="question-type-dropdown" onchange="switchQuestionType(this.value, 'mcq')">
                    <option value="mcq" selected>MCQ</option>
                    <option value="torf">True/False</option>
                    <option value="iden">Identification</option>
                    <option value="enum">Enumeration</option>
                    <option value="essay">Essay</option>
                </select>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="mcqForm">
                    <input type="hidden" id="mcq_section_id" name="section_id">
                    <input type="hidden" id="mcq_item_id" name="item_id">
                    <input type="hidden" id="mcq_after_item_id" name="after_item_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" id="mcq_question" name="question" rows="3" placeholder="Write your question here" required></textarea>
                    </div>

                    <div class="mb-4" id="mcq_options">
                        <label class="form-label-custom">Options</label>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option A" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="0" title="Mark as correct">
                            <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option B" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="1" title="Mark as correct">
                            <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option C" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="2" title="Mark as correct">
                            <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option D" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="3" title="Mark as correct">
                            <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addMCQOption()">
                        <i class="bi bi-plus-circle"></i> Add Option
                    </button>

                    <div class="mb-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" id="mcq_points" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question" id="mcq_submit_btn">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- True or False Modal -->
<div class="modal fade" id="torfModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-toggle-on"></i>
                    <span>True or False</span>
                </h5>
                <select class="question-type-dropdown" onchange="switchQuestionType(this.value, 'torf')">
                    <option value="mcq">MCQ</option>
                    <option value="torf" selected>True/False</option>
                    <option value="iden">Identification</option>
                    <option value="enum">Enumeration</option>
                    <option value="essay">Essay</option>
                </select>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="torfForm">
                    <input type="hidden" id="torf_section_id" name="section_id">
                    <input type="hidden" id="torf_item_id" name="item_id">
                    <input type="hidden" id="torf_after_item_id" name="after_item_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" id="torf_question" name="question" rows="3" placeholder="Write your question here" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Correct Answer</label>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div class="form-check" style="padding-left: 0;">
                                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; background: #f9fafb; border-radius: 8px;">
                                    <input class="form-check-input" type="radio" name="correct_answer" id="true_option" value="true" required style="margin: 0; width: 20px; height: 20px;">
                                    <span style="font-size: 0.95rem; color: #374151;">True</span>
                                </label>
                            </div>
                            <div class="form-check" style="padding-left: 0;">
                                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; background: #f9fafb; border-radius: 8px;">
                                    <input class="form-check-input" type="radio" name="correct_answer" id="false_option" value="false" style="margin: 0; width: 20px; height: 20px;">
                                    <span style="font-size: 0.95rem; color: #374151;">False</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" id="torf_points" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question" id="torf_submit_btn">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Identification Modal -->
<div class="modal fade" id="idenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-pencil-square"></i>
                    <span>Identification</span>
                </h5>
                <select class="question-type-dropdown" onchange="switchQuestionType(this.value, 'iden')">
                    <option value="mcq">MCQ</option>
                    <option value="torf">True/False</option>
                    <option value="iden" selected>Identification</option>
                    <option value="enum">Enumeration</option>
                    <option value="essay">Essay</option>
                </select>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="idenForm">
                    <input type="hidden" id="iden_section_id" name="section_id">
                    <input type="hidden" id="iden_item_id" name="item_id">
                    <input type="hidden" id="iden_after_item_id" name="after_item_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" id="iden_question" name="question" rows="3" placeholder="Write your question here" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Expected Answer</label>
                        <input type="text" class="form-control-custom" id="iden_expected_answer" name="expected_answer" placeholder="Enter the expected answer" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points for correct answer</label>
                        <input type="number" class="form-control-custom" id="iden_points" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question" id="iden_submit_btn">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Enumeration Modal -->
<div class="modal fade" id="enumModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-list-ol"></i>
                    <span>Enumeration</span>
                </h5>
                <select class="question-type-dropdown" onchange="switchQuestionType(this.value, 'enum')">
                    <option value="mcq">MCQ</option>
                    <option value="torf">True/False</option>
                    <option value="iden">Identification</option>
                    <option value="enum" selected>Enumeration</option>
                    <option value="essay">Essay</option>
                </select>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="enumForm">
                    <input type="hidden" id="enum_section_id" name="section_id">
                    <input type="hidden" id="enum_item_id" name="item_id">
                    <input type="hidden" id="enum_after_item_id" name="after_item_id">
                    <input type="hidden" id="enum_type" name="enum_type" value="ordered">
                    
                    <div class="mb-4" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div style="flex: 1;">
                            <label class="form-label-custom">Question</label>
                            <textarea class="form-control-custom" id="enum_question" name="question" rows="3" placeholder="ex: Lists the steps for the water cycle" required></textarea>
                        </div>
                        <div style="min-width: 240px;">
                            <label class="form-label-custom">Type</label>
                            <select class="form-control-custom" id="enumTypeSelect" onchange="toggleEnumType()" style="cursor: pointer;">
                                <option value="ordered">Ordered Enumeration</option>
                                <option value="unordered">Unordered Enumeration</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4" id="enum_answers">
                        <label class="form-label-custom" id="enumAnswersLabel">Expected Answers (in order)</label>
                        <div class="option-input-group">
                            <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">1.</span>
                            <input type="text" class="form-control-custom" placeholder="Answer 1" name="answers[]" required>
                            <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                            <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="option-input-group">
                            <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">2.</span>
                            <input type="text" class="form-control-custom" placeholder="Answer 2" name="answers[]" required>
                            <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                            <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addEnumAnswer()">
                        <i class="bi bi-plus-circle"></i> Add Answer
                    </button>

                    <div class="mb-4 mt-4">
                        <label class="form-label-custom" id="enumPointsLabel">Points:</label>
                        <input type="number" class="form-control-custom" id="enum_points" name="points_awarded" value="1" min="1" required style="width: 120px;">
                        <small id="enumPointsHelp" class="form-text text-muted" style="display: none; margin-top: 5px;">
                            Students earn points for each correct answer. If you enter 3 points for 3 answers, each correct answer = 1 point.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question" id="enum_submit_btn">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Essay Modal -->
<div class="modal fade" id="essayModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-textarea-t"></i>
                    <span>Essay Question</span>
                </h5>
                <select class="question-type-dropdown" onchange="switchQuestionType(this.value, 'essay')">
                    <option value="mcq">MCQ</option>
                    <option value="torf">True/False</option>
                    <option value="iden">Identification</option>
                    <option value="enum">Enumeration</option>
                    <option value="essay" selected>Essay</option>
                </select>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="essayForm">
                    <input type="hidden" id="essay_section_id" name="section_id">
                    <input type="hidden" id="essay_item_id" name="item_id">
                    <input type="hidden" id="essay_after_item_id" name="after_item_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" id="essay_question" name="question" rows="4" placeholder="Write your essay question here" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" id="essay_points" name="points_awarded" value="10" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question" id="essay_submit_btn">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentSectionId = null;
let editingItemId = null;

// Open Question Modal (for creating new questions)
function openQuestionModal(type, sectionId, afterItemId = null) {
    // Check if exam is locked
    if (typeof isLocked !== 'undefined' && isLocked) {
        alert('Cannot add or edit questions while exam is under approval or approved.');
        return;
    }
    
    currentSectionId = sectionId;
    editingItemId = null;
    
    // Map type names
    const typeMap = {
        'mcq': 'mcq',
        'torf': 'torf',
        'iden': 'iden',
        'enum': 'enum',
        'essay': 'essay'
    };
    const modalType = typeMap[type] || type;
    
    // Reset form
    document.getElementById(modalType + 'Form').reset();
    
    // Clear hidden item_id field (for new question)
    document.getElementById(modalType + '_item_id').value = '';
    
    // Set section ID
    document.getElementById(modalType + '_section_id').value = sectionId;
    
    // Set after_item_id if provided
    const afterItemIdField = document.getElementById(modalType + '_after_item_id');
    if (afterItemIdField) {
        afterItemIdField.value = afterItemId || '';
    }
    
    // Update submit button text
    document.getElementById(modalType + '_submit_btn').textContent = 'Save Question';
    
    // Reset MCQ options to default 4
    if (modalType === 'mcq') {
        const container = document.getElementById('mcq_options');
        container.innerHTML = `
            <label class="form-label-custom">Options</label>
            <div class="option-input-group">
                <input type="text" class="form-control-custom" placeholder="Option A" name="options[]" required>
                <input type="checkbox" class="correct-checkbox" name="correct[]" value="0" title="Mark as correct">
                <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="option-input-group">
                <input type="text" class="form-control-custom" placeholder="Option B" name="options[]" required>
                <input type="checkbox" class="correct-checkbox" name="correct[]" value="1" title="Mark as correct">
                <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="option-input-group">
                <input type="text" class="form-control-custom" placeholder="Option C" name="options[]" required>
                <input type="checkbox" class="correct-checkbox" name="correct[]" value="2" title="Mark as correct">
                <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="option-input-group">
                <input type="text" class="form-control-custom" placeholder="Option D" name="options[]" required>
                <input type="checkbox" class="correct-checkbox" name="correct[]" value="3" title="Mark as correct">
                <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
    }
    
    // Reset enumeration type if it's enum modal
    if (modalType === 'enum') {
        document.getElementById('enumTypeSelect').value = 'ordered';
        document.getElementById('enum_type').value = 'ordered';
        toggleEnumType();
        
        // Reset to 2 answer fields
        const container = document.getElementById('enum_answers');
        container.innerHTML = `
            <div class="option-input-group">
                <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">1.</span>
                <input type="text" class="form-control-custom" placeholder="Answer 1" name="answers[]" required>
                <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="option-input-group">
                <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">2.</span>
                <input type="text" class="form-control-custom" placeholder="Answer 2" name="answers[]" required>
                <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
        
        // Make draggable after adding
        setTimeout(() => makeEnumAnswersDraggable(), 100);
    }
    
    // Open appropriate modal
    const modal = new bootstrap.Modal(document.getElementById(modalType + 'Modal'));
    modal.show();
    
    // Close add dropdown if exists
    const addDropdown = document.getElementById('addDropdown');
    if (addDropdown) {
        addDropdown.classList.remove('show');
    }
}

// Edit Question - Fetch question data and populate modal
function editQuestion(itemId) {
    // Check if exam is locked
    if (typeof isLocked !== 'undefined' && isLocked) {
        alert('Cannot edit questions while exam is under approval or approved.');
        return;
    }
    
    editingItemId = itemId;
    
    // Fetch question data
    fetch(`/instructor/exams/${examId}/questions/${itemId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const item = result.item;
                populateModalForEdit(item);
            } else {
                alert('Error loading question data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load question data');
        });
}

// Populate modal with question data for editing
function populateModalForEdit(item) {
    const type = item.item_type;
    
    // Helper function to safely parse JSON or return existing object/array
    const safeJsonParse = (data, defaultValue = null) => {
        if (data === null || data === undefined) return defaultValue;
        if (typeof data === 'string') {
            try {
                return JSON.parse(data);
            } catch (e) {
                console.error('JSON parse error:', e);
                return defaultValue;
            }
        }
        return data; // Already an object/array
    };
    
    // Set editing mode
    editingItemId = item.item_id;
    currentSectionId = item.exam_section_id;
    
    // Fill common fields
    document.getElementById(type + '_section_id').value = item.exam_section_id;
    document.getElementById(type + '_item_id').value = item.item_id;
    document.getElementById(type + '_question').value = item.question;
    document.getElementById(type + '_points').value = item.points_awarded;
    
    // Update submit button text
    document.getElementById(type + '_submit_btn').textContent = 'Update Question';
    
    // Fill type-specific fields
    if (type === 'mcq') {
        // Parse options and answers (may be string or already parsed)
        const options = safeJsonParse(item.options, {});
        const correctAnswers = safeJsonParse(item.answer, []);
        
        const container = document.getElementById('mcq_options');
        container.innerHTML = '<label class="form-label-custom">Options</label>';
        
        Object.keys(options).forEach((key, index) => {
            const div = document.createElement('div');
            div.className = 'option-input-group';
            const letter = String.fromCharCode(65 + index);
            const isCorrect = correctAnswers.includes(parseInt(key));
            
            div.innerHTML = `
                <input type="text" class="form-control-custom" placeholder="Option ${letter}" name="options[]" value="${options[key]}" required>
                <input type="checkbox" class="correct-checkbox" name="correct[]" value="${index}" title="Mark as correct" ${isCorrect ? 'checked' : ''}>
                <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;
            container.appendChild(div);
        });
        
    } else if (type === 'torf') {
        // Parse answer (may be string or already parsed)
        const answer = safeJsonParse(item.answer, {});
        const correctValue = answer.correct;
        
        if (correctValue === 'true') {
            document.getElementById('true_option').checked = true;
        } else {
            document.getElementById('false_option').checked = true;
        }
        
    } else if (type === 'iden') {
        document.getElementById('iden_expected_answer').value = item.expected_answer || '';
        
    } else if (type === 'enum') {
        // Parse answers (may be string or already parsed)
        const answers = safeJsonParse(item.answer, []);
        const enumType = item.enum_type || 'ordered';
        
        document.getElementById('enumTypeSelect').value = enumType;
        document.getElementById('enum_type').value = enumType;
        
        const container = document.getElementById('enum_answers');
        container.innerHTML = '';
        
        answers.forEach((answer, index) => {
            const div = document.createElement('div');
            div.className = 'option-input-group';
            
            if (enumType === 'ordered') {
                div.innerHTML = `
                    <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">${index + 1}.</span>
                    <input type="text" class="form-control-custom" placeholder="Answer ${index + 1}" name="answers[]" value="${answer}" required>
                    <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                    <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                `;
            } else {
                div.innerHTML = `
                    <span class="enum-number" style="display: none; font-weight: 600; margin-right: 8px; min-width: 24px;">${index + 1}.</span>
                    <input type="text" class="form-control-custom" placeholder="Answer ${index + 1}" name="answers[]" value="${answer}" required>
                    <span class="enum-drag-handle" style="display: inline-block; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                    <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                `;
            }
            container.appendChild(div);
        });
        
        toggleEnumType();
    }
    
    // Open the appropriate modal
    const modal = new bootstrap.Modal(document.getElementById(type + 'Modal'));
    modal.show();
    
    // Update the dropdown to reflect current question type
    const dropdown = document.querySelector(`#${type}Modal .question-type-dropdown`);
    if (dropdown) {
        dropdown.value = type;
    }
}

// Handle MCQ Form Submit
document.getElementById('mcqForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const options = {};
    const correctAnswers = [];
    
    // Build options object
    formData.getAll('options[]').forEach((opt, index) => {
        options[index] = opt;
    });
    
    // Get correct answers
    document.querySelectorAll('#mcqForm .correct-checkbox:checked').forEach(cb => {
        correctAnswers.push(parseInt(cb.value));
    });
    
    if (correctAnswers.length === 0) {
        alert('Please mark at least one option as correct');
        return;
    }
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'mcq',
        options: JSON.stringify(options),
        answer: JSON.stringify(correctAnswers),
        points_awarded: formData.get('points_awarded'),
        after_item_id: formData.get('after_item_id')
    };
    
    saveQuestion(data);
});

// Handle True/False Form Submit
document.getElementById('torfForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const correctAnswer = formData.get('correct_answer');
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'torf',
        options: JSON.stringify({0: 'True', 1: 'False'}),
        answer: JSON.stringify({correct: correctAnswer}),
        points_awarded: formData.get('points_awarded'),
        after_item_id: formData.get('after_item_id')
    };
    
    saveQuestion(data);
});

// Handle Identification Form Submit
document.getElementById('idenForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'iden',
        expected_answer: formData.get('expected_answer'),
        points_awarded: formData.get('points_awarded'),
        after_item_id: formData.get('after_item_id')
    };
    
    saveQuestion(data);
});

// Handle Enumeration Form Submit
document.getElementById('enumForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const answers = formData.getAll('answers[]');
    const enumType = document.getElementById('enumTypeSelect').value;
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'enum',
        enum_type: enumType,
        answer: JSON.stringify(answers),
        points_awarded: formData.get('points_awarded'),
        after_item_id: formData.get('after_item_id')
    };
    
    saveQuestion(data);
});

// Handle Essay Form Submit
document.getElementById('essayForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'essay',
        points_awarded: formData.get('points_awarded'),
        after_item_id: formData.get('after_item_id')
    };
    
    saveQuestion(data);
});

// Save Question (Create or Update)
function saveQuestion(data) {
    const isEditing = editingItemId !== null;
    const url = isEditing 
        ? `/instructor/exams/${examId}/questions/${editingItemId}`
        : `/instructor/exams/${examId}/questions`;
    const method = isEditing ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Close modal
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                const modal = bootstrap.Modal.getInstance(activeModal);
                modal.hide();
            }
            
            // Show success message and reload
            location.reload();
        } else {
            alert('Error saving question: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save question. Please try again.');
    });
}

// Add MCQ Option
function addMCQOption() {
    const container = document.getElementById('mcq_options');
    const optionCount = container.querySelectorAll('.option-input-group').length;
    const letter = String.fromCharCode(65 + optionCount);
    
    const div = document.createElement('div');
    div.className = 'option-input-group';
    div.innerHTML = `
        <input type="text" class="form-control-custom" placeholder="Option ${letter}" name="options[]" required>
        <input type="checkbox" class="correct-checkbox" name="correct[]" value="${optionCount}" title="Mark as correct">
        <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove MCQ Option
function removeMCQOption(button) {
    const container = document.getElementById('mcq_options');
    const optionGroups = container.querySelectorAll('.option-input-group');
    
    // Don't allow removing if only 2 options left (minimum for MCQ)
    if (optionGroups.length <= 2) {
        alert('A multiple choice question must have at least 2 options.');
        return;
    }
    
    // Remove the option group
    button.closest('.option-input-group').remove();
    
    // Update checkbox values to maintain proper indexing
    const updatedGroups = container.querySelectorAll('.option-input-group');
    updatedGroups.forEach((group, index) => {
        const checkbox = group.querySelector('.correct-checkbox');
        checkbox.value = index;
        
        const input = group.querySelector('input[type="text"]');
        const letter = String.fromCharCode(65 + index);
        input.placeholder = `Option ${letter}`;
    });
}

// Add Enum Answer
function addEnumAnswer() {
    const container = document.getElementById('enum_answers');
    const answerCount = container.querySelectorAll('.option-input-group').length + 1;
    const enumType = document.getElementById('enumTypeSelect').value;
    
    const div = document.createElement('div');
    div.className = 'option-input-group';
    
    if (enumType === 'ordered') {
        div.innerHTML = `
            <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">${answerCount}.</span>
            <input type="text" class="form-control-custom" placeholder="Answer ${answerCount}" name="answers[]" required>
            <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
            <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
    } else {
        div.innerHTML = `
            <span class="enum-number" style="display: none; font-weight: 600; margin-right: 8px; min-width: 24px;">${answerCount}.</span>
            <input type="text" class="form-control-custom" placeholder="Answer ${answerCount}" name="answers[]" required>
            <span class="enum-drag-handle" style="display: inline-block; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
            <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
    }
    
    container.appendChild(div);
    
    // Make the new element draggable
    makeEnumAnswersDraggable();
}

// Remove Enum Answer
function removeEnumAnswer(button) {
    const container = document.getElementById('enum_answers');
    const answerGroups = container.querySelectorAll('.option-input-group');
    
    // Don't allow removing if only 2 answers left (minimum for enumeration)
    if (answerGroups.length <= 2) {
        alert('An enumeration question must have at least 2 answers.');
        return;
    }
    
    // Remove the answer group
    button.closest('.option-input-group').remove();
    
    // Update numbering for ordered type
    const enumType = document.getElementById('enumTypeSelect').value;
    if (enumType === 'ordered') {
        const updatedGroups = container.querySelectorAll('.option-input-group');
        updatedGroups.forEach((group, index) => {
            const number = group.querySelector('.enum-number');
            if (number) {
                number.textContent = `${index + 1}.`;
            }
            const input = group.querySelector('input[type="text"]');
            input.placeholder = `Answer ${index + 1}`;
        });
    }
}

// Drag and Drop for Enumeration Answers
let draggedElement = null;

// Make enum answers draggable
function makeEnumAnswersDraggable() {
    const container = document.getElementById('enum_answers');
    const optionGroups = container.querySelectorAll('.option-input-group');
    
    optionGroups.forEach(group => {
        // Remove existing listeners to avoid duplicates
        group.draggable = true;
        
        group.ondragstart = function(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        };
        
        group.ondragend = function(e) {
            this.style.opacity = '';
        };
        
        group.ondragover = function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            if (draggedElement !== this) {
                const allGroups = Array.from(container.querySelectorAll('.option-input-group'));
                const draggedIndex = allGroups.indexOf(draggedElement);
                const targetIndex = allGroups.indexOf(this);
                
                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedElement, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedElement, this);
                }
            }
        };
        
        group.ondrop = function(e) {
            e.preventDefault();
            // Update numbering after drop
            const enumType = document.getElementById('enumTypeSelect').value;
            if (enumType === 'ordered') {
                updateEnumNumbering();
            }
        };
    });
}

// Update enumeration numbering
function updateEnumNumbering() {
    const container = document.getElementById('enum_answers');
    const groups = container.querySelectorAll('.option-input-group');
    groups.forEach((group, index) => {
        const number = group.querySelector('.enum-number');
        if (number) {
            number.textContent = `${index + 1}.`;
        }
        const input = group.querySelector('input[type="text"]');
        input.placeholder = `Answer ${index + 1}`;
    });
}

// Call this whenever enum answers are added or modal is opened
document.getElementById('enumModal').addEventListener('shown.bs.modal', function() {
    makeEnumAnswersDraggable();
});

// Toggle Enumeration Type
function toggleEnumType() {
    const enumType = document.getElementById('enumTypeSelect').value;
    const numbers = document.querySelectorAll('.enum-number');
    const dragHandles = document.querySelectorAll('.enum-drag-handle');
    const answersLabel = document.getElementById('enumAnswersLabel');
    const pointsLabel = document.getElementById('enumPointsLabel');
    const pointsHelp = document.getElementById('enumPointsHelp');
    
    // Update hidden field
    document.getElementById('enum_type').value = enumType;
    
    if (enumType === 'ordered') {
        // Show numbers, hide drag handles
        numbers.forEach(num => num.style.display = 'inline-block');
        dragHandles.forEach(handle => handle.style.display = 'none');
        if (answersLabel) answersLabel.textContent = 'Expected Answers (in order)';
        if (pointsLabel) pointsLabel.textContent = 'Total Points:';
        if (pointsHelp) pointsHelp.style.display = 'none';
    } else {
        // Hide numbers, show drag handles
        numbers.forEach(num => num.style.display = 'none');
        dragHandles.forEach(handle => handle.style.display = 'inline-block');
        if (answersLabel) answersLabel.textContent = 'Expected Answers (any order)';
        if (pointsLabel) pointsLabel.textContent = 'Total Points (partial credit enabled):';
        if (pointsHelp) pointsHelp.style.display = 'block';
    }
}

// Switch Question Type
function switchQuestionType(newType, currentType) {
    // Store current section ID and item ID
    const sectionId = document.getElementById(currentType + '_section_id').value;
    const itemId = document.getElementById(currentType + '_item_id').value;
    
    // Close current modal
    const currentModal = bootstrap.Modal.getInstance(document.getElementById(currentType + 'Modal'));
    if (currentModal) {
        currentModal.hide();
    }
    
    // Small delay to allow modal to close smoothly
    setTimeout(() => {
        if (itemId) {
            // In edit mode: Open blank form of new type but maintain edit context
            // Reset the new type's form first
            document.getElementById(newType + 'Form').reset();
            
            // Set the section ID and item ID (to maintain edit mode)
            document.getElementById(newType + '_section_id').value = sectionId;
            document.getElementById(newType + '_item_id').value = itemId;
            
            // Update button text to indicate updating
            document.getElementById(newType + '_submit_btn').textContent = 'Update Question';
            
            // Reset MCQ options to default 4 if switching to MCQ
            if (newType === 'mcq') {
                const container = document.getElementById('mcq_options');
                container.innerHTML = `
                    <label class="form-label-custom">Options</label>
                    <div class="option-input-group">
                        <input type="text" class="form-control-custom" placeholder="Option A" name="options[]" required>
                        <input type="checkbox" class="correct-checkbox" name="correct[]" value="0" title="Mark as correct">
                        <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="option-input-group">
                        <input type="text" class="form-control-custom" placeholder="Option B" name="options[]" required>
                        <input type="checkbox" class="correct-checkbox" name="correct[]" value="1" title="Mark as correct">
                        <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="option-input-group">
                        <input type="text" class="form-control-custom" placeholder="Option C" name="options[]" required>
                        <input type="checkbox" class="correct-checkbox" name="correct[]" value="2" title="Mark as correct">
                        <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="option-input-group">
                        <input type="text" class="form-control-custom" placeholder="Option D" name="options[]" required>
                        <input type="checkbox" class="correct-checkbox" name="correct[]" value="3" title="Mark as correct">
                        <button type="button" class="btn-remove-option" onclick="removeMCQOption(this)" title="Remove option">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
            }
            
            // Reset enum answers to default 2 if switching to enum
            if (newType === 'enum') {
                const container = document.getElementById('enum_answers');
                container.innerHTML = `
                    <div class="option-input-group">
                        <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">1.</span>
                        <input type="text" class="form-control-custom" placeholder="Answer 1" name="answers[]" required>
                        <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                        <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="option-input-group">
                        <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">2.</span>
                        <input type="text" class="form-control-custom" placeholder="Answer 2" name="answers[]" required>
                        <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                        <button type="button" class="btn-remove-option" onclick="removeEnumAnswer(this)" title="Remove answer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
                // Reset enum type to ordered
                document.getElementById('enumTypeSelect').value = 'ordered';
                document.getElementById('enum_type').value = 'ordered';
                
                // Make draggable after adding
                setTimeout(() => makeEnumAnswersDraggable(), 100);
            }
            
            // Open the new modal
            const newModal = new bootstrap.Modal(document.getElementById(newType + 'Modal'));
            newModal.show();
        } else {
            // If creating new question, open the new type modal normally
            openQuestionModal(newType, sectionId);
        }
    }, 300);
}
</script>

<style>
.question-type-dropdown {
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    color: white;
    padding: 6px 12px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    margin-left: auto;
    margin-right: 12px;
    transition: all 0.2s ease;
}

.question-type-dropdown:hover {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

.question-type-dropdown:focus {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.6);
}

.question-type-dropdown option {
    background-color: #7ca5b8;
    color: white;
}

.btn-remove-option {
    background: transparent;
    border: none;
    color: #ef4444;
    padding: 6px;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    margin-left: 8px;
}

.btn-remove-option:hover {
    background-color: #fee2e2;
    color: #dc2626;
}

.btn-remove-option i {
    font-size: 0.85rem;
}

.option-input-group[draggable="true"] {
    cursor: move;
}

.option-input-group[draggable="true"]:hover {
    background-color: #f9fafb;
}

.enum-drag-handle {
    cursor: grab;
    user-select: none;
}

.enum-drag-handle:active {
    cursor: grabbing;
}
</style>