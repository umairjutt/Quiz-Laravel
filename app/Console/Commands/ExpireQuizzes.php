<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireQuizzes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'quizzes:expire';
    protected $description = 'Expire quizzes that have passed their expiration date';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expiredAttempts = QuizAttempt::where('expires_at', '<', $now)
                            ->whereNull('score')
                            ->get();

        foreach ($expiredAttempts as $attempt) {
            // Logic to handle expired quizzes, e.g., mark as expired
            // For example, send notification or update status
            // $attempt->status = 'expired';
            // $attempt->save();
        }

        $this->info('Expired quizzes have been processed.');

    }
}
