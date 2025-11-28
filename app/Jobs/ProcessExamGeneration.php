<?php

namespace App\Jobs;

use App\Services\ExamGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExamGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $examId,
        public array $context = [],
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ExamGenerationService $service): void
    {
        $service->handle($this->examId, $this->context);
    }
}
