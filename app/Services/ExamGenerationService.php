<?php

namespace App\Services;

use App\Models\OnlineExam;
use App\Models\OnlineExamSection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExamGenerationService
{
    /**
     * Handle exam generation for the provided exam ID.
     */
    public function handle(int $examId, array $context = []): void
    {
        $exam = OnlineExam::query()->with(['sections', 'subject'])->find($examId);

        if (! $exam || $exam->creation_method === 'manual') {
            return;
        }

        $exam->forceFill([
            'generation_status' => 'processing',
            'generation_provider' => $this->resolveProvider($exam),
        ])->save();

        try {
            if ($exam->creation_method === 'automatic') {
                $this->generateFromBlueprint($exam, $context);
            } else {
                $this->generateWithAiBridge($exam, $context);
            }

            $metadata = $this->appendMetadata($exam, [
                'last_completed_at' => now()->toIso8601String(),
            ]);

            $exam->forceFill([
                'generation_status' => 'completed',
                'generation_metadata' => $metadata,
            ])->save();
        } catch (\Throwable $e) {
            report($e);

            $metadata = $this->appendMetadata($exam, [
                'last_error' => $e->getMessage(),
                'failed_at' => now()->toIso8601String(),
            ]);

            $exam->forceFill([
                'generation_status' => 'failed',
                'generation_metadata' => $metadata,
            ])->save();
        }
    }

    protected function generateFromBlueprint(OnlineExam $exam, array $context): void
    {
        $topics = $this->normalizeTopics($exam, $context);
        $questionTypes = $this->normalizeQuestionTypes($context);
        $difficulty = $context['difficulty'] ?? 'balanced';

        DB::transaction(function () use ($exam, $topics, $questionTypes, $difficulty): void {
            $orderOffset = (int) ($exam->sections()->max('order') ?? 0);
            $questionCount = max(1, $topics->count() * max(1, count($questionTypes)));
            $marksPerQuestion = max(1, (int) floor($exam->total_marks / $questionCount));

            foreach ($topics as $index => $topic) {
                $section = $exam->sections()->create([
                    'title' => __(':topic Blueprint', ['topic' => Str::title($topic)]),
                    'description' => __('Auto-generated outline for :topic (:difficulty focus).', [
                        'topic' => $topic,
                        'difficulty' => $this->difficultyLabel($difficulty),
                    ]),
                    'order' => $orderOffset + $index + 1,
                ]);

                $this->seedQuestions($section, $topic, $questionTypes, $marksPerQuestion, $difficulty);
            }
        });
    }

    protected function generateWithAiBridge(OnlineExam $exam, array $context): void
    {
        $driver = config('services.exam_generation.driver');

        if (! $driver) {
            $metadata = $this->appendMetadata($exam, [
                'ai_bridge' => [
                    'active' => false,
                    'message' => 'AI driver not configured. Blueprint fallback applied.',
                ],
            ]);

            $exam->forceFill(['generation_metadata' => $metadata])->save();
            $this->generateFromBlueprint($exam, $context);

            return;
        }

        $metadata = $this->appendMetadata($exam, [
            'ai_bridge' => [
                'active' => true,
                'driver' => $driver,
            ],
        ]);

        $exam->forceFill(['generation_metadata' => $metadata])->save();

        if ($driver === 'openai') {
            $this->generateWithOpenAi($exam, $context);
            return;
        }

        // Fallback for unsupported drivers
        $this->generateFromBlueprint($exam, $context);
    }

    protected function generateWithOpenAi(OnlineExam $exam, array $context): void
    {
        $apiKey = config('services.exam_generation.providers.openai.api_key');
        $model = config('services.exam_generation.providers.openai.model', 'gpt-4o-mini');

        if (! $apiKey) {
            throw new \Exception('OpenAI API key not configured.');
        }

        $topics = $this->normalizeTopics($exam, $context);
        $questionTypes = $this->normalizeQuestionTypes($context);
        $difficulty = $context['difficulty'] ?? 'balanced';
        $subjectName = $exam->subject->name ?? 'General Knowledge';

        $prompt = $this->buildOpenAiPrompt($subjectName, $topics, $questionTypes, $difficulty, $exam->total_marks);

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert exam setter. Generate a structured exam in JSON format.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        $data = json_decode($content, true);

        if (! isset($data['sections']) || ! is_array($data['sections'])) {
            throw new \Exception('Invalid JSON structure received from OpenAI.');
        }

        $this->persistGeneratedContent($exam, $data['sections']);
    }

    protected function buildOpenAiPrompt(string $subject, $topics, array $questionTypes, string $difficulty, int $totalMarks): string
    {
        $topicsStr = $topics->implode(', ');
        $typesStr = implode(', ', $questionTypes);

        return <<<EOT
Generate an exam for the subject "{$subject}".
Topics: {$topicsStr}
Difficulty: {$difficulty}
Question Types: {$typesStr}
Total Marks: {$totalMarks}

Return a JSON object with a "sections" array. Each section should have a "title", "description", and "questions" array.
Each question must have:
- "type": one of [{$typesStr}]
- "question": the question text
- "options": object with keys A, B, C, D (only for multiple_choice)
- "correct_answer": string (e.g., "A" for multiple_choice, "True" for true_false, or the answer text)
- "marks": integer
- "explanation": brief explanation of the answer

Ensure the total marks across all questions sum up to approximately {$totalMarks}.
EOT;
    }

    protected function persistGeneratedContent(OnlineExam $exam, array $sections): void
    {
        DB::transaction(function () use ($exam, $sections) {
            $orderOffset = (int) ($exam->sections()->max('order') ?? 0);

            foreach ($sections as $sectionIndex => $sectionData) {
                $section = $exam->sections()->create([
                    'title' => $sectionData['title'] ?? 'Section ' . ($sectionIndex + 1),
                    'description' => $sectionData['description'] ?? '',
                    'order' => $orderOffset + $sectionIndex + 1,
                ]);

                foreach ($sectionData['questions'] ?? [] as $qIndex => $qData) {
                    $section->questions()->create([
                        'online_exam_id' => $exam->id,
                        'type' => $qData['type'] ?? 'short_answer',
                        'question' => $qData['question'],
                        'options' => $qData['options'] ?? null,
                        'correct_answer' => $qData['correct_answer'] ?? null,
                        'marks' => $qData['marks'] ?? 1,
                        'order' => $qIndex + 1,
                        'explanation' => $qData['explanation'] ?? null,
                    ]);
                }
            }
        });
    }

    protected function seedQuestions(OnlineExamSection $section, string $topic, array $questionTypes, int $marks, string $difficulty): void
    {
        if (empty($questionTypes)) {
            $questionTypes = ['short_answer', 'essay'];
        }

        foreach ($questionTypes as $offset => $type) {
            $section->questions()->create([
                'online_exam_id' => $section->online_exam_id,
                'type' => $type,
                'question' => $this->buildPrompt($type, $topic, $difficulty, $offset + 1),
                'options' => $this->suggestOptions($type, $topic),
                'correct_answer' => $type === 'multiple_choice' ? 'A' : null,
                'marks' => $marks,
                'order' => ($section->questions()->max('order') ?? 0) + 1,
                'explanation' => __('Auto-generated placeholder. Review and refine before publishing.'),
            ]);
        }
    }

    protected function buildPrompt(string $type, string $topic, string $difficulty, int $sequence): string
    {
        return match ($type) {
            'multiple_choice' => __('Which statement best represents :topic at level :level?', [
                'topic' => $topic,
                'level' => Str::title($difficulty),
            ]),
            'true_false' => __('True or False: :topic is essential for mastering this unit.', ['topic' => $topic]),
            'fill_blank' => __('Complete the statement about :topic (item :number).', [
                'topic' => $topic,
                'number' => $sequence,
            ]),
            'essay' => __('Discuss :topic in depth, referencing at least two classroom resources.'),
            default => __('Explain :topic in your own words.', ['topic' => $topic]),
        };
    }

    protected function suggestOptions(string $type, string $topic): ?array
    {
        if ($type !== 'multiple_choice') {
            return null;
        }

        return [
            'A' => __('Core principle of :topic.', ['topic' => $topic]),
            'B' => __('Secondary detail unrelated to :topic.'),
            'C' => __('Historical fact loosely tied to :topic.'),
            'D' => __('Distractor choice for :topic.'),
        ];
    }

    protected function normalizeTopics(OnlineExam $exam, array $context)
    {
        $raw = $context['syllabus_topics']
            ?? $context['topics']
            ?? data_get($exam->generation_metadata, 'last_request.syllabus_topics');

        $topics = collect(is_string($raw) ? preg_split('/[\n,]+/', $raw) : (array) $raw)
            ->map(fn ($topic) => trim((string) $topic))
            ->filter()
            ->values();

        if ($topics->isEmpty() && $exam->subject) {
            $topics = collect([$exam->subject->name]);
        }

        if ($topics->isEmpty()) {
            $topics = collect(['General Competencies']);
        }

        return $topics->take(4);
    }

    protected function normalizeQuestionTypes(array $context): array
    {
        $types = Arr::wrap($context['question_types'] ?? data_get($context, 'last_request.question_types'));

        $types = array_values(array_filter($types, function ($type) {
            return in_array($type, ['multiple_choice', 'true_false', 'short_answer', 'essay', 'fill_blank'], true);
        }));

        if (empty($types)) {
            $types = ['short_answer', 'essay'];
        }

        return $types;
    }

    protected function difficultyLabel(string $difficulty): string
    {
        return match ($difficulty) {
            'foundation' => __('Foundation'),
            'advanced' => __('Advanced'),
            default => __('Balanced'),
        };
    }

    protected function appendMetadata(OnlineExam $exam, array $payload): array
    {
        $existing = $exam->generation_metadata ?? [];

        return array_replace_recursive($existing, $payload);
    }

    protected function resolveProvider(OnlineExam $exam): ?string
    {
        return match ($exam->creation_method) {
            'automatic' => 'blueprint',
            'ai' => config('services.exam_generation.driver', 'ai-bridge'),
            default => null,
        };
    }
}
