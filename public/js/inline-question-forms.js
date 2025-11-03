/**
 * Inline Question Forms - Phase 2
 * Replaces modal-based question editing with inline expandable forms
 */

let questionFormCounter = 0;
let currentEditingForm = null;

/**
 * Open inline question form for adding new question
 * @param {string} type - Question type (mcq, torf, iden, enum, essay)
 * @param {number} sectionId - Section ID
 * @param {number|null} afterItemId - Insert after this question
 */
function openInlineQuestionForm(type, sectionId, afterItemId = null) {
    // Close any existing form
    closeAllInlineForms();

    // Generate unique ID for this form
    const formId = 'inlineForm' + (++questionFormCounter);

    // Create form HTML
    const formHTML = `
        <div class="inline-question-form" id="${formId}" data-section-id="${sectionId}" data-after-item-id="${afterItemId || ''}">
            <div class="question-form-container">
                <form class="question-form" onsubmit="return false;">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">

                    <div class="form-header">
                        <div class="form-title">
                            <i class="bi bi-question-circle"></i>
                            <span class="question-type-label">New Question</span>
                        </div>
                        <div class="form-header-actions">
                            <select class="question-type-selector" name="item_type" onchange="switchQuestionTypeInline(this)" required>
                                <option value="mcq" ${type === 'mcq' ? 'selected' : ''}>Multiple Choice</option>
                                <option value="torf" ${type === 'torf' ? 'selected' : ''}>True or False</option>
                                <option value="iden" ${type === 'iden' ? 'selected' : ''}>Identification</option>
                                <option value="enum" ${type === 'enum' ? 'selected' : ''}>Enumeration</option>
                                <option value="essay" ${type === 'essay' ? 'selected' : ''}>Essay</option>
                            </select>
                            <button type="button" class="btn-close-inline" onclick="closeInlineQuestionForm(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Question Text <span class="text-danger">*</span></label>
                        <textarea class="form-input" name="question" rows="3" placeholder="Enter your question here..." required></textarea>
                    </div>

                    <div class="question-type-section mcq-section" style="display: none;">
                        <label class="form-label">Options</label>
                        <div class="options-list" id="${formId}_mcqOptions"></div>
                        <button type="button" class="btn-add-option" onclick="addMCQOption(this)">
                            <i class="bi bi-plus-circle"></i> Add Option
                        </button>
                    </div>

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

                    <div class="question-type-section iden-section" style="display: none;">
                        <label class="form-label">Expected Answer <span class="text-danger">*</span></label>
                        <input type="text" class="form-input" name="iden_answer" placeholder="Enter the correct answer...">
                    </div>

                    <div class="question-type-section enum-section" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Enumeration Type</label>
                            <select class="form-input" name="enum_type">
                                <option value="ordered">Ordered (must be in sequence)</option>
                                <option value="unordered">Unordered (any order)</option>
                            </select>
                        </div>
                        <label class="form-label">Answers</label>
                        <div class="enum-list" id="${formId}_enumAnswers"></div>
                        <button type="button" class="btn-add-option" onclick="addEnumAnswer(this)">
                            <i class="bi bi-plus-circle"></i> Add Answer
                        </button>
                    </div>

                    <div class="question-type-section essay-section" style="display: none;">
                        <label class="form-label">Rubric / Talking Points</label>
                        <div class="rubric-list" id="${formId}_essayRubric"></div>
                        <button type="button" class="btn-add-option" onclick="addRubricItem(this)">
                            <i class="bi bi-plus-circle"></i> Add Talking Point
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" class="form-input points-input" name="points_awarded" min="1" value="1" required>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeInlineQuestionForm(this)">Cancel</button>
                        <button type="button" class="btn-save" onclick="saveInlineQuestion(this)">
                            <i class="bi bi-check-circle"></i> Save Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

    // Insert form after the appropriate location
    let insertLocation;
    if (afterItemId) {
        insertLocation = document.querySelector(`.question-card[data-item-id="${afterItemId}"]`);
    } else {
        // Insert at end of section
        insertLocation = document.querySelector(`.exam-section[data-section-id="${sectionId}"] .section-card`);
    }

    if (insertLocation) {
        insertLocation.insertAdjacentHTML('afterend', formHTML);

        // Initialize form for the selected type
        const form = document.getElementById(formId);
        switchQuestionTypeInline(form.querySelector('.question-type-selector'));

        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Focus on question text
        setTimeout(() => {
            form.querySelector('textarea[name="question"]').focus();
        }, 300);

        currentEditingForm = formId;

        // Track for undo/redo
        if (window.undoRedoManager) {
            window.undoRedoManager.recordAction('form_opened', { formId, type, sectionId });
        }
    }
}

/**
 * Switch question type in inline form
 * @param {HTMLElement} selectElement
 */
function switchQuestionTypeInline(selectElement) {
    const form = selectElement.closest('.inline-question-form');
    const type = selectElement.value;

    // Hide all type sections
    form.querySelectorAll('.question-type-section').forEach(section => {
        section.style.display = 'none';
    });

    // Show selected type section
    const typeSection = form.querySelector(`.${type}-section`);
    if (typeSection) {
        typeSection.style.display = 'block';
    }

    // Initialize type-specific content if empty
    if (type === 'mcq') {
        const optionsList = form.querySelector('.options-list');
        if (optionsList && optionsList.children.length === 0) {
            // Add 4 default options
            for (let i = 0; i < 4; i++) {
                addMCQOption(form.querySelector('.mcq-section .btn-add-option'));
            }
        }
    } else if (type === 'enum') {
        const enumList = form.querySelector('.enum-list');
        if (enumList && enumList.children.length === 0) {
            // Add 3 default answers
            for (let i = 0; i < 3; i++) {
                addEnumAnswer(form.querySelector('.enum-section .btn-add-option'));
            }
        }
    } else if (type === 'essay') {
        const rubricList = form.querySelector('.rubric-list');
        if (rubricList && rubricList.children.length === 0) {
            // Add 1 default talking point
            addRubricItem(form.querySelector('.essay-section .btn-add-option'));
        }
    }
}

/**
 * Add MCQ option
 */
function addMCQOption(button) {
    const form = button.closest('.inline-question-form');
    const optionsList = form.querySelector('.options-list');
    const optionNumber = optionsList.children.length + 1;

    const optionHTML = `
        <div class="option-item" draggable="true" ondragstart="dragStart(event)" ondragover="dragOver(event)" ondrop="dropOption(event)" ondragend="dragEnd(event)">
            <i class="bi bi-grip-vertical drag-handle"></i>
            <input type="checkbox" class="option-checkbox" title="Mark as correct">
            <input type="text" class="option-input" placeholder="Option ${optionNumber}" required>
            <button type="button" class="btn-remove-option" onclick="removeOption(this)" title="Remove option">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    optionsList.insertAdjacentHTML('beforeend', optionHTML);
}

/**
 * Remove option
 */
function removeOption(button) {
    const optionItem = button.closest('.option-item, .enum-item, .rubric-item');
    optionItem.remove();

    // Update numbering for enum items
    const enumList = button.closest('.enum-list');
    if (enumList) {
        Array.from(enumList.querySelectorAll('.enum-item')).forEach((item, index) => {
            const numberSpan = item.querySelector('.enum-number');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }
}

/**
 * Add enumeration answer
 */
function addEnumAnswer(button) {
    const form = button.closest('.inline-question-form');
    const enumList = form.querySelector('.enum-list');
    const answerNumber = enumList.children.length + 1;

    const answerHTML = `
        <div class="enum-item" draggable="true" ondragstart="dragStart(event)" ondragover="dragOver(event)" ondrop="dropOption(event)" ondragend="dragEnd(event)">
            <i class="bi bi-grip-vertical drag-handle"></i>
            <span class="enum-number">${answerNumber}</span>
            <input type="text" class="option-input" placeholder="Answer ${answerNumber}" required>
            <button type="button" class="btn-remove-option" onclick="removeOption(this)" title="Remove answer">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    enumList.insertAdjacentHTML('beforeend', answerHTML);
}

/**
 * Add rubric item (essay)
 */
function addRubricItem(button) {
    const form = button.closest('.inline-question-form');
    const rubricList = form.querySelector('.rubric-list');

    const rubricHTML = `
        <div class="rubric-item">
            <div class="rubric-content">
                <input type="text" class="form-input" placeholder="Talking point..." required>
                <input type="number" class="form-input rubric-weight" placeholder="Weight" min="1" value="5" required>
            </div>
            <button type="button" class="btn-remove-option" onclick="removeOption(this)" title="Remove talking point">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    rubricList.insertAdjacentHTML('beforeend', rubricHTML);
}

/**
 * Save inline question
 */
async function saveInlineQuestion(button) {
    const form = button.closest('.inline-question-form');
    const questionForm = form.querySelector('.question-form');
    const sectionId = form.dataset.sectionId;
    const afterItemId = form.dataset.afterItemId;
    const isEdit = form.dataset.questionId ? true : false;

    // Get form data
    const formData = {
        section_id: sectionId,
        question: questionForm.querySelector('textarea[name="question"]').value,
        item_type: questionForm.querySelector('select[name="item_type"]').value,
        points_awarded: questionForm.querySelector('input[name="points_awarded"]').value,
        after_item_id: afterItemId || null,
        _token: document.querySelector('meta[name="csrf-token"]')?.content
    };

    // Get type-specific data
    switch (formData.item_type) {
        case 'mcq':
            const mcqData = getMCQData(form);
            if (!mcqData) return;
            Object.assign(formData, mcqData);
            break;
        case 'torf':
            const torfAnswer = questionForm.querySelector('input[name="torf_answer"]:checked');
            if (!torfAnswer) {
                alert('Please select True or False');
                return;
            }
            formData.answer = JSON.stringify({ correct: torfAnswer.value });
            break;
        case 'iden':
            const idenAnswer = questionForm.querySelector('input[name="iden_answer"]').value;
            if (!idenAnswer) {
                alert('Please enter the expected answer');
                return;
            }
            formData.expected_answer = idenAnswer;
            break;
        case 'enum':
            const enumData = getEnumData(form);
            if (!enumData) return;
            Object.assign(formData, enumData);
            break;
        case 'essay':
            const essayData = getEssayData(form);
            if (!essayData) return;
            Object.assign(formData, essayData);
            break;
    }

    // Show saving indicator
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Saving...';

    try {
        const url = isEdit
            ? `/instructor/exams/${window.examId}/questions/${form.dataset.questionId}`
            : `/instructor/exams/${window.examId}/questions`;

        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData._token
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success || response.ok) {
            // Success - reload page to show new question
            showAutoSaveIndicator('saved');

            // Track for undo/redo
            if (window.undoRedoManager) {
                window.undoRedoManager.recordAction(isEdit ? 'question_updated' : 'question_created', {
                    questionId: result.item_id || form.dataset.questionId,
                    sectionId: sectionId
                });
            }

            // Reload to show updated question list
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            throw new Error(result.message || 'Failed to save question');
        }
    } catch (error) {
        console.error('Error saving question:', error);
        alert('Failed to save question: ' + error.message);
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-check-circle"></i> Save Question';
    }
}

/**
 * Get MCQ data from form
 */
function getMCQData(form) {
    const options = [];
    const correctAnswers = [];

    form.querySelectorAll('.mcq-section .option-item').forEach((optionItem, index) => {
        const text = optionItem.querySelector('.option-input').value.trim();
        if (!text) {
            alert('All options must have text');
            return null;
        }
        options.push(text);

        if (optionItem.querySelector('.option-checkbox').checked) {
            correctAnswers.push(index);
        }
    });

    if (options.length < 2) {
        alert('Please add at least 2 options');
        return null;
    }

    if (correctAnswers.length === 0) {
        alert('Please mark at least one option as correct');
        return null;
    }

    return {
        options: JSON.stringify(options),
        answer: JSON.stringify(correctAnswers)
    };
}

/**
 * Get enumeration data from form
 */
function getEnumData(form) {
    const answers = [];

    form.querySelectorAll('.enum-section .enum-item').forEach(item => {
        const text = item.querySelector('.option-input').value.trim();
        if (text) {
            answers.push(text);
        }
    });

    if (answers.length === 0) {
        alert('Please add at least one answer');
        return null;
    }

    return {
        answer: JSON.stringify(answers),
        enum_type: form.querySelector('select[name="enum_type"]').value
    };
}

/**
 * Get essay rubric data from form
 */
function getEssayData(form) {
    const rubric = [];
    let totalWeight = 0;

    form.querySelectorAll('.essay-section .rubric-item').forEach(item => {
        const inputs = item.querySelectorAll('input');
        const talkingPoint = inputs[0].value.trim();
        const weight = parseInt(inputs[1].value) || 0;

        if (talkingPoint && weight > 0) {
            rubric.push({
                talking_point: talkingPoint,
                weight: weight
            });
            totalWeight += weight;
        }
    });

    if (rubric.length === 0) {
        alert('Please add at least one talking point');
        return null;
    }

    return {
        expected_answer: JSON.stringify(rubric),
        points_awarded: totalWeight
    };
}

/**
 * Close inline question form
 */
function closeInlineQuestionForm(button) {
    const form = button.closest('.inline-question-form');

    if (confirm('Close this form? Any unsaved changes will be lost.')) {
        form.remove();
        currentEditingForm = null;
    }
}

/**
 * Close all inline forms
 */
function closeAllInlineForms() {
    document.querySelectorAll('.inline-question-form').forEach(form => {
        form.remove();
    });
    currentEditingForm = null;
}

/**
 * Edit existing question inline
 */
async function editQuestionInline(itemId) {
    // Close any existing form
    closeAllInlineForms();

    // Fetch question data
    try {
        const response = await fetch(`/instructor/exams/${window.examId}/questions/${itemId}`);
        const result = await response.json();

        if (result.success) {
            const item = result.item;

            // Find the question card
            const questionCard = document.querySelector(`.question-card[data-item-id="${itemId}"]`);
            if (!questionCard) return;

            // Create inline form for editing
            const formId = 'editForm' + itemId;
            const formHTML = createEditFormHTML(formId, item);

            questionCard.insertAdjacentHTML('afterend', formHTML);

            const form = document.getElementById(formId);
            populateEditForm(form, item);

            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });

            currentEditingForm = formId;
        }
    } catch (error) {
        console.error('Error loading question:', error);
        alert('Failed to load question for editing');
    }
}

// Drag and drop handlers for options (Phase 3)
let draggedElement = null;

function dragStart(event) {
    draggedElement = event.target;
    event.target.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
}

function dragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';

    const target = event.target.closest('.option-item, .enum-item');
    if (target && target !== draggedElement) {
        target.classList.add('drag-over');
    }
}

function dropOption(event) {
    event.preventDefault();

    const target = event.target.closest('.option-item, .enum-item');
    if (target && draggedElement && target !== draggedElement) {
        const list = target.parentElement;
        const draggedIndex = Array.from(list.children).indexOf(draggedElement);
        const targetIndex = Array.from(list.children).indexOf(target);

        if (draggedIndex < targetIndex) {
            target.after(draggedElement);
        } else {
            target.before(draggedElement);
        }
    }

    // Remove drag-over class from all items
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
}

function dragEnd(event) {
    event.target.classList.remove('dragging');
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    draggedElement = null;
}

// Export functions
window.openInlineQuestionForm = openInlineQuestionForm;
window.switchQuestionTypeInline = switchQuestionTypeInline;
window.addMCQOption = addMCQOption;
window.addEnumAnswer = addEnumAnswer;
window.addRubricItem = addRubricItem;
window.removeOption = removeOption;
window.saveInlineQuestion = saveInlineQuestion;
window.closeInlineQuestionForm = closeInlineQuestionForm;
window.closeAllInlineForms = closeAllInlineForms;
window.editQuestionInline = editQuestionInline;
window.dragStart = dragStart;
window.dragOver = dragOver;
window.dropOption = dropOption;
window.dragEnd = dragEnd;
