<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('biometric_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->morphs('user'); // user_type and user_id (student or staff)
            $table->string('biometric_type')->default('fingerprint'); // fingerprint, face, iris
            $table->integer('finger_position')->nullable(); // 1-10 for fingerprint
            $table->text('template_data'); // Encrypted biometric template
            $table->string('device_id')->nullable();
            $table->integer('quality_score')->nullable(); // 0-100
            $table->timestamp('enrolled_at');
            $table->unsignedBigInteger('enrolled_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'user_type', 'user_id']);
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('biometric_templates');
    }
};
