/**
 * Keyboard Shortcuts - Phase 5
 * Comprehensive keyboard navigation and shortcuts for exam builder
 */

class KeyboardShortcutsManager {
    constructor() {
        this.shortcuts = {
            // Question operations
            'ctrl+enter': { action: 'addQuestion', description: 'Add new question to active section', icon: 'plus-circle' },
            'alt+q': { action: 'addMCQ', description: 'Add Multiple Choice question', icon: 'list-check' },
            'alt+t': { action: 'addTrueFalse', description: 'Add True/False question', icon: 'check2-square' },
            'alt+i': { action: 'addIdentification', description: 'Add Identification question', icon: 'input-cursor-text' },
            'alt+e': { action: 'addEnumeration', description: 'Add Enumeration question', icon: 'list-ol' },
            'alt+s': { action: 'addEssay', description: 'Add Essay question', icon: 'textarea-t' },

            // Navigation
            'arrowdown': { action: 'nextQuestion', description: 'Select next question', icon: 'arrow-down' },
            'arrowup': { action: 'prevQuestion', description: 'Select previous question', icon: 'arrow-up' },
            'arrowright': { action: 'nextSection', description: 'Select next section', icon: 'arrow-right' },
            'arrowleft': { action: 'prevSection', description: 'Select previous section', icon: 'arrow-left' },
            'home': { action: 'firstQuestion', description: 'Select first question', icon: 'skip-start' },
            'end': { action: 'lastQuestion', description: 'Select last question', icon: 'skip-end' },

            // Editing
            'enter': { action: 'editSelected', description: 'Edit selected question', icon: 'pencil' },
            'delete': { action: 'deleteSelected', description: 'Delete selected question', icon: 'trash' },
            'ctrl+d': { action: 'duplicateSelected', description: 'Duplicate selected question', icon: 'files' },
            'escape': { action: 'deselectAll', description: 'Deselect all / Close form', icon: 'x-circle' },

            // Undo/Redo
            'ctrl+z': { action: 'undo', description: 'Undo last action', icon: 'arrow-counterclockwise' },
            'ctrl+y': { action: 'redo', description: 'Redo last action', icon: 'arrow-clockwise' },
            'ctrl+shift+z': { action: 'redo', description: 'Redo last action', icon: 'arrow-clockwise' },

            // Save
            'ctrl+s': { action: 'save', description: 'Save exam', icon: 'save' },

            // Help
            'shift+?': { action: 'showHelp', description: 'Show keyboard shortcuts help', icon: 'question-circle' },
            'f1': { action: 'showHelp', description: 'Show keyboard shortcuts help', icon: 'question-circle' }
        };

        this.currentlySelected = null;
        this.activeSection = null;
        this.enabled = true;

        this.initialize();
    }

    /**
     * Initialize keyboard shortcuts
     */
    initialize() {
        document.addEventListener('keydown', (event) => this.handleKeyDown(event));

        // Create help panel
        this.createHelpPanel();

        // Add help button to header
        this.addHelpButton();

        console.log('Keyboard shortcuts initialized');
    }

    /**
     * Handle keydown events
     */
    handleKeyDown(event) {
        if (!this.enabled) return;

        // Don't trigger shortcuts when typing in inputs
        const activeElement = document.activeElement;
        const isInputField = activeElement.tagName === 'INPUT' ||
                           activeElement.tagName === 'TEXTAREA' ||
                           activeElement.tagName === 'SELECT' ||
                           activeElement.isContentEditable;

        // Allow some shortcuts even in input fields
        const allowedInInputs = ['ctrl+s', 'ctrl+z', 'ctrl+y', 'escape', 'f1', 'shift+?'];

        // Build shortcut key string
        let key = '';
        if (event.ctrlKey || event.metaKey) key += 'ctrl+';
        if (event.shiftKey) key += 'shift+';
        if (event.altKey) key += 'alt+';
        key += event.key.toLowerCase();

        // Check if this is a registered shortcut
        const shortcut = this.shortcuts[key];

        if (shortcut) {
            // Check if allowed in input fields
            if (isInputField && !allowedInInputs.includes(key)) {
                return;
            }

            event.preventDefault();
            this.executeAction(shortcut.action, event);
        }
    }

    /**
     * Execute shortcut action
     */
    executeAction(action, event) {
        console.log('Executing keyboard shortcut:', action);

        switch (action) {
            // Add questions
            case 'addQuestion':
                this.addQuestionToActiveSection();
                break;
            case 'addMCQ':
                this.addQuestionToActiveSection('mcq');
                break;
            case 'addTrueFalse':
                this.addQuestionToActiveSection('torf');
                break;
            case 'addIdentification':
                this.addQuestionToActiveSection('iden');
                break;
            case 'addEnumeration':
                this.addQuestionToActiveSection('enum');
                break;
            case 'addEssay':
                this.addQuestionToActiveSection('essay');
                break;

            // Navigation
            case 'nextQuestion':
                this.selectNextQuestion();
                break;
            case 'prevQuestion':
                this.selectPrevQuestion();
                break;
            case 'nextSection':
                this.selectNextSection();
                break;
            case 'prevSection':
                this.selectPrevSection();
                break;
            case 'firstQuestion':
                this.selectFirstQuestion();
                break;
            case 'lastQuestion':
                this.selectLastQuestion();
                break;

            // Editing
            case 'editSelected':
                this.editSelectedQuestion();
                break;
            case 'deleteSelected':
                this.deleteSelectedQuestion();
                break;
            case 'duplicateSelected':
                this.duplicateSelectedQuestion();
                break;
            case 'deselectAll':
                this.deselectAll();
                break;

            // Undo/Redo (handled by undo-redo.js)
            case 'undo':
                if (window.undoRedoManager) {
                    window.undoRedoManager.undo();
                }
                break;
            case 'redo':
                if (window.undoRedoManager) {
                    window.undoRedoManager.redo();
                }
                break;

            // Save
            case 'save':
                this.saveExam();
                break;

            // Help
            case 'showHelp':
                this.showHelpPanel();
                break;
        }
    }

    // ========== QUESTION OPERATIONS ==========

    addQuestionToActiveSection(type = 'mcq') {
        // Get active section
        const activeSection = document.querySelector('.section-card.active');
        if (!activeSection) {
            alert('Please select a section first');
            return;
        }

        const sectionId = activeSection.dataset.sectionId;

        // Open inline question form
        if (typeof openInlineQuestionForm === 'function') {
            openInlineQuestionForm(type, sectionId);
        } else {
            console.error('openInlineQuestionForm not found');
        }
    }

    // ========== NAVIGATION ==========

    selectNextQuestion() {
        const questions = Array.from(document.querySelectorAll('.question-card'));
        const currentIndex = questions.findIndex(q => q.classList.contains('active'));

        if (currentIndex < questions.length - 1) {
            this.selectQuestion(questions[currentIndex + 1]);
        }
    }

    selectPrevQuestion() {
        const questions = Array.from(document.querySelectorAll('.question-card'));
        const currentIndex = questions.findIndex(q => q.classList.contains('active'));

        if (currentIndex > 0) {
            this.selectQuestion(questions[currentIndex - 1]);
        } else if (currentIndex === -1 && questions.length > 0) {
            this.selectQuestion(questions[0]);
        }
    }

    selectNextSection() {
        const sections = Array.from(document.querySelectorAll('.section-card'));
        const currentIndex = sections.findIndex(s => s.classList.contains('active'));

        if (currentIndex < sections.length - 1) {
            this.selectSection(sections[currentIndex + 1]);
        }
    }

    selectPrevSection() {
        const sections = Array.from(document.querySelectorAll('.section-card'));
        const currentIndex = sections.findIndex(s => s.classList.contains('active'));

        if (currentIndex > 0) {
            this.selectSection(sections[currentIndex - 1]);
        } else if (currentIndex === -1 && sections.length > 0) {
            this.selectSection(sections[0]);
        }
    }

    selectFirstQuestion() {
        const questions = document.querySelectorAll('.question-card');
        if (questions.length > 0) {
            this.selectQuestion(questions[0]);
        }
    }

    selectLastQuestion() {
        const questions = document.querySelectorAll('.question-card');
        if (questions.length > 0) {
            this.selectQuestion(questions[questions.length - 1]);
        }
    }

    selectQuestion(questionCard) {
        // Remove active from all
        document.querySelectorAll('.question-card').forEach(q => q.classList.remove('active'));
        document.querySelectorAll('.section-card').forEach(s => s.classList.remove('active'));

        // Add active to selected
        questionCard.classList.add('active');

        // Scroll into view
        questionCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

        this.currentlySelected = questionCard;
    }

    selectSection(sectionCard) {
        // Remove active from all
        document.querySelectorAll('.section-card').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.question-card').forEach(q => q.classList.remove('active'));

        // Add active to selected
        sectionCard.classList.add('active');

        // Scroll into view
        sectionCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

        this.activeSection = sectionCard;
    }

    // ========== EDITING OPERATIONS ==========

    editSelectedQuestion() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) return;

        const itemId = activeQuestion.dataset.itemId;
        if (typeof editQuestionInline === 'function') {
            editQuestionInline(itemId);
        } else if (typeof editQuestion === 'function') {
            editQuestion(itemId);
        }
    }

    deleteSelectedQuestion() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) return;

        const itemId = activeQuestion.dataset.itemId;
        if (confirm('Delete this question?')) {
            if (typeof deleteQuestion === 'function') {
                deleteQuestion(window.examId, itemId);
            }
        }
    }

    duplicateSelectedQuestion() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) return;

        const itemId = activeQuestion.dataset.itemId;
        if (typeof duplicateQuestion === 'function') {
            duplicateQuestion(window.examId, itemId);
        }
    }

    deselectAll() {
        // Close any open forms first
        if (typeof closeAllInlineForms === 'function') {
            closeAllInlineForms();
        }

        // Deselect all
        document.querySelectorAll('.question-card.active').forEach(q => q.classList.remove('active'));
        document.querySelectorAll('.section-card.active').forEach(s => s.classList.remove('active'));

        this.currentlySelected = null;
    }

    saveExam() {
        // Trigger auto-save for any active field
        const activeField = document.activeElement;
        if (activeField && activeField.dataset.field) {
            if (typeof autoSaveField === 'function') {
                autoSaveField(activeField.dataset.field, activeField.value);
            }
        }

        showAutoSaveIndicator('saved');
    }

    // ========== HELP PANEL ==========

    createHelpPanel() {
        const helpHTML = `
            <div id="keyboardShortcutsHelp" class="keyboard-shortcuts-help" style="display: none;">
                <div class="help-overlay" onclick="window.keyboardShortcuts.hideHelpPanel()"></div>
                <div class="help-panel">
                    <div class="help-header">
                        <h3><i class="bi bi-keyboard"></i> Keyboard Shortcuts</h3>
                        <button class="btn-close-help" onclick="window.keyboardShortcuts.hideHelpPanel()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="help-content">
                        ${this.generateHelpContent()}
                    </div>
                    <div class="help-footer">
                        <p>Press <kbd>?</kbd> or <kbd>F1</kbd> to show/hide this panel</p>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', helpHTML);
    }

    generateHelpContent() {
        const categories = {
            'Add Questions': ['addQuestion', 'addMCQ', 'addTrueFalse', 'addIdentification', 'addEnumeration', 'addEssay'],
            'Navigation': ['nextQuestion', 'prevQuestion', 'nextSection', 'prevSection', 'firstQuestion', 'lastQuestion'],
            'Editing': ['editSelected', 'deleteSelected', 'duplicateSelected', 'deselectAll'],
            'Undo/Redo': ['undo', 'redo'],
            'Save': ['save'],
            'Help': ['showHelp']
        };

        let html = '';

        for (const [category, actions] of Object.entries(categories)) {
            html += `<div class="help-category">
                <h4>${category}</h4>
                <div class="help-shortcuts">`;

            for (const action of actions) {
                const shortcut = Object.entries(this.shortcuts).find(([key, val]) => val.action === action);
                if (shortcut) {
                    const [key, data] = shortcut;
                    html += `
                        <div class="help-shortcut">
                            <kbd>${this.formatKey(key)}</kbd>
                            <span>${data.description}</span>
                        </div>
                    `;
                }
            }

            html += `</div></div>`;
        }

        return html;
    }

    formatKey(key) {
        return key
            .replace('ctrl+', '⌃ ')
            .replace('shift+', '⇧ ')
            .replace('alt+', '⌥ ')
            .replace('enter', '↵')
            .replace('arrowdown', '↓')
            .replace('arrowup', '↑')
            .replace('arrowright', '→')
            .replace('arrowleft', '←')
            .toUpperCase();
    }

    showHelpPanel() {
        const panel = document.getElementById('keyboardShortcutsHelp');
        if (panel) {
            panel.style.display = 'flex';
        }
    }

    hideHelpPanel() {
        const panel = document.getElementById('keyboardShortcutsHelp');
        if (panel) {
            panel.style.display = 'none';
        }
    }

    addHelpButton() {
        const headerActions = document.querySelector('.header-actions');
        if (headerActions && !document.getElementById('helpBtn')) {
            const helpBtn = `
                <button id="helpBtn" class="header-icon-btn" title="Keyboard Shortcuts (F1)" onclick="window.keyboardShortcuts.showHelpPanel()">
                    <i class="bi bi-question-circle"></i>
                </button>
            `;

            headerActions.insertAdjacentHTML('beforeend', helpBtn);
        }
    }

    /**
     * Enable/disable shortcuts
     */
    setEnabled(enabled) {
        this.enabled = enabled;
    }
}

// Initialize on page load
let keyboardShortcuts;

document.addEventListener('DOMContentLoaded', () => {
    keyboardShortcuts = new KeyboardShortcutsManager();
    window.keyboardShortcuts = keyboardShortcuts;

    console.log('Keyboard shortcuts ready. Press ? or F1 for help');
});

// Export
window.KeyboardShortcutsManager = KeyboardShortcutsManager;
