<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function centralConnection(): string
    {
        return config(
            'tenancy.database.central_connection',
            config('database.central_connection', config('database.default'))
        );
    }

    public function getConnection()
    {
        // Ensure this migration runs on central connection
        return $this->centralConnection();
    }

    public function up(): void
    {
        Schema::connection($this->centralConnection())->create('landlord_dunning_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Policy');
            // Thresholds and grace periods (days)
            $table->unsignedInteger('warning_threshold_days')->default(5);
            $table->unsignedInteger('suspension_grace_days')->default(7);
            $table->unsignedInteger('termination_grace_days')->default(30);
            // Reminder cadence windows in days before due (e.g., [-7,-3,-1,0,3])
            $table->json('reminder_windows')->nullable();
            // Late fees
            $table->decimal('late_fee_percent', 5, 2)->nullable();
            $table->decimal('late_fee_flat', 10, 2)->nullable();
            // Channels and recipients
            $table->json('warning_channels')->nullable();
            $table->json('suspension_channels')->nullable();
            $table->json('termination_channels')->nullable();
            $table->json('warning_recipients')->nullable();
            $table->json('suspension_recipients')->nullable();
            $table->json('termination_recipients')->nullable();
            // Phone recipients for SMS
            $table->json('warning_phones')->nullable();
            $table->json('suspension_phones')->nullable();
            $table->json('termination_phones')->nullable();
            // Optional webhooks/slack endpoints
            $table->json('warning_webhooks')->nullable();
            $table->json('suspension_webhooks')->nullable();
            $table->json('termination_webhooks')->nullable();
            $table->json('warning_slack_webhooks')->nullable();
            $table->json('suspension_slack_webhooks')->nullable();
            $table->json('termination_slack_webhooks')->nullable();
            // Templates
            $table->json('templates')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->centralConnection())->dropIfExists('landlord_dunning_policies');
    }
};
