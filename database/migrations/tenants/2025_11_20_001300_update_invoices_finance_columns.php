<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'student_id')) {
                try {
                    $table->dropForeign(['student_id']);
                } catch (\Throwable $e) {
                    try {
                        $table->dropForeign('invoices_student_id_foreign');
                    } catch (\Throwable $ignored) {
                        // Constraint already removed or never existed
                    }
                }
                $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('invoices', 'fee_structure_id')) {
                $table->foreignId('fee_structure_id')
                    ->nullable()
                    ->after('student_id')
                    ->constrained('fee_structures')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
            }

            if (! Schema::hasColumn('invoices', 'balance')) {
                $table->decimal('balance', 12, 2)->default(0)->after('paid_amount');
            }

            if (! Schema::hasColumn('invoices', 'academic_year')) {
                $table->string('academic_year', 191)->nullable()->after('status');
            }

            if (! Schema::hasColumn('invoices', 'term')) {
                $table->string('term', 191)->nullable()->after('academic_year');
            }

            if (! Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('term');
            }
        });

        if (Schema::hasColumn('invoices', 'status')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver !== 'sqlite') {
                DB::statement("ALTER TABLE `invoices` MODIFY `status` VARCHAR(25) NOT NULL DEFAULT 'unpaid'");
            }
        }

        if (Schema::hasColumn('invoices', 'balance')) {
            DB::table('invoices')
                ->whereNull('balance')
                ->update(['balance' => DB::raw('total_amount - COALESCE(paid_amount, 0)')]);
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'term')) {
                $table->dropColumn('term');
            }

            if (Schema::hasColumn('invoices', 'academic_year')) {
                $table->dropColumn('academic_year');
            }

            if (Schema::hasColumn('invoices', 'balance')) {
                $table->dropColumn('balance');
            }

            if (Schema::hasColumn('invoices', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }

            if (Schema::hasColumn('invoices', 'fee_structure_id')) {
                $table->dropForeign(['fee_structure_id']);
                $table->dropColumn('fee_structure_id');
            }

            if (Schema::hasColumn('invoices', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
