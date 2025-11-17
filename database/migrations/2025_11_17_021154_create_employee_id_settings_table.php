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
        Schema::create('employee_id_settings', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('card_width')->default('85.6'); // mm
            $table->string('card_height')->default('54'); // mm
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#000000');
            $table->string('header_text')->default('Employee ID Card');
            $table->string('header_color')->default('#2563eb');
            $table->string('logo_path')->nullable();
            $table->json('fields_to_display');
            $table->boolean('include_qr_code')->default(true);
            $table->string('qr_code_position')->default('bottom-right'); // top-left, top-right, bottom-left, bottom-right
            $table->string('qr_code_size')->default('80'); // pixels
            $table->boolean('include_photo')->default(true);
            $table->string('photo_position')->default('top-right'); // top-left, top-right, center
            $table->string('photo_size')->default('100'); // pixels
            $table->string('font_family')->default('Arial, sans-serif');
            $table->string('font_size')->default('12');
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
        Schema::dropIfExists('employee_id_settings');
    }
};
