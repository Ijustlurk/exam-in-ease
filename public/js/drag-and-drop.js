/**
 * Drag and Drop System - Phase 3
 * HTML5 drag-and-drop for questions and sections
 */

let draggedQuestion = null;
let draggedSection = null;
let dropPlaceholder = null;

/**
 * Initialize drag and drop for all questions and sections
 */
function initializeDragAndDrop() {
    // Make all question cards draggable
    document.querySelectorAll('.question-card').forEach(card => {
        card.setAttribute('draggable', 'true');
        card.addEventListener('dragstart', handleQuestionDragStart);
        card.addEventListener('dragover', handleQuestionDragOver);
        card.addEventListener('drop', handleQuestionDrop);
        card.addEventListener('dragend', handleQuestionDragEnd);
        card.addEventListener('dragenter', handleDragEnter);
        card.addEventListener('dragleave', handleDragLeave);
    });

    // Make all sections draggable
    document.querySelectorAll('.section-wrapper').forEach(wrapper => {
        const header = wrapper.querySelector('.section-header');
        if (header) {
            header.setAttribute('draggable', 'true');
            header.style.cursor = 'grab';

            wrapper.addEventListener('dragstart', handleSectionDragStart);
            wrapper.addEventListener('dragover', handleSectionDragOver);
            wrapper.addEventListener('drop', handleSectionDrop);
            wrapper.addEventListener('dragend', handleSectionDragEnd);
        }
    });

    console.log('Drag and drop initialized');
}

// ========== QUESTION DRAG AND DROP ==========

function handleQuestionDragStart(event) {
    draggedQuestion = event.currentTarget;
    draggedQuestion.style.opacity = '0.4';
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', draggedQuestion.innerHTML);

    // Create placeholder
    createPlaceholder();

    // Track for undo/redo
    if (window.undoRedoManager) {
        window.undoRedoManager.recordAction('drag_start', {
            itemId: draggedQuestion.dataset.itemId,
            type: 'question'
        });
    }

    // Add dragging class to body
    document.body.classList.add('is-dragging-question');
}

function handleQuestionDragOver(event) {
    if (!draggedQuestion) return;

    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';

    const target = event.currentTarget;
    if (target === draggedQuestion) return;

    // Get the bounding rectangle
    const rect = target.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;

    // Determine if we're dropping before or after
    if (event.clientY < midpoint) {
        target.parentNode.insertBefore(dropPlaceholder, target);
    } else {
        target.parentNode.insertBefore(dropPlaceholder, target.nextSibling);
    }
}

function handleDragEnter(event) {
    const target = event.currentTarget;
    if (target !== draggedQuestion && target.classList.contains('question-card')) {
        target.classList.add('drag-over');
    }
}

function handleDragLeave(event) {
    const target = event.currentTarget;
    target.classList.remove('drag-over');
}

function handleQuestionDrop(event) {
    event.stopPropagation();
    event.preventDefault();

    if (!draggedQuestion || !dropPlaceholder) return;

    // Move the dragged item to the placeholder location
    dropPlaceholder.parentNode.insertBefore(draggedQuestion, dropPlaceholder);

    // Save the new order
    saveQuestionOrder();

    return false;
}

function handleQuestionDragEnd(event) {
    if (draggedQuestion) {
        draggedQuestion.style.opacity = '1';
    }

    // Remove all drag-over classes
    document.querySelectorAll('.drag-over').forEach(el => {
        el.classList.remove('drag-over');
    });

    // Remove placeholder
    if (dropPlaceholder && dropPlaceholder.parentNode) {
        dropPlaceholder.parentNode.removeChild(dropPlaceholder);
    }

    draggedQuestion = null;
    dropPlaceholder = null;

    document.body.classList.remove('is-dragging-question');
}

/**
 * Save question order after drag and drop
 */
async function saveQuestionOrder() {
    const sectionId = draggedQuestion.closest('.exam-section').dataset.sectionId;
    const questionCards = document.querySelectorAll(`.exam-section[data-section-id="${sectionId}"] .question-card`);

    const order = Array.from(questionCards).map(card => ({
        item_id: card.dataset.itemId,
        order: Array.from(questionCards).indexOf(card) + 1
    }));

    try {
        const response = await fetch(`/instructor/exams/${window.examId}/questions/reorder-drag`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ items: order })
        });

        const result = await response.json();

        if (result.success) {
            showAutoSaveIndicator('saved');

            // Track for undo/redo
            if (window.undoRedoManager) {
                window.undoRedoManager.recordAction('questions_reordered', {
                    sectionId: sectionId,
                    order: order
                });
            }
        } else {
            throw new Error(result.message || 'Failed to save order');
        }
    } catch (error) {
        console.error('Error saving question order:', error);
        showAutoSaveIndicator('error');
        alert('Failed to save question order. Please refresh the page.');
    }
}

// ========== SECTION DRAG AND DROP ==========

function handleSectionDragStart(event) {
    draggedSection = event.currentTarget.closest('.exam-section');
    draggedSection.style.opacity = '0.4';
    event.dataTransfer.effectAllowed = 'move';

    // Track for undo/redo
    if (window.undoRedoManager) {
        window.undoRedoManager.recordAction('drag_start', {
            sectionId: draggedSection.dataset.sectionId,
            type: 'section'
        });
    }

    document.body.classList.add('is-dragging-section');
}

function handleSectionDragOver(event) {
    if (!draggedSection) return;

    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';

    const target = event.currentTarget.closest('.exam-section');
    if (!target || target === draggedSection) return;

    // Get the bounding rectangle
    const rect = target.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;

    // Add visual indicator
    target.classList.add('drag-over');

    // Determine drop position
    if (event.clientY < midpoint) {
        target.parentNode.insertBefore(draggedSection, target);
    } else {
        target.parentNode.insertBefore(draggedSection, target.nextSibling);
    }
}

function handleSectionDrop(event) {
    event.stopPropagation();
    event.preventDefault();

    if (!draggedSection) return;

    // Save the new order
    saveSectionOrder();

    return false;
}

function handleSectionDragEnd(event) {
    if (draggedSection) {
        draggedSection.style.opacity = '1';
    }

    // Remove all drag-over classes
    document.querySelectorAll('.exam-section.drag-over').forEach(el => {
        el.classList.remove('drag-over');
    });

    draggedSection = null;

    document.body.classList.remove('is-dragging-section');
}

/**
 * Save section order after drag and drop
 */
async function saveSectionOrder() {
    const sections = document.querySelectorAll('.exam-section');
    const order = Array.from(sections).map((section, index) => ({
        section_id: section.dataset.sectionId,
        section_order: index + 1
    }));

    try {
        const response = await fetch(`/instructor/exams/${window.examId}/sections/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ sections: order })
        });

        const result = await response.json();

        if (result.success) {
            showAutoSaveIndicator('saved');

            // Update section numbers in UI
            sections.forEach((section, index) => {
                const titleElement = section.querySelector('.section-title');
                if (titleElement) {
                    titleElement.textContent = `Section ${index + 1} of ${sections.length}`;
                }
            });

            // Track for undo/redo
            if (window.undoRedoManager) {
                window.undoRedoManager.recordAction('sections_reordered', {
                    order: order
                });
            }
        } else {
            throw new Error(result.message || 'Failed to save order');
        }
    } catch (error) {
        console.error('Error saving section order:', error);
        showAutoSaveIndicator('error');
        alert('Failed to save section order. Please refresh the page.');
    }
}

/**
 * Create placeholder element for drag visualization
 */
function createPlaceholder() {
    if (dropPlaceholder) {
        dropPlaceholder.remove();
    }

    dropPlaceholder = document.createElement('div');
    dropPlaceholder.className = 'drag-placeholder';
    dropPlaceholder.innerHTML = '<i class="bi bi-arrow-down-short"></i> Drop here';
}

/**
 * Enable/disable drag and drop based on exam status
 */
function toggleDragAndDrop(enabled) {
    const method = enabled ? 'setAttribute' : 'removeAttribute';

    document.querySelectorAll('.question-card').forEach(card => {
        card[method]('draggable', 'true');
    });

    document.querySelectorAll('.section-wrapper .section-header').forEach(header => {
        header[method]('draggable', 'true');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Check if user has permission to edit
    const examStatus = document.querySelector('[data-exam-status]')?.dataset.examStatus;
    const isOwner = document.querySelector('[data-is-owner]')?.dataset.isOwner === 'true';

    if (isOwner && examStatus === 'draft') {
        initializeDragAndDrop();
    }
});

// Re-initialize after dynamic content changes
window.reinitializeDragAndDrop = initializeDragAndDrop;

// Export functions
window.initializeDragAndDrop = initializeDragAndDrop;
window.toggleDragAndDrop = toggleDragAndDrop;
