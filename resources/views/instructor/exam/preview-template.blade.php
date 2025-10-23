<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Exam Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Century Gothic, Arial, sans-serif; 
            font-size: 11pt; 
            line-height: 1.5;
            padding: 0;
            margin: 0;
            background: white;
        }
        .exam-header { text-align: center; margin-bottom: 20px; }
        .exam-header p { margin: 2px 0; }
        .exam-subject { font-weight: bold; }
        .student-info { margin-bottom: 20px; }
        .info-line { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .exam-section { margin: 20px 0; }
        .exam-section > div:first-child { margin-bottom: 10px; }
        .question-item { margin: 15px 0; }
        .question-text { margin-bottom: 8px; }
        .question-options { margin-left: 40px; margin-top: 5px; }
        .option-item { margin: 3px 0; }
        .identification-answer { margin: 8px 0 8px 20px; font-style: italic; }
        .enum-table { 
            width: 60%; 
            margin: 10px 0; 
            border-collapse: collapse; 
        }
        .enum-table td { 
            border: 1px solid #000; 
            padding: 20px;
            width: 50%;
        }
        .footer-section { margin-top: 60px; }
        .footer-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px; 
        }
        .footer-row > div { flex: 1; }
        .footer-row > div:last-child { text-align: right; }
        .page-footer { text-align: center; margin-top: 30px; font-size: 10pt; }
        @media print {
            body { background: white; }
        }
    </style>
</head>
<body>
<div class="exam-header">
    <p>{{ strtoupper($exam->term ?? 'PRELIM') }}</p>
    <p>First Semester, A.Y. {{ date('Y') }}-{{ date('Y')+1 }}</p>
    <p class="exam-subject">{{ strtoupper($exam->subject->subject_name ?? $exam->exam_title) }}</p>
</div>

<div class="student-info">
    <div class="info-line">
        <span>Name: ______________________________</span>
        <span>Score: __________</span>
    </div>
    <div class="info-line">
        <span>Year and Section: ______________________________</span>
        <span>Date: __________</span>
    </div>
</div>

@php $qNum = 1; $sNum = 1; @endphp
@foreach($exam->sections as $section)
<div class="exam-section">
    @if($section->section_title)
    <div><strong>{{ ['I','II','III','IV','V'][$sNum-1] ?? $sNum }}. {{ $section->section_title }}</strong></div>
    @endif
    @if($section->section_directions)
    <div>{{ $section->section_directions }}</div>
    @endif
    @foreach($section->items as $item)
    <div class="question-item">
        @if($item->item_type === 'mcq')
        <div class="question-text">_______{{ $qNum }}. {{ strip_tags($item->question) }}</div>
        <div class="question-options">
            @php $opts = json_decode($item->options, true); @endphp
            @if(is_array($opts))
            @foreach($opts as $k => $opt)
            <div class="option-item">{{ chr(65+$k) }}. {{ strip_tags($opt) }}</div>
            @endforeach
            @endif
        </div>
        
        @elseif($item->item_type === 'torf')
        <div class="question-text">_______{{ $qNum }}. {{ strip_tags($item->question) }}</div>
        <div class="question-options">
            <div class="option-item">A. TRUE</div>
            <div class="option-item">B. FALSE</div>
        </div>
        
        @elseif(in_array($item->item_type, ['iden', 'essay']))
        <div class="question-text">{{ $qNum }}. {{ strip_tags($item->question) }}</div>
        <div class="identification-answer">(Answer in the blank) _______________________________</div>
        
        @elseif($item->item_type === 'enum')
        <div class="question-text">{{ $qNum }}. {{ strip_tags($item->question) }}</div>
        <table class="enum-table">
            <tr><td>1.</td><td>2.</td></tr>
            <tr><td>3.</td><td>4.</td></tr>
            <tr><td>5.</td><td>6.</td></tr>
        </table>
        
        @else
        <div class="question-text">{{ $qNum }}. {{ strip_tags($item->question) }}</div>
        @endif
    </div>
    @php $qNum++; @endphp
    @endforeach
</div>
@php $sNum++; @endphp
@endforeach

<div class="footer-section">
    <div class="footer-row">
        <div>Prepared By:</div>
        <div>Checked by:</div>
    </div>
    <div class="footer-row">
        <div><strong>{{ strtoupper($exam->teacher->first_name . ' ' . $exam->teacher->last_name) }}</strong></div>
        <div><strong>JULIETA B. BABAS, DIT</strong></div>
    </div>
    <div class="footer-row">
        <div><strong>Faculty</strong></div>
        <div><strong>College Dean</strong></div>
    </div>
</div>

<div class="page-footer">Page 1 out of 1</div>
</body>
</html>
