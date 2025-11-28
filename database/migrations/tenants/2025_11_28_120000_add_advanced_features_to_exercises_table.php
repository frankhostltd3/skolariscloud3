<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('exercises', 'rubric')) {
                $table->json('rubric')->nullable();
            }
            if (!Schema::hasColumn('exercises', 'plagiarism_check_enabled')) {
                $table->boolean('plagiarism_check_enabled')->default(false);
            }
            if (!Schema::hasColumn('exercises', 'peer_review_enabled')) {
                $table->boolean('peer_review_enabled')->default(false);
            }
            if (!Schema::hasColumn('exercises', 'peer_review_count')) {
                $table->integer('peer_review_count')->default(1);
            }
            if (!Schema::hasColumn('exercises', 'status')) {
                $table->enum('status', ['draft', 'active', 'closed', 'archived'])->default('active');
            }
            if (!Schema::hasColumn('exercises', 'version')) {
                $table->integer('version')->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $columns = ['rubric', 'plagiarism_check_enabled', 'peer_review_enabled', 'peer_review_count', 'status', 'version'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('exercises', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
