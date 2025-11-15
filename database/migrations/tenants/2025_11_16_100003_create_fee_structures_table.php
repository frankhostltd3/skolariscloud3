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
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('fee_name');
            $table->string('fee_type'); // tuition, exam, library, transport, etc
            $table->decimal('amount', 15, 2);
            $table->string('academic_year');
            $table->string('term')->nullable();
            $table->string('class')->nullable(); // null means applies to all classes
            $table->date('due_date')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'academic_year']);
            $table->index(['school_id', 'class']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
