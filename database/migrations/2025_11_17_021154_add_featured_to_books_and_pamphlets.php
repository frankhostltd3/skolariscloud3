<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                if (! Schema::hasColumn('books', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('is_published');
                }
            });
        }

        if (Schema::hasTable('pamphlets')) {
            Schema::table('pamphlets', function (Blueprint $table) {
                if (! Schema::hasColumn('pamphlets', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('is_published');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                if (Schema::hasColumn('books', 'is_featured')) {
                    $table->dropColumn('is_featured');
                }
            });
        }

        if (Schema::hasTable('pamphlets')) {
            Schema::table('pamphlets', function (Blueprint $table) {
                if (Schema::hasColumn('pamphlets', 'is_featured')) {
                    $table->dropColumn('is_featured');
                }
            });
        }
    }
};
