<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Remove old columns and constraints
            if (Schema::hasColumn('expenses', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }

            if (Schema::hasColumn('expenses', 'expense_name')) {
                $table->dropColumn('expense_name');
            }

            if (Schema::hasColumn('expenses', 'vendor')) {
                $table->dropColumn('vendor');
            }

            // Rename category_id to expense_category_id if needed
            if (Schema::hasColumn('expenses', 'category_id') && !Schema::hasColumn('expenses', 'expense_category_id')) {
                $table->dropForeign(['category_id']);
                $table->renameColumn('category_id', 'expense_category_id');
            }
        });

        // Add new columns in a separate schema call
        Schema::table('expenses', function (Blueprint $table) {
            // Add title if not exists
            if (!Schema::hasColumn('expenses', 'title')) {
                $table->string('title')->after('id');
            }

            // Reorder description if exists, or add it
            if (Schema::hasColumn('expenses', 'description')) {
                // Already exists, just ensure it's after title
            } else {
                $table->text('description')->nullable()->after('title');
            }

            // Add currency_id if not exists
            if (!Schema::hasColumn('expenses', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->after('amount');
            }

            // Add vendor_name if not exists
            if (!Schema::hasColumn('expenses', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('reference_number');
            }

            // Add vendor_contact if not exists
            if (!Schema::hasColumn('expenses', 'vendor_contact')) {
                $table->string('vendor_contact')->nullable()->after('vendor_name');
            }

            // Add receipt_path if not exists
            if (!Schema::hasColumn('expenses', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('vendor_contact');
            }

            // Modify payment_method to enum if it's a string
            if (Schema::hasColumn('expenses', 'payment_method')) {
                DB::statement("ALTER TABLE `expenses` MODIFY `payment_method` ENUM('cash', 'bank_transfer', 'credit_card', 'debit_card', 'check', 'online_payment', 'other') DEFAULT 'cash'");
            }

            // Modify status to enum if it's a string
            if (Schema::hasColumn('expenses', 'status')) {
                DB::statement("ALTER TABLE `expenses` MODIFY `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
            }

            // Add approved_at if not exists
            if (!Schema::hasColumn('expenses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            // Add rejected_reason if not exists
            if (!Schema::hasColumn('expenses', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('approved_at');
            }

            // Add notes if not exists
            if (!Schema::hasColumn('expenses', 'notes')) {
                $table->text('notes')->nullable()->after('rejected_reason');
            }

            // Add tenant_id if not exists
            if (!Schema::hasColumn('expenses', 'tenant_id')) {
                $table->string('tenant_id')->after('created_by');
            }

            // Add soft deletes if not exists
            if (!Schema::hasColumn('expenses', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add foreign keys - wrap in try-catch to handle if they already exist
        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'currency_id')) {
                    $table->foreign('currency_id')->references('id')->on('currencies');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'expense_category_id')) {
                    $table->foreign('expense_category_id')->references('id')->on('expense_categories');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'approved_by')) {
                    $table->foreign('approved_by')->references('id')->on('users');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'created_by')) {
                    $table->foreign('created_by')->references('id')->on('users');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Remove new columns
            $columnsToRemove = [
                'tenant_id', 'notes', 'rejected_reason', 'approved_at',
                'receipt_path', 'vendor_contact', 'vendor_name', 'currency_id', 'title', 'deleted_at'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    if ($column === 'currency_id') {
                        $table->dropForeign(['currency_id']);
                    }
                    $table->dropColumn($column);
                }
            }

            // Rename expense_category_id back to category_id
            if (Schema::hasColumn('expenses', 'expense_category_id')) {
                $table->dropForeign(['expense_category_id']);
                $table->renameColumn('expense_category_id', 'category_id');
            }

            // Add back old columns
            if (!Schema::hasColumn('expenses', 'school_id')) {
                $table->foreignId('school_id')->after('id')->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('expenses', 'expense_name')) {
                $table->string('expense_name')->after('expense_category_id');
            }

            if (!Schema::hasColumn('expenses', 'vendor')) {
                $table->string('vendor')->nullable()->after('reference_number');
            }
        });
    }
};
