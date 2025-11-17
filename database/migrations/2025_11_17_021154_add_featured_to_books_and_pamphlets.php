<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_published');
        });
        Schema::table('pamphlets', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
        Schema::table('pamphlets', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
