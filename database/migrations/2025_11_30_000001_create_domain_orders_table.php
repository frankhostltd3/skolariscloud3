<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('domain_name');
            $table->string('domain_type')->default('custom'); // subdomain, custom, transfer
            $table->string('tld', 20); // .com, .school, .academy, etc.

            // Contact Information
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();

            // Billing Details
            $table->string('billing_entity');
            $table->string('billing_cycle')->default('annual'); // annual, biennial, triennial
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method')->nullable(); // mpesa, card, invoice
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_reference')->nullable();

            // Order Status
            $table->string('status')->default('pending'); // pending, reviewing, approved, rejected, active, expired, cancelled
            $table->text('purchase_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // DNS Configuration
            $table->string('dns_assignee')->default('skolaris'); // skolaris, customer, third-party
            $table->json('dns_records')->nullable(); // Store generated DNS records
            $table->boolean('dns_verified')->default(false);
            $table->timestamp('dns_verified_at')->nullable();
            $table->string('verification_token')->nullable();

            // SSL Configuration
            $table->boolean('ssl_enabled')->default(false);
            $table->string('ssl_provider')->nullable(); // letsencrypt, cloudflare, custom
            $table->string('ssl_status')->nullable(); // pending, active, expired, failed
            $table->timestamp('ssl_issued_at')->nullable();
            $table->timestamp('ssl_expires_at')->nullable();

            // Domain Registration
            $table->string('registrar')->nullable(); // namecheap, cloudflare, etc.
            $table->string('registrar_order_id')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('renewal_notified_at')->nullable();

            // Routing
            $table->boolean('routing_active')->default(false);
            $table->timestamp('activated_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('payment_status');
            $table->index('dns_verified');
            $table->index('expires_at');
            $table->unique(['domain_name', 'tld']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_orders');
    }
};
