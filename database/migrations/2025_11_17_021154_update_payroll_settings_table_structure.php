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
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->string('category')->after('id');
            $table->string('type')->default('text')->after('value');
            $table->string('label')->after('type');
            $table->text('description')->nullable()->after('label');
            $table->boolean('is_active')->default(true)->after('description');
            $table->integer('sort_order')->default(0)->after('is_active');
            $table->json('validation_rules')->nullable()->after('sort_order');
            $table->json('options')->nullable()->after('validation_rules');

            // Add indexes
            $table->index(['category', 'is_active']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropIndex(['category', 'is_active']);
            $table->dropIndex(['key']);

            $table->dropColumn([
                'category',
                'type',
                'label',
                'description',
                'is_active',
                'sort_order',
                'validation_rules',
                'options'
            ]);
        });
    }
};
