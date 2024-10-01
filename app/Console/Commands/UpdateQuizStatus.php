<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quiz;
use Carbon\Carbon;

class UpdateQuizStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of quizzes based on activation and expiration times';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get current time
        $currentTime = Carbon::now();

        // Find all quizzes that need to be activated or expired
        $quizzes = Quiz::where('activate_at', '<=', $currentTime)
                        ->orWhere('expires_at', '<=', $currentTime)
                        ->get();

        // Update the status based on the time
        foreach ($quizzes as $quiz) {
            if ($quiz->activate_at <= $currentTime && $quiz->status != 'active') {
                $quiz->status = 'active';
                $quiz->save();
                $this->info('Quiz ID ' . $quiz->id . ' activated.');
            }

            if ($quiz->expires_at <= $currentTime && $quiz->status != 'expired') {
                $quiz->status = 'expired';
                $quiz->save();
                $this->info('Quiz ID ' . $quiz->id . ' expired.');
            }
        }
    }
}
