<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('exercises', 'content')) {
                $table->longText('content')->nullable()->after('instructions');
            }
            if (!Schema::hasColumn('exercises', 'attachments')) {
                $table->json('attachments')->nullable()->after('submission_type');
            }
            if (!Schema::hasColumn('exercises', 'auto_grade')) {
                $table->boolean('auto_grade')->default(false)->after('attachments');
            }
            if (!Schema::hasColumn('exercises', 'allow_file_upload')) {
                $table->boolean('allow_file_upload')->default(true)->after('auto_grade');
            }
            if (!Schema::hasColumn('exercises', 'allow_text_response')) {
                $table->boolean('allow_text_response')->default(true)->after('allow_file_upload');
            }
            if (!Schema::hasColumn('exercises', 'show_answers_after_submit')) {
                $table->boolean('show_answers_after_submit')->default(false)->after('allow_text_response');
            }
            if (!Schema::hasColumn('exercises', 'max_file_size_mb')) {
                $table->integer('max_file_size_mb')->default(10)->after('show_answers_after_submit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $columns = [
                'content',
                'attachments',
                'auto_grade',
                'allow_file_upload',
                'allow_text_response',
                'show_answers_after_submit',
                'max_file_size_mb',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exercises', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
