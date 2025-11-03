/**
 * Auto-save functionality for inline exam builder
 * Debounces input changes and saves to server
 */

let autoSaveTimeout = null;
const AUTO_SAVE_DELAY = 2000; // 2 seconds after user stops typing
const examId = window.examId || document.querySelector('[data-exam-id]')?.dataset.examId;

/**
 * Show auto-save indicator
 * @param {string} status - 'saving', 'saved', or 'error'
 */
function showAutoSaveIndicator(status) {
    const indicator = document.getElementById('autoSaveIndicator');
    if (!indicator) return;

    indicator.style.display = 'block';
    indicator.className = 'auto-save-indicator ' + status;

    // Auto-hide after 3 seconds if saved successfully
    if (status === 'saved') {
        setTimeout(() => {
            indicator.style.display = 'none';
        }, 3000);
    }
}

/**
 * Auto-save a single field to the server
 * @param {string} field - Field name
 * @param {any} value - Field value
 * @returns {Promise}
 */
function autoSaveField(field, value) {
    if (!examId) {
        console.error('No exam ID found for auto-save');
        return Promise.reject('No exam ID');
    }

    showAutoSaveIndicator('saving');

    const data = {
        [field]: value,
        _token: document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value
    };

    return fetch(`/instructor/exams/${examId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAutoSaveIndicator('saved');
            console.log(`Auto-saved ${field}:`, value);
            return result;
        } else {
            showAutoSaveIndicator('error');
            console.error('Auto-save failed:', result);
            throw new Error(result.message || 'Auto-save failed');
        }
    })
    .catch(error => {
        showAutoSaveIndicator('error');
        console.error('Auto-save error:', error);
        throw error;
    });
}

/**
 * Debounced auto-save for text inputs
 * @param {string} field - Field name
 * @param {any} value - Field value
 */
function debouncedAutoSave(field, value) {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        autoSaveField(field, value);
    }, AUTO_SAVE_DELAY);
}

/**
 * Initialize auto-save listeners for all inline inputs
 */
function initializeAutoSave() {
    // Text inputs, textareas, number inputs
    const textInputs = document.querySelectorAll('.inline-input[data-field]');
    textInputs.forEach(input => {
        if (input.disabled || input.readOnly) return;

        const field = input.dataset.field;

        if (input.type === 'datetime-local' || input.type === 'number' || input.tagName === 'SELECT') {
            // Save immediately on change (no debounce)
            input.addEventListener('change', () => {
                autoSaveField(field, input.value);
            });
        } else {
            // Debounce for text inputs
            input.addEventListener('input', () => {
                debouncedAutoSave(field, input.value);
            });

            // Also save on blur
            input.addEventListener('blur', () => {
                clearTimeout(autoSaveTimeout);
                autoSaveField(field, input.value);
            });
        }
    });

    // Special handling for exam title in header
    const examTitleInput = document.getElementById('examTitle');
    if (examTitleInput && !examTitleInput.disabled && !examTitleInput.readOnly) {
        examTitleInput.addEventListener('input', () => {
            debouncedAutoSave('exam_title', examTitleInput.value);
        });

        examTitleInput.addEventListener('blur', () => {
            clearTimeout(autoSaveTimeout);
            autoSaveField('exam_title', examTitleInput.value);
        });
    }

    console.log('Auto-save initialized for', textInputs.length, 'fields');
}

/**
 * Keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + S to manually trigger save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();

            // Save all modified fields
            const activeElement = document.activeElement;
            if (activeElement && activeElement.dataset.field) {
                clearTimeout(autoSaveTimeout);
                autoSaveField(activeElement.dataset.field, activeElement.value);
            }
        }
    });
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    initializeAutoSave();
    initializeKeyboardShortcuts();

    // Check if we should show a "draft created" message
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('new') === 'true') {
        console.log('New draft created - ready to edit');
    }
});

// Export for use in other scripts
window.autoSaveField = autoSaveField;
window.debouncedAutoSave = debouncedAutoSave;
window.showAutoSaveIndicator = showAutoSaveIndicator;
