@can('admin-access')
@extends('layouts.Admin.app')

@section('content')
<style>
    .exam-view-container {
        background-color: #e8f1f5;
        min-height: 100vh;
        padding: 30px;
    }

    .exam-header {
        background-color: #6ba5b3;
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .exam-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .approve-btn {
        background-color: #5a94aa;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .approve-btn:hover {
        background-color: #4a7d8f;
    }

    .approve-btn:disabled {
        background-color: #95a5a6;
        cursor: not-allowed;
    }

    .tabs-container {
        background: white;
        border-bottom: 2px solid #e0e0e0;
    }

    .tabs {
        display: flex;
        gap: 0;
    }

    .tab {
        padding: 18px 40px;
        background: transparent;
        border: none;
        color: #95a5a6;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab.active {
        color: #6ba5b3;
        border-bottom-color: #6ba5b3;
    }

    .questions-container {
        background: white;
        padding: 30px;
        border-radius: 0 0 12px 12px;
        min-height: 500px;
    }

    .section-header {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .section-directions {
        font-size: 14px;
        color: #5a6c7d;
    }

    .question-card {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .question-header {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .answer-option {
        border: 2px solid #d1d5db;
        border-radius: 25px;
        padding: 12px 20px;
        margin-bottom: 10px;
        font-size: 15px;
        color: #333;
        transition: all 0.2s;
    }

    .answer-option.correct {
        background-color: #4caf50;
        color: white;
        border-color: #4caf50;
        font-weight: 600;
    }

    .back-btn {
        background-color: #6ba5b3;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        background-color: #5a94aa;
    }

    .no-questions {
        text-align: center;
        padding: 60px;
        color: #95a5a6;
        font-size: 18px;
    }
</style>

<div id="mainContent" class="main exam-view-container">
    <a href="{{ route('admin.exam-statistics.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Exams
    </a>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
        <!-- Exam Header -->
        <div class="exam-header">
            <h1 class="exam-title">{{ $exam->exam_title }}</h1>
            @if($exam->status === 'draft' || $exam->status === 'pending')
            <button class="approve-btn" onclick="approveExam({{ $exam->exam_id }})">
                Approve Exam
            </button>
            @else
            <button class="approve-btn" disabled>
                {{ ucfirst($exam->status) }}
            </button>
            @endif
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active">Questions</button>
            </div>
        </div>

        <!-- Questions Container -->
        <div class="questions-container">
            @if($exam->sections->count() > 0)
                @foreach($exam->sections as $section)
                    <!-- Section Header -->
                    <div class="section-header">
                        <div class="section-title">{{ $section->section_title }}</div>
                        @if($section->section_directions)
                        <div class="section-directions">{{ $section->section_directions }}</div>
                        @endif
                    </div>

                    <!-- Questions in this section -->
                    @if($section->examItems->count() > 0)
                        @foreach($section->examItems as $index => $item)
                            <div class="question-card">
                                <div class="question-header">
                                    Question {{ $index + 1 }}. {{ $item->question }}
                                </div>

                                @if($item->item_type === 'mcq')
                                    @php
                                        $options = is_array($item->options_array) ? $item->options_array : [];
                                        $correctAnswer = is_array($item->answer_array) && isset($item->answer_array['correct']) 
                                            ? $item->answer_array['correct'] 
                                            : null;
                                    @endphp

                                    @foreach($options as $key => $option)
                                        <div class="answer-option {{ strtolower($correctAnswer) === strtolower($key) ? 'correct' : '' }}">
                                            {{ $key }}. {{ $option }}
                                        </div>
                                    @endforeach

                                @elseif($item->item_type === 'torf')
                                    @php
                                        $correctAnswer = is_array($item->answer_array) && isset($item->answer_array['correct']) 
                                            ? strtolower($item->answer_array['correct']) 
                                            : null;
                                    @endphp

                                    <div class="answer-option {{ $correctAnswer === 'true' ? 'correct' : '' }}">
                                        True
                                    </div>
                                    <div class="answer-option {{ $correctAnswer === 'false' ? 'correct' : '' }}">
                                        False
                                    </div>

                                @elseif($item->item_type === 'essay')
                                    <div class="answer-option" style="border-radius: 10px; padding: 15px;">
                                        <em>Essay question - No predefined answer</em>
                                    </div>

                                @elseif($item->item_type === 'iden' || $item->item_type === 'enum')
                                    <div class="answer-option correct" style="border-radius: 10px;">
                                        Expected Answer: {{ $item->expected_answer ?? 'N/A' }}
                                    </div>

                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="no-questions">
                            <i class="fas fa-question-circle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>No questions in this section yet</p>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="no-questions">
                    <i class="fas fa-clipboard-list" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>No sections or questions added to this exam yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function approveExam(examId) {
        if (confirm('Are you sure you want to approve this exam?')) {
            fetch(`/admin/exam-statistics/${examId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while approving the exam');
            });
        }
    }
</script>
@endsection
@endcan