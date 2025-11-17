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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->text('address')->nullable()->after('date_of_birth');
            $table->string('qualification')->nullable()->after('address');
            $table->string('specialization')->nullable()->after('qualification');
            $table->string('profile_photo')->nullable()->after('specialization');
            $table->json('notification_preferences')->nullable()->after('profile_photo');
            $table->string('emergency_contact_name')->nullable()->after('notification_preferences');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'gender',
                'date_of_birth',
                'address',
                'qualification',
                'specialization',
                'profile_photo',
                'notification_preferences',
                'emergency_contact_name',
                'emergency_contact_phone',
            ]);
        });
    }
};
