<!-- MCQ Modal -->
<div class="modal fade" id="mcqModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="bi bi-question-circle"></i>
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
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="0">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option B" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="1">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option C" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="2">
                        </div>
                        <div class="option-input-group">
                            <input type="text" class="form-control-custom" placeholder="Option D" name="options[]" required>
                            <input type="checkbox" class="correct-checkbox" name="correct[]" value="3">
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addMCQOption()">
                        <i class="bi bi-plus-circle"></i> Add Option
                    </button>

                    <div class="mb-4 mt-4">
                        <label class="form-label-custom">Points per correct answer</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end">
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
                    <i class="bi bi-check2-circle"></i>
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
                        <div class="option-input-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" id="true_option" value="true" required>
                                <label class="form-check-label" for="true_option">True</label>
                            </div>
                        </div>
                        <div class="option-input-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" id="false_option" value="false">
                                <label class="form-check-label" for="false_option">False</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Points</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end">
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

                    <div class="d-flex justify-content-end">
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
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Question</label>
                        <textarea class="form-control-custom" name="question" rows="3" placeholder="ex: Lists the steps for the water cycle" required></textarea>
                    </div>

                    <div class="mb-4" id="enum_answers">
                        <label class="form-label-custom">Expected Answers (in order)</label>
                        <div class="option-input-group">
                            <span style="font-weight: 600; margin-right: 8px;">1.</span>
                            <input type="text" class="form-control-custom" placeholder="Answer 1" name="answers[]" required>
                        </div>
                        <div class="option-input-group">
                            <span style="font-weight: 600; margin-right: 8px;">2.</span>
                            <input type="text" class="form-control-custom" placeholder="Answer 2" name="answers[]" required>
                        </div>
                    </div>

                    <button type="button" class="btn-add-option" onclick="addEnumAnswer()">
                        <i class="bi bi-plus-circle"></i> Add Answer
                    </button>

                    <div class="mb-4 mt-4">
                        <label class="form-label-custom">Points per correct answer</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="1" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end">
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
                        <label class="form-label-custom">Points per correct answer</label>
                        <input type="number" class="form-control-custom" name="points_awarded" value="10" min="1" required style="width: 120px;">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-save-question">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const examId = {{ $exam->exam_id }};
let currentSectionId = null;

// Toggle Add Dropdown
function toggleAddDropdown() {
    document.getElementById('addDropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.add-dropdown')) {
        document.getElementById('addDropdown').classList.remove('show');
    }
});

// Open Question Modal
function openQuestionModal(type, sectionId) {
    currentSectionId = sectionId;
    
    // Set section ID in forms
    document.getElementById(type + '_section_id').value = sectionId;
    
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
    
    const data = {
        section_id: formData.get('section_id'),
        question: formData.get('question'),
        item_type: 'enum',
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
            location.reload();
        } else {
            alert('Error saving question');
        }
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
        <input type="checkbox" class="correct-checkbox" name="correct[]" value="${optionCount}">
    `;
    container.appendChild(div);
}

// Add Enum Answer
function addEnumAnswer() {
    const container = document.getElementById('enum_answers');
    const answerCount = container.querySelectorAll('.option-input-group').length + 1;
    
    const div = document.createElement('div');
    div.className = 'option-input-group';
    div.innerHTML = `
        <span style="font-weight: 600; margin-right: 8px;">${answerCount}.</span>
        <input type="text" class="form-control-custom" placeholder="Answer ${answerCount}" name="answers[]" required>
    `;
    container.appendChild(div);
}

// Make Card Active
function makeCardActive(card, event) {
    if (card.classList.contains('view-mode')) {
        document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
    }
}

// Update Exam Title
function updateExamTitle() {
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
    fetch(`/instructor/exams/${examId}/sections/${sectionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ [field]: value })
    });
}

// Duplicate Question
function duplicateQuestion(examId, itemId) {
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

// Reorder Question
function reorderQuestion(examId, itemId, direction) {
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
</script>