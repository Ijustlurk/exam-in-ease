/**
 * Undo/Redo System - Phase 4
 * Tracks all exam builder actions and allows undo/redo
 */

class UndoRedoManager {
    constructor(maxHistory = 50) {
        this.undoStack = [];
        this.redoStack = [];
        this.maxHistory = maxHistory;
        this.isProcessing = false;

        this.initializeUI();
        this.updateButtons();
    }

    /**
     * Record an action
     * @param {string} action - Action type
     * @param {object} data - Action data
     */
    recordAction(action, data) {
        if (this.isProcessing) return; // Prevent recording during undo/redo

        const actionRecord = {
            action: action,
            data: data,
            timestamp: Date.now(),
            inverseAction: this.getInverseAction(action, data)
        };

        this.undoStack.push(actionRecord);

        // Limit stack size
        if (this.undoStack.length > this.maxHistory) {
            this.undoStack.shift();
        }

        // Clear redo stack when new action is recorded
        this.redoStack = [];

        this.updateButtons();

        console.log('Action recorded:', action, data);
    }

    /**
     * Get inverse action for undo
     */
    getInverseAction(action, data) {
        const inverseMap = {
            'question_created': 'question_deleted',
            'question_deleted': 'question_created',
            'question_updated': 'question_updated', // Inverse is itself (swap old/new)
            'section_created': 'section_deleted',
            'section_deleted': 'section_created',
            'questions_reordered': 'questions_reordered',
            'sections_reordered': 'sections_reordered',
            'form_opened': 'form_closed',
            'form_closed': 'form_opened'
        };

        return inverseMap[action] || action;
    }

    /**
     * Undo last action
     */
    async undo() {
        if (this.undoStack.length === 0) {
            console.log('Nothing to undo');
            return;
        }

        this.isProcessing = true;
        const actionRecord = this.undoStack.pop();

        console.log('Undoing:', actionRecord);

        try {
            await this.executeInverse(actionRecord);

            // Move to redo stack
            this.redoStack.push(actionRecord);

            showAutoSaveIndicator('saved');
        } catch (error) {
            console.error('Undo failed:', error);
            showAutoSaveIndicator('error');

            // Return to undo stack if failed
            this.undoStack.push(actionRecord);
        } finally {
            this.isProcessing = false;
            this.updateButtons();
        }
    }

    /**
     * Redo last undone action
     */
    async redo() {
        if (this.redoStack.length === 0) {
            console.log('Nothing to redo');
            return;
        }

        this.isProcessing = true;
        const actionRecord = this.redoStack.pop();

        console.log('Redoing:', actionRecord);

        try {
            await this.executeAction(actionRecord);

            // Move back to undo stack
            this.undoStack.push(actionRecord);

            showAutoSaveIndicator('saved');
        } catch (error) {
            console.error('Redo failed:', error);
            showAutoSaveIndicator('error');

            // Return to redo stack if failed
            this.redoStack.push(actionRecord);
        } finally {
            this.isProcessing = false;
            this.updateButtons();
        }
    }

    /**
     * Execute inverse action
     */
    async executeInverse(actionRecord) {
        const { action, data } = actionRecord;

        switch (action) {
            case 'question_created':
                // Undo: delete the question
                await this.deleteQuestion(data.questionId);
                break;

            case 'question_deleted':
                // Undo: recreate the question (would need to store full data)
                console.warn('Cannot undo delete - full data not stored');
                break;

            case 'question_updated':
                // Undo: restore previous values (would need old values)
                console.warn('Cannot undo update - previous data not stored');
                break;

            case 'questions_reordered':
                // Undo: restore previous order (would need previous order)
                console.warn('Cannot undo reorder - previous order not stored');
                break;

            case 'sections_reordered':
                // Undo: restore previous order
                console.warn('Cannot undo section reorder - previous order not stored');
                break;

            case 'form_opened':
                // Undo: close the form
                closeAllInlineForms();
                break;

            default:
                console.log('No inverse action for:', action);
        }
    }

    /**
     * Execute action (for redo)
     */
    async executeAction(actionRecord) {
        // For now, most actions require page reload to redo
        // In a full implementation, we'd store enough data to replay actions
        console.log('Redo would replay:', actionRecord);

        // Reload page to see changes
        location.reload();
    }

    /**
     * Delete question (for undo)
     */
    async deleteQuestion(questionId) {
        const response = await fetch(`/instructor/exams/${window.examId}/questions/${questionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        if (response.ok) {
            // Remove from DOM
            const questionCard = document.querySelector(`.question-card[data-item-id="${questionId}"]`);
            if (questionCard) {
                questionCard.remove();
            }
        } else {
            throw new Error('Failed to delete question');
        }
    }

    /**
     * Initialize UI elements
     */
    initializeUI() {
        // Create undo/redo buttons in header if they don't exist
        const headerActions = document.querySelector('.header-actions');
        if (headerActions && !document.getElementById('undoBtn')) {
            const undoRedoHTML = `
                <div class="undo-redo-buttons" style="display: flex; gap: 8px; margin-right: 8px;">
                    <button id="undoBtn" class="header-icon-btn" title="Undo (Ctrl+Z)" disabled>
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button id="redoBtn" class="header-icon-btn" title="Redo (Ctrl+Y)" disabled>
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            `;

            headerActions.insertAdjacentHTML('afterbegin', undoRedoHTML);

            // Add event listeners
            document.getElementById('undoBtn').addEventListener('click', () => this.undo());
            document.getElementById('redoBtn').addEventListener('click', () => this.redo());
        }
    }

    /**
     * Update button states
     */
    updateButtons() {
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');

        if (undoBtn) {
            undoBtn.disabled = this.undoStack.length === 0;
            undoBtn.title = this.undoStack.length > 0
                ? `Undo: ${this.getLastActionDescription(this.undoStack)} (Ctrl+Z)`
                : 'Nothing to undo';
        }

        if (redoBtn) {
            redoBtn.disabled = this.redoStack.length === 0;
            redoBtn.title = this.redoStack.length > 0
                ? `Redo: ${this.getLastActionDescription(this.redoStack)} (Ctrl+Y)`
                : 'Nothing to redo';
        }
    }

    /**
     * Get human-readable description of last action
     */
    getLastActionDescription(stack) {
        if (stack.length === 0) return '';

        const actionRecord = stack[stack.length - 1];
        const descriptions = {
            'question_created': 'Add question',
            'question_deleted': 'Delete question',
            'question_updated': 'Edit question',
            'section_created': 'Add section',
            'section_deleted': 'Delete section',
            'questions_reordered': 'Reorder questions',
            'sections_reordered': 'Reorder sections',
            'form_opened': 'Open form',
            'form_closed': 'Close form'
        };

        return descriptions[actionRecord.action] || actionRecord.action;
    }

    /**
     * Clear all history
     */
    clear() {
        this.undoStack = [];
        this.redoStack = [];
        this.updateButtons();
    }

    /**
     * Get current state summary
     */
    getState() {
        return {
            undoCount: this.undoStack.length,
            redoCount: this.redoStack.length,
            canUndo: this.undoStack.length > 0,
            canRedo: this.redoStack.length > 0
        };
    }
}

// Initialize global undo/redo manager
let undoRedoManager;

document.addEventListener('DOMContentLoaded', () => {
    undoRedoManager = new UndoRedoManager(50);
    window.undoRedoManager = undoRedoManager;

    console.log('Undo/Redo system initialized');
});

// Keyboard shortcuts for undo/redo
document.addEventListener('keydown', (event) => {
    // Ctrl+Z or Cmd+Z for undo
    if ((event.ctrlKey || event.metaKey) && event.key === 'z' && !event.shiftKey) {
        event.preventDefault();
        if (undoRedoManager) {
            undoRedoManager.undo();
        }
    }

    // Ctrl+Y or Ctrl+Shift+Z for redo
    if ((event.ctrlKey || event.metaKey) && (event.key === 'y' || (event.shiftKey && event.key === 'z'))) {
        event.preventDefault();
        if (undoRedoManager) {
            undoRedoManager.redo();
        }
    }
});

// Export for use in other modules
window.UndoRedoManager = UndoRedoManager;
