<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            if (! Schema::hasColumn('schools', 'subdomain')) {
                $table->string('subdomain')->nullable()->after('code');
            }
        });

        $schools = DB::table('schools')->whereNull('subdomain')->get();

        foreach ($schools as $school) {
            $subdomain = Str::slug((string) $school->code);

            if ($subdomain === '') {
                $subdomain = Str::slug((string) $school->name);
            }

            if ($subdomain === '') {
                $subdomain = 'school-' . $school->id;
            }

            DB::table('schools')
                ->where('id', $school->id)
                ->update(['subdomain' => $subdomain]);
        }

        if (Schema::hasColumn('schools', 'subdomain')) {
            Schema::table('schools', function (Blueprint $table): void {
                $table->unique('subdomain');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('schools', 'subdomain')) {
            Schema::table('schools', function (Blueprint $table): void {
                $table->dropUnique('schools_subdomain_unique');
                $table->dropColumn('subdomain');
            });
        }
    }
};
