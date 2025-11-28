<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_exams', function (Blueprint $table): void {
            if (! Schema::hasColumn('online_exams', 'creation_method')) {
                $table->enum('creation_method', ['manual', 'automatic', 'ai'])
                    ->default('manual')
                    ->after('teacher_id');
            }

            if (! Schema::hasColumn('online_exams', 'activation_mode')) {
                $table->enum('activation_mode', ['manual', 'schedule', 'auto'])
                    ->default('schedule')
                    ->after('creation_method');
            }

            if (! Schema::hasColumn('online_exams', 'approval_status')) {
                $table->enum('approval_status', ['draft', 'pending_review', 'changes_requested', 'approved', 'rejected'])
                    ->default('draft')
                    ->after('status');
            }

            if (! Schema::hasColumn('online_exams', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('approval_status');
            }

            if (! Schema::hasColumn('online_exams', 'reviewed_by')) {
                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->after('review_notes')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('online_exams', 'reviewed_at')) {
                $table->dateTime('reviewed_at')->nullable()->after('reviewed_by');
            }

            if (! Schema::hasColumn('online_exams', 'submitted_for_review_at')) {
                $table->dateTime('submitted_for_review_at')->nullable()->after('reviewed_at');
            }

            if (! Schema::hasColumn('online_exams', 'generation_status')) {
                $table->enum('generation_status', ['idle', 'requested', 'processing', 'completed', 'failed'])
                    ->default('idle')
                    ->after('submitted_for_review_at');
            }

            if (! Schema::hasColumn('online_exams', 'generation_provider')) {
                $table->string('generation_provider')->nullable()->after('generation_status');
            }

            if (! Schema::hasColumn('online_exams', 'generation_metadata')) {
                $table->json('generation_metadata')->nullable()->after('generation_provider');
            }

            if (! Schema::hasColumn('online_exams', 'activated_at')) {
                $table->dateTime('activated_at')->nullable()->after('generation_metadata');
            }

            if (! Schema::hasColumn('online_exams', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('activated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('online_exams', function (Blueprint $table): void {
            if (Schema::hasColumn('online_exams', 'completed_at')) {
                $table->dropColumn('completed_at');
            }

            if (Schema::hasColumn('online_exams', 'activated_at')) {
                $table->dropColumn('activated_at');
            }

            if (Schema::hasColumn('online_exams', 'generation_metadata')) {
                $table->dropColumn('generation_metadata');
            }

            if (Schema::hasColumn('online_exams', 'generation_provider')) {
                $table->dropColumn('generation_provider');
            }

            if (Schema::hasColumn('online_exams', 'generation_status')) {
                $table->dropColumn('generation_status');
            }

            if (Schema::hasColumn('online_exams', 'submitted_for_review_at')) {
                $table->dropColumn('submitted_for_review_at');
            }

            if (Schema::hasColumn('online_exams', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }

            if (Schema::hasColumn('online_exams', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            if (Schema::hasColumn('online_exams', 'review_notes')) {
                $table->dropColumn('review_notes');
            }

            if (Schema::hasColumn('online_exams', 'approval_status')) {
                $table->dropColumn('approval_status');
            }

            if (Schema::hasColumn('online_exams', 'activation_mode')) {
                $table->dropColumn('activation_mode');
            }

            if (Schema::hasColumn('online_exams', 'creation_method')) {
                $table->dropColumn('creation_method');
            }
        });
    }
};
