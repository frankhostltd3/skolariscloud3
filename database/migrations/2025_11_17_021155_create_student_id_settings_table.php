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
        Schema::create('student_id_settings', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->decimal('card_width', 8, 2)->default(85.6); // in mm
            $table->decimal('card_height', 8, 2)->default(54); // in mm
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#000000');
            $table->string('header_text')->default('Student ID Card');
            $table->string('header_color')->default('#2563eb');
            $table->string('logo_path')->nullable();
            $table->json('fields_to_display')->nullable();
            $table->boolean('include_qr_code')->default(true);
            $table->string('qr_code_position')->default('bottom-right');
            $table->integer('qr_code_size')->default(80);
            $table->boolean('include_photo')->default(true);
            $table->string('photo_position')->default('top-left');
            $table->integer('photo_size')->default(100);
            $table->string('font_family')->default('Arial, sans-serif');
            $table->integer('font_size')->default(12);
            $table->json('layout_settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_id_settings');
    }
};
