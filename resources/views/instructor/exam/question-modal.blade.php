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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="mcqForm">
                    <input type="hidden" id="mcq_section_id" name="section_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" name="question" rows="3" placeholder="Write your question here" required></textarea>
                    </div>

                    <div class="mb-4" id="mcq_options">
                        <label class="form-label-custom">Options</label>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option A" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="0" title="Mark as correct">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option B" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="1" title="Mark as correct">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option C" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="2" title="Mark as correct">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option D" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="3" title="Mark as correct">
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addMCQOption()">
                        <i class="bi bi-plus-circle"></i> Add Option
                    </button>

                    <div class="mb-4 mt-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question">Save Question</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="torfForm">
                    <input type="hidden" id="torf_section_id" name="section_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" name="question" rows="3" placeholder="Write your question here" required></textarea>
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
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question">Save Question</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="idenForm">
                    <input type="hidden" id="iden_section_id" name="section_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" name="question" rows="3" placeholder="Write your question here" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Expected Answer</label>
                        <input type="text" class="form-control-custom" name="expected_answer" placeholder="Enter the expected answer" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points for correct answer</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question">Save Question</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="enumForm">
                    <input type="hidden" id="enum_section_id" name="section_id">
                    <input type="hidden" id="enum_type" name="enum_type" value="ordered">
                    
                    <div class="mb-4" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div style="flex: 1;">
                            <label class="form-label-custom">Question</label>
                            <textarea class="form-control-custom" name="question" rows="3" placeholder="ex: Lists the steps for the water cycle" required></textarea>
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
                        </div>
                        <div class="option-input-group">
                            <span class="enum-number" style="font-weight: 600; margin-right: 8px; min-width: 24px;">2.</span>
                            <input type="text" class="form-control-custom" placeholder="Answer 2" name="answers[]" required>
                            <span class="enum-drag-handle" style="display: none; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addEnumAnswer()">
                        <i class="bi bi-plus-circle"></i> Add Answer
                    </button>

                    <div class="mb-4 mt-4">
                        <label class="form-label-custom" id="enumPointsLabel">Points:</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question">Save Question</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <form id="essayForm">
                    <input type="hidden" id="essay_section_id" name="section_id">
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" name="question" rows="4" placeholder="Write your essay question here" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="10" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn-save-question">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentSectionId = null;

// Open Question Modal
function openQuestionModal(type, sectionId) {
    currentSectionId = sectionId;
    
    // Set section ID in forms
    document.getElementById(type + '_section_id').value = sectionId;
    
    // Reset form
    document.getElementById(type + 'Form').reset();
    document.getElementById(type + '_section_id').value = sectionId;
    
    // Reset enumeration type if it's enum modal
    if (type === 'enum') {
        document.getElementById('enumTypeSelect').value = 'ordered';
        document.getElementById('enum_type').value = 'ordered';
        toggleEnumType();
    }
    
    // Open appropriate modal
    const modal = new bootstrap.Modal(document.getElementById(type + 'Modal'));
    modal.show();
    
    // Close add dropdown
    document.getElementById('addDropdown').classList.remove('show');
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
        points_awarded: formData.get('points_awarded')
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
        points_awarded: formData.get('points_awarded')
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
        points_awarded: formData.get('points_awarded')
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
        points_awarded: formData.get('points_awarded')
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
        points_awarded: formData.get('points_awarded')
    };
    
    saveQuestion(data);
});

// Save Question
function saveQuestion(data) {
    fetch(`/instructor/exams/${examId}/questions`, {
        method: 'POST',
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
    `;
    container.appendChild(div);
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
        `;
    } else {
        div.innerHTML = `
            <span class="enum-number" style="display: none; font-weight: 600; margin-right: 8px; min-width: 24px;">${answerCount}.</span>
            <input type="text" class="form-control-custom" placeholder="Answer ${answerCount}" name="answers[]" required>
            <span class="enum-drag-handle" style="display: inline-block; color: #9ca3af; font-size: 1rem; cursor: move; margin-left: 8px;"><i class="bi bi-grip-vertical"></i></span>
        `;
    }
    
    container.appendChild(div);
}

// Toggle Enumeration Type
function toggleEnumType() {
    const enumType = document.getElementById('enumTypeSelect').value;
    const numbers = document.querySelectorAll('.enum-number');
    const dragHandles = document.querySelectorAll('.enum-drag-handle');
    const answersLabel = document.getElementById('enumAnswersLabel');
    const pointsLabel = document.getElementById('enumPointsLabel');
    
    // Update hidden field
    document.getElementById('enum_type').value = enumType;
    
    if (enumType === 'ordered') {
        // Show numbers, hide drag handles
        numbers.forEach(num => num.style.display = 'inline-block');
        dragHandles.forEach(handle => handle.style.display = 'none');
        answersLabel.textContent = 'Expected Answers (in order)';
        pointsLabel.textContent = 'Points:';
    } else {
        // Hide numbers, show drag handles
        numbers.forEach(num => num.style.display = 'none');
        dragHandles.forEach(handle => handle.style.display = 'inline-block');
        answersLabel.textContent = 'Expected Answers';
        pointsLabel.textContent = 'Point per correct answer:';
    }
}
</script>