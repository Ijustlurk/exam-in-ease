{{-- Inline Exam Metadata Editor --}}
<div class="inline-metadata-panel" id="inlineMetadata">
    <div class="metadata-container">
        {{-- Auto-save indicator --}}
        <div class="auto-save-indicator" id="autoSaveIndicator" style="display: none;">
            <span class="save-status saving">
                <i class="bi bi-arrow-clockwise spin"></i> Saving...
            </span>
            <span class="save-status saved">
                <i class="bi bi-check-circle-fill"></i> Saved
            </span>
            <span class="save-status error">
                <i class="bi bi-exclamation-circle-fill"></i> Error saving
            </span>
        </div>

        {{-- Collapsible Metadata Section --}}
        <div class="metadata-header" onclick="toggleMetadata()">
            <div class="metadata-title">
                <i class="bi bi-info-circle"></i>
                <span>Exam Details</span>
                <span class="badge bg-secondary ms-2">{{ ucfirst($exam->status) }}</span>
            </div>
            <i class="bi bi-chevron-down toggle-icon" id="metadataToggleIcon"></i>
        </div>

        <div class="metadata-body" id="metadataBody">
            <form id="inlineExamForm">
                @csrf

                {{-- Basic Information --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="inline-label">Exam Description</label>
                        <textarea
                            class="inline-input"
                            name="exam_desc"
                            id="inlineExamDesc"
                            rows="2"
                            placeholder="Add a description for this exam..."
                            data-field="exam_desc"
                            {{ $exam->status !== 'draft' ? 'readonly' : '' }}
                        >{{ $exam->exam_desc }}</textarea>
                    </div>
                </div>

                {{-- Subject and Duration --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="inline-label">Subject <span class="text-danger">*</span></label>
                        <select
                            class="inline-input"
                            name="subject_id"
                            id="inlineSubjectSelect"
                            data-field="subject_id"
                            {{ $exam->status !== 'draft' ? 'disabled' : '' }}
                            onchange="loadClassesBySubjectInline()"
                        >
                            <option value="">Select Subject</option>
                            @foreach(\App\Models\Subject::all() as $subject)
                            <option value="{{ $subject->subject_id }}" {{ $exam->subject_id == $subject->subject_id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="inline-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            class="inline-input"
                            name="duration"
                            id="inlineDuration"
                            value="{{ $exam->duration }}"
                            min="1"
                            placeholder="60"
                            data-field="duration"
                            {{ $exam->status !== 'draft' ? 'readonly' : '' }}
                        >
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="inline-label">Start Date & Time <span class="text-danger">*</span></label>
                        <input
                            type="datetime-local"
                            class="inline-input"
                            name="schedule_start"
                            id="inlineScheduleStart"
                            value="{{ $exam->schedule_start ? date('Y-m-d\TH:i', strtotime($exam->schedule_start)) : '' }}"
                            data-field="schedule_start"
                            {{ $exam->status !== 'draft' ? 'readonly' : '' }}
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="inline-label">End Date & Time <span class="text-danger">*</span></label>
                        <input
                            type="datetime-local"
                            class="inline-input"
                            name="schedule_end"
                            id="inlineScheduleEnd"
                            value="{{ $exam->schedule_end ? date('Y-m-d\TH:i', strtotime($exam->schedule_end)) : '' }}"
                            data-field="schedule_end"
                            {{ $exam->status !== 'draft' ? 'readonly' : '' }}
                        >
                    </div>
                </div>

                {{-- Classes Selection --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="inline-label">Assigned Classes <span class="text-danger">*</span></label>
                        <div class="dropdown" style="width: 100%;">
                            <button
                                class="inline-input dropdown-toggle text-start"
                                type="button"
                                id="inlineClassDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="width: 100%;"
                                {{ $exam->status !== 'draft' ? 'disabled' : '' }}
                            >
                                <span id="inlineSelectedClassesText">
                                    @if($exam->examAssignments && $exam->examAssignments->count() > 0)
                                        {{ $exam->examAssignments->count() }} class(es) selected
                                    @else
                                        Select Classes
                                    @endif
                                </span>
                            </button>
                            <div class="dropdown-menu" id="inlineClassCheckboxList" style="width: 100%; max-height: 300px; overflow-y: auto;">
                                @if($exam->subject_id)
                                    <div class="p-3 text-muted">Loading classes...</div>
                                @else
                                    <div class="p-3 text-muted">Select subject first</div>
                                @endif
                            </div>
                            <input type="hidden" name="selected_classes" id="inlineSelectedClassesInput" value="">
                        </div>
                    </div>
                </div>

                {{-- Term (Optional) --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="inline-label">Term (Optional)</label>
                        <input
                            type="text"
                            class="inline-input"
                            name="term"
                            id="inlineTerm"
                            value="{{ $exam->term }}"
                            placeholder="e.g., Fall 2025, Midterm"
                            data-field="term"
                            {{ $exam->status !== 'draft' ? 'readonly' : '' }}
                        >
                    </div>
                </div>

                {{-- Request Approval Button --}}
                @if($exam->status === 'draft' && $exam->no_of_items > 0)
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" onclick="requestApproval()">
                            <i class="bi bi-send"></i> Request Approval
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
    .inline-metadata-panel {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin: 0 auto 32px;
        max-width: 1200px;
        overflow: hidden;
    }

    .metadata-container {
        position: relative;
    }

    .auto-save-indicator {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 10;
        font-size: 0.85rem;
    }

    .auto-save-indicator .save-status {
        display: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    .auto-save-indicator.saving .saving {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff3cd;
        color: #856404;
    }

    .auto-save-indicator.saved .saved {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #d1e7dd;
        color: #0f5132;
    }

    .auto-save-indicator.error .error {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8d7da;
        color: #842029;
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }

    .metadata-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%);
        color: white;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
    }

    .metadata-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .toggle-icon {
        font-size: 1.2rem;
        transition: transform 0.3s;
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    .metadata-body {
        padding: 24px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .metadata-body.expanded {
        max-height: 1000px;
        transition: max-height 0.5s ease-in;
    }

    .inline-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .inline-input {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: white;
    }

    .inline-input:focus {
        outline: none;
        border-color: #6b9aac;
        box-shadow: 0 0 0 3px rgba(107, 154, 172, 0.1);
    }

    .inline-input:disabled,
    .inline-input:readonly {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .inline-input::placeholder {
        color: #9ca3af;
    }

    textarea.inline-input {
        resize: vertical;
        min-height: 60px;
    }

    .dropdown-menu {
        padding: 8px;
    }

    .class-item {
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .class-item:hover {
        background: #f3f4f6;
    }
</style>

<script>
// Metadata panel toggle
function toggleMetadata() {
    const body = document.getElementById('metadataBody');
    const icon = document.getElementById('metadataToggleIcon');

    body.classList.toggle('expanded');
    icon.classList.toggle('rotated');
}

// Auto-expand on page load if exam has no subject (new draft)
document.addEventListener('DOMContentLoaded', function() {
    @if(!$exam->subject_id)
        toggleMetadata();
    @endif

    // Load classes if subject is already selected
    @if($exam->subject_id)
        loadClassesBySubjectInline();
    @endif
});

// Load classes by subject (inline version)
function loadClassesBySubjectInline() {
    const subjectSelect = document.getElementById('inlineSubjectSelect');
    const subjectId = subjectSelect.value;

    const classCheckboxList = document.getElementById('inlineClassCheckboxList');
    const selectedClassesText = document.getElementById('inlineSelectedClassesText');
    const selectedClassesInput = document.getElementById('inlineSelectedClassesInput');

    if (!subjectId) {
        classCheckboxList.innerHTML = '<div class="p-3 text-muted">Select subject first</div>';
        return;
    }

    classCheckboxList.innerHTML = '<div class="p-3 text-muted">Loading classes...</div>';

    // Auto-save subject selection
    autoSaveField('subject_id', subjectId);

    fetch(`/instructor/api/classes?subject_id=${subjectId}`)
        .then(response => response.json())
        .then(classes => {
            if (classes.length === 0) {
                classCheckboxList.innerHTML = '<div class="p-3 text-muted">No classes available</div>';
            } else {
                // Get currently assigned classes
                const currentClasses = @json($exam->examAssignments ? $exam->examAssignments->pluck('class_id')->toArray() : []);

                classCheckboxList.innerHTML = classes.map(cls => `
                    <div class="class-item">
                        <input type="checkbox"
                               id="inline_class_${cls.class_id}"
                               value="${cls.class_id}"
                               onchange="updateSelectedClassesInline()"
                               class="form-check-input me-2"
                               ${currentClasses.includes(cls.class_id) ? 'checked' : ''}>
                        <label for="inline_class_${cls.class_id}" class="form-check-label">${cls.display}</label>
                    </div>
                `).join('');

                updateSelectedClassesInline();
            }
        })
        .catch(error => {
            console.error('Error loading classes:', error);
            classCheckboxList.innerHTML = '<div class="p-3 text-danger">Error loading classes</div>';
        });
}

// Update selected classes display
function updateSelectedClassesInline() {
    const checkboxes = document.querySelectorAll('#inlineClassCheckboxList input[type="checkbox"]:checked');
    const selectedClassesText = document.getElementById('inlineSelectedClassesText');
    const selectedClassesInput = document.getElementById('inlineSelectedClassesInput');

    const selectedIds = Array.from(checkboxes).map(cb => cb.value);

    if (selectedIds.length === 0) {
        selectedClassesText.textContent = 'Select Classes';
        selectedClassesInput.value = '';
    } else {
        selectedClassesText.textContent = `${selectedIds.length} class(es) selected`;
        selectedClassesInput.value = selectedIds.join(',');
    }

    // Auto-save classes
    if (selectedIds.length > 0) {
        autoSaveField('selected_classes', selectedIds.join(','));
    }
}

// Request approval
function requestApproval() {
    if (!confirm('Request approval for this exam? You will not be able to edit it until it is reviewed.')) {
        return;
    }

    autoSaveField('status', 'for approval').then(() => {
        alert('Approval requested successfully!');
        location.reload();
    });
}
</script>
