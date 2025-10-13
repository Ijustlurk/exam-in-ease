@extends('layouts.Instructor.app')

@section('content')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #e8eef2;
    }
    
    .exam-results-container {
        background-color: #e8eef2;
        min-height: 100vh;
    }
    
    .exam-header-bar {
        background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%);
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .exam-title-header {
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    
    .header-controls {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .download-btn {
        background-color: white;
        color: #374151;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .download-btn:hover {
        background-color: #f9fafb;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .class-select {
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 36px 8px 16px;
        font-size: 0.9rem;
        color: #374151;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23374151'%3E%3Cpath d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        min-width: 180px;
    }
    
    .tabs-container {
        background-color: white;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .nav-tabs {
        border: none;
        display: flex;
        justify-content: center;
        gap: 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6b7280;
        font-size: 1rem;
        font-weight: 500;
        padding: 16px 48px;
        background: transparent;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #374151;
    }
    
    .nav-tabs .nav-link.active {
        color: #212529;
        background: transparent;
        border-bottom: 3px solid #6b9aac;
        font-weight: 600;
    }
    
    .tab-content {
        background-color: #e8eef2;
        padding: 32px;
        min-height: 70vh;
    }
    
    /* Individual Tab Styles */
    .student-navigation {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .student-nav-btn {
        background: transparent;
        border: none;
        color: #6b7280;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 4px 12px;
        transition: color 0.2s;
    }
    
    .student-nav-btn:hover {
        color: #374151;
    }
    
    .student-name {
        font-size: 1.1rem;
        font-style: italic;
        color: #212529;
        font-weight: 500;
    }
    
    .score-summary {
        text-align: center;
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 32px;
    }
    
    .section-card, .question-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 8px;
    }
    
    .section-description {
        font-size: 0.95rem;
        color: #6b7280;
        margin: 0;
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    
    .question-title {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }
    
    .question-type {
        font-size: 0.85rem;
        color: #9ca3af;
        font-style: italic;
    }
    
    .choice-option {
        border: 2px solid #d1d5db;
        border-radius: 50px;
        padding: 12px 20px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        color: #212529;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }
    
    .choice-option.correct {
        background-color: #22c55e;
        color: white;
        border-color: #22c55e;
        font-weight: 500;
    }
    
    .choice-option.incorrect {
        background-color: #ef4444;
        color: white;
        border-color: #ef4444;
        font-weight: 500;
    }
    
    .student-answer-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-style: italic;
        color: white;
    }
    
    .student-answer-indicator i {
        font-size: 1rem;
    }
    
    .question-points {
        text-align: right;
        font-size: 0.9rem;
        color: #6b7280;
        font-style: italic;
        margin-top: 12px;
    }
    
    /* Questions Tab Styles */
    .part-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 8px;
    }
    
    .part-directions {
        font-size: 0.95rem;
        color: #6b7280;
    }
    
    .response-count {
        font-size: 0.9rem;
        color: #6b7280;
        font-style: italic;
    }
    
    .choice-with-stats {
        border: 2px solid #d1d5db;
        border-radius: 50px;
        padding: 12px 20px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        color: #212529;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .choice-with-stats.correct {
        background-color: #22c55e;
        color: white;
        border-color: #22c55e;
        font-weight: 500;
    }
    
    .response-stat {
        font-style: italic;
        color: inherit;
    }
    
    .question-analysis {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        font-size: 0.95rem;
        color: #374151;
    }
    
    .correct-percentage {
        font-weight: 600;
    }
    
    /* Summary Tab Styles */
    .summary-section {
        margin-bottom: 32px;
    }
    
    .summary-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 24px;
    }
    
    .chart-placeholder {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 48px;
        text-align: center;
        color: #9ca3af;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="exam-results-container">
    <!-- Header Bar -->
    <div class="exam-header-bar">
        <h1 class="exam-title-header">Exam No. 1</h1>
        <div class="header-controls">
            <button class="download-btn" id="downloadBtn" style="display: none;">
                <i class="bi bi-download"></i>
                Download Results
            </button>
            <select class="class-select">
                <option>Select Class</option>
                <option>1A - Computer Programming</option>
                <option>1B - Computer Programming</option>
                <option>1C - Computer Programming</option>
            </select>
        </div>
    </div>
    
    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" id="examTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">
                    Summary
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab">
                    Questions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
                    Individual
                </button>
            </li>
        </ul>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Summary Tab -->
        <div class="tab-pane fade show active" id="summary" role="tabpanel">
            <div class="summary-section">
                <h2 class="summary-title">1. Scores Distribution</h2>
                <div class="chart-placeholder">
                    <div>
                        <i class="bi bi-bar-chart" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                        <p>Chart visualization will be displayed here</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Questions Tab -->
        <div class="tab-pane fade" id="questions" role="tabpanel">
            <div class="section-card">
                <h3 class="part-header">Part 1.</h3>
                <p class="part-directions">Directions for Part I</p>
            </div>
            
            <!-- Question 1 -->
            <div class="question-card">
                <div class="question-header">
                    <div>
                        <h4 class="question-title">Question 1. This is the question asked?</h4>
                    </div>
                    <div class="response-count">242 responses</div>
                </div>
                
                <div class="choice-with-stats correct">
                    <span>A. This is the one of the choices that is correct.</span>
                    <span class="response-stat">194 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>B. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">12 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>C. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">20 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>D. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">18 responses</span>
                </div>
                
                <div class="question-analysis">
                    <span class="correct-percentage">88% (194)</span> got the correct answer <strong>(A)</strong>. Students found it easy.
                </div>
            </div>
            
            <!-- Question 2 -->
            <div class="question-card">
                <div class="question-header">
                    <div>
                        <h4 class="question-title">Question 2. This is the question asked?</h4>
                    </div>
                    <div class="response-count">242 responses</div>
                </div>
                
                <div class="choice-with-stats correct">
                    <span>A. This is the one of the choices that is correct.</span>
                    <span class="response-stat">194 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>B. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">12 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>C. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">20 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>D. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">18 responses</span>
                </div>
                
                <div class="question-analysis">
                    <span class="correct-percentage">88% (194)</span> got the correct answer <strong>(A)</strong>. Students found it easy.
                </div>
            </div>
            
            <!-- Question 3 -->
            <div class="question-card">
                <div class="question-header">
                    <div>
                        <h4 class="question-title">Question 3. This is the question asked?</h4>
                    </div>
                    <div class="response-count">242 responses</div>
                </div>
                
                <div class="choice-with-stats correct">
                    <span>A. This is the one of the choices that is correct.</span>
                    <span class="response-stat">194 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>B. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">12 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>C. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">20 responses</span>
                </div>
                
                <div class="choice-with-stats">
                    <span>D. This is the one of the choices that is wrong.</span>
                    <span class="response-stat">18 responses</span>
                </div>
                
                <div class="question-analysis">
                    <span class="correct-percentage">88% (194)</span> got the correct answer <strong>(A)</strong>. Students found it easy.
                </div>
            </div>
        </div>
        
        <!-- Individual Tab -->
        <div class="tab-pane fade" id="individual" role="tabpanel">
            <!-- Student Navigation -->
            <div class="student-navigation">
                <button class="student-nav-btn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="student-name">Aglugub, Elian Benjamin</div>
                <button class="student-nav-btn">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            
            <!-- Score Summary -->
            <div class="score-summary">
                2 pts. out of 3 pts. | Time Taken: 5:00 minutes
            </div>
            
            <!-- Section Info -->
            <div class="section-card">
                <h3 class="section-title">Section 1</h3>
                <p class="section-description">This is what this exam is about.</p>
            </div>
            
            <!-- Question 1 -->
            <div class="question-card">
                <div class="question-header">
                    <h4 class="question-title">Question 1. This is the question asked?</h4>
                    <span class="question-type">MCQ</span>
                </div>
                
                <div class="choice-option correct">
                    <span>A. This is the one of the choices that is correct.</span>
                    <span class="student-answer-indicator">
                        Student's answer
                        <i class="bi bi-check-lg"></i>
                    </span>
                </div>
                
                <div class="choice-option">
                    <span>B. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="choice-option">
                    <span>C. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="choice-option">
                    <span>D. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="question-points">1 pt.</div>
            </div>
            
            <!-- Question 2 -->
            <div class="question-card">
                <div class="question-header">
                    <h4 class="question-title">Question 2. This is the question asked?</h4>
                    <span class="question-type">MCQ</span>
                </div>
                
                <div class="choice-option">
                    <span>A. This is the one of the choices that is correct.</span>
                </div>
                
                <div class="choice-option">
                    <span>B. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="choice-option incorrect">
                    <span>C. This is the one of the choices that is wrong.</span>
                    <span class="student-answer-indicator">
                        Student's answer
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                
                <div class="choice-option">
                    <span>D. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="question-points">0 pt.</div>
            </div>
            
            <!-- Question 3 -->
            <div class="question-card">
                <div class="question-header">
                    <h4 class="question-title">Question 3. This is the question asked?</h4>
                    <span class="question-type">MCQ</span>
                </div>
                
                <div class="choice-option correct">
                    <span>A. This is the one of the choices that is correct.</span>
                    <span class="student-answer-indicator">
                        Student's answer
                        <i class="bi bi-check-lg"></i>
                    </span>
                </div>
                
                <div class="choice-option">
                    <span>B. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="choice-option">
                    <span>C. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="choice-option">
                    <span>D. This is the one of the choices that is wrong.</span>
                </div>
                
                <div class="question-points">1 pt.</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Show download button only on Questions tab
    document.getElementById('questions-tab').addEventListener('click', function() {
        document.getElementById('downloadBtn').style.display = 'flex';
    });
    
    document.getElementById('summary-tab').addEventListener('click', function() {
        document.getElementById('downloadBtn').style.display = 'none';
    });
    
    document.getElementById('individual-tab').addEventListener('click', function() {
        document.getElementById('downloadBtn').style.display = 'none';
    });
    
    // Student navigation (placeholder functionality)
    const students = [
        'Aglugub, Elian Benjamin',
        'Cruz, Maria Santos',
        'Reyes, Juan dela Cruz'
    ];
    
    let currentStudentIndex = 0;
    
    document.querySelectorAll('.student-nav-btn').forEach((btn, index) => {
        btn.addEventListener('click', function() {
            if (index === 0) { // Previous
                currentStudentIndex = (currentStudentIndex - 1 + students.length) % students.length;
            } else { // Next
                currentStudentIndex = (currentStudentIndex + 1) % students.length;
            }
            document.querySelector('.student-name').textContent = students[currentStudentIndex];
        });
    });
</script>
@endpush