<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveExpiredExams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:archive-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically archive exams that have passed their schedule_end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired exams...');

        // Get all ongoing or approved exams that have passed their end schedule
        $expiredExams = Exam::whereIn('status', ['ongoing', 'approved'])
            ->whereNotNull('schedule_end')
            ->where('schedule_end', '<', Carbon::now())
            ->get();

        if ($expiredExams->isEmpty()) {
            $this->info('No expired exams found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredExams as $exam) {
            try {
                $exam->update(['status' => 'archived']);
                $count++;
                $this->line("✓ Archived: {$exam->exam_title} (ID: {$exam->exam_id})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to archive exam {$exam->exam_id}: {$e->getMessage()}");
            }
        }

        $this->info("\nSuccessfully archived {$count} exam(s).");
        return 0;
    }
}
