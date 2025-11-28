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
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'password_expires_at')) {
                $table->timestamp('password_expires_at')->nullable()->after('password_changed_at');
            }

            if (! Schema::hasColumn('users', 'password_history')) {
                $table->json('password_history')->nullable()->after('password_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = collect(['password_changed_at', 'password_expires_at', 'password_history'])
                ->filter(fn ($column) => Schema::hasColumn('users', $column))
                ->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
