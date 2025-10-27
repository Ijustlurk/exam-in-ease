<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ExamItem;
use App\Models\ExamAnswer;

// Test True/False in database
echo "=== Checking True/False in Database ===\n";
$torfItem = ExamItem::where('item_type', 'torf')->first();
if ($torfItem) {
    echo "Question: {$torfItem->question}\n";
    echo "Correct Answer (in exam_items): " . json_encode($torfItem->answer) . "\n\n";
    
    // Check student answers for this question
    $studentAnswers = ExamAnswer::where('item_id', $torfItem->item_id)->get();
    if ($studentAnswers->count() > 0) {
        echo "Student Answers (in exam_answers):\n";
        foreach ($studentAnswers as $ans) {
            echo "  - answer_text: '{$ans->answer_text}'\n";
            echo "    is_correct: " . ($ans->is_correct ? 'TRUE' : 'FALSE') . "\n";
            echo "    points_earned: {$ans->points_earned}\n\n";
        }
    } else {
        echo "No student answers found for this question.\n";
    }
}

echo "\n=== Checking MCQ in Database ===\n";
$mcqItem = ExamItem::where('item_type', 'mcq')->first();
if ($mcqItem) {
    echo "Question: {$mcqItem->question}\n";
    echo "Correct Answer (in exam_items): " . json_encode($mcqItem->answer) . "\n\n";
    
    // Check student answers for this question
    $studentAnswers = ExamAnswer::where('item_id', $mcqItem->item_id)->get();
    if ($studentAnswers->count() > 0) {
        echo "Student Answers (in exam_answers):\n";
        foreach ($studentAnswers as $ans) {
            echo "  - answer_text: '{$ans->answer_text}'\n";
            echo "    is_correct: " . ($ans->is_correct ? 'TRUE' : 'FALSE') . "\n";
            echo "    points_earned: {$ans->points_earned}\n\n";
        }
    } else {
        echo "No student answers found for this question.\n";
    }
}
