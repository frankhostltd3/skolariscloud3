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
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('gender');
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('date_of_birth');
            }

            if (! Schema::hasColumn('users', 'qualification')) {
                $table->string('qualification')->nullable()->after('address');
            }

            if (! Schema::hasColumn('users', 'specialization')) {
                $table->string('specialization')->nullable()->after('qualification');
            }

            if (! Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('specialization');
            }

            if (! Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('profile_photo');
            }

            if (! Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('notification_preferences');
            }

            if (! Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
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
            $columns = collect([
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
            ])->filter(fn ($column) => Schema::hasColumn('users', $column))->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
