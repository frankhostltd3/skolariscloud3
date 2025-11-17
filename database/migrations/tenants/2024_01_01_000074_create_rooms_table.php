<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('name', 100);
                $table->string('code', 50)->nullable();
                $table->unsignedSmallInteger('capacity')->nullable();
                $table->string('type', 50)->nullable(); // lab, classroom, hall, etc.
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['school_id', 'name']);
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
