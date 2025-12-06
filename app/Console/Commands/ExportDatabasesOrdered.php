<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportDatabasesOrdered extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:export-databases-ordered
                            {--path= : Export directory path (default: storage/backups/export_TIMESTAMP)}
                            {--central-only : Export only the central database}
                            {--tenants-only : Export only tenant databases}';

    /**
     * The console command description.
     */
    protected $description = 'Export all databases with tables ordered by foreign key dependencies (for clean import)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $exportPath = $this->option('path') ?: storage_path("backups/export_{$timestamp}");

        // Create export directory
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0755, true);
        }

        $this->info("🚀 Starting ordered database export to: {$exportPath}");
        $this->newLine();

        // Get database credentials
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $exportedCount = 0;

        // Export central database
        if (!$this->option('tenants-only')) {
            $centralDb = config('database.connections.mysql.database');
            $this->exportDatabase($centralDb, $exportPath, $host, $port, $username, $password, 'central');
            $exportedCount++;
        }

        // Export tenant databases
        if (!$this->option('central-only')) {
            $schools = School::all();

            if ($schools->isEmpty()) {
                $this->warn('No tenant schools found in the database.');
            } else {
                $this->info("Found {$schools->count()} tenant database(s) to export.");
                $this->newLine();

                foreach ($schools as $school) {
                    $tenantDb = $school->database ?? "tenant_{$school->id}";
                    $this->exportDatabase($tenantDb, $exportPath, $host, $port, $username, $password, "tenant_{$school->subdomain}");
                    $exportedCount++;
                }
            }
        }

        $this->newLine();
        $this->info("✅ Export complete! {$exportedCount} database(s) exported to:");
        $this->line("   {$exportPath}");
        $this->newLine();

        // Create import instructions
        $this->createImportInstructions($exportPath);

        return Command::SUCCESS;
    }

    /**
     * Export a single database with tables ordered by dependencies.
     */
    protected function exportDatabase(
        string $database,
        string $exportPath,
        string $host,
        string $port,
        string $username,
        string $password,
        string $prefix
    ): void {
        $this->info("📦 Exporting: {$database}");

        // Check if database exists
        try {
            $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$database]);
            if (empty($exists)) {
                $this->warn("   ⚠️  Database '{$database}' does not exist. Skipping.");
                return;
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Error checking database: " . $e->getMessage());
            return;
        }

        // Get tables in dependency order
        $orderedTables = $this->getTablesInDependencyOrder($database);

        if (empty($orderedTables)) {
            $this->warn("   ⚠️  No tables found in '{$database}'. Skipping.");
            return;
        }

        $this->line("   Found " . count($orderedTables) . " tables");

        $filename = "{$prefix}_{$database}.sql";
        $filepath = "{$exportPath}/{$filename}";

        // Build the SQL file manually with proper ordering
        $this->buildOrderedSqlFile($database, $orderedTables, $filepath, $host, $port, $username, $password);

        if (file_exists($filepath) && filesize($filepath) > 0) {
            $size = $this->formatBytes(filesize($filepath));
            $this->info("   ✓ Exported to {$filename} ({$size})");
        } else {
            $this->error("   ❌ Export failed for {$database}");
        }
    }

    /**
     * Get tables ordered by foreign key dependencies (parents first).
     */
    protected function getTablesInDependencyOrder(string $database): array
    {
        // Get all tables
        $tables = DB::select("
            SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
        ", [$database]);

        $tableNames = array_map(fn($t) => $t->TABLE_NAME, $tables);

        // Get foreign key dependencies
        $dependencies = [];
        foreach ($tableNames as $table) {
            $dependencies[$table] = [];
        }

        $fks = DB::select("
            SELECT
                TABLE_NAME as child_table,
                REFERENCED_TABLE_NAME as parent_table
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
              AND REFERENCED_TABLE_SCHEMA = ?
        ", [$database, $database]);

        foreach ($fks as $fk) {
            if (isset($dependencies[$fk->child_table]) && in_array($fk->parent_table, $tableNames)) {
                $dependencies[$fk->child_table][] = $fk->parent_table;
            }
        }

        // Topological sort (Kahn's algorithm)
        $sorted = [];
        $noDeps = [];

        // Find tables with no dependencies
        foreach ($dependencies as $table => $deps) {
            if (empty($deps)) {
                $noDeps[] = $table;
            }
        }

        while (!empty($noDeps)) {
            $table = array_shift($noDeps);
            $sorted[] = $table;

            // Remove this table from all dependency lists
            foreach ($dependencies as $t => $deps) {
                $key = array_search($table, $deps);
                if ($key !== false) {
                    unset($dependencies[$t][$key]);
                    $dependencies[$t] = array_values($dependencies[$t]);

                    if (empty($dependencies[$t]) && !in_array($t, $sorted) && !in_array($t, $noDeps)) {
                        $noDeps[] = $t;
                    }
                }
            }
        }

        // Add any remaining tables (circular dependencies) at the end
        foreach ($tableNames as $table) {
            if (!in_array($table, $sorted)) {
                $sorted[] = $table;
            }
        }

        return $sorted;
    }

    /**
     * Build SQL file with tables in the correct order.
     */
    protected function buildOrderedSqlFile(
        string $database,
        array $orderedTables,
        string $filepath,
        string $host,
        string $port,
        string $username,
        string $password
    ): void {
        $handle = fopen($filepath, 'w');

        // Write header
        fwrite($handle, "-- ============================================================\n");
        fwrite($handle, "-- Database Export: {$database}\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- Tables are ordered by foreign key dependencies\n");
        fwrite($handle, "-- ============================================================\n\n");

        // Disable foreign key checks at the start
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
        fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
        fwrite($handle, "SET AUTOCOMMIT = 0;\n");
        fwrite($handle, "START TRANSACTION;\n\n");

        // Create database if not exists
        fwrite($handle, "-- Create database if not exists\n");
        fwrite($handle, "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n");
        fwrite($handle, "USE `{$database}`;\n\n");

        // Write table order comment
        fwrite($handle, "-- ============================================================\n");
        fwrite($handle, "-- TABLE ORDER (for manual import if needed):\n");
        foreach ($orderedTables as $index => $table) {
            $num = $index + 1;
            fwrite($handle, "-- {$num}. {$table}\n");
        }
        fwrite($handle, "-- ============================================================\n\n");

        fclose($handle);

        // Use mysqldump for each table in order and append to file
        $escapedPassword = escapeshellarg($password);

        foreach ($orderedTables as $table) {
            $cmd = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s ' .
                '--single-transaction --routines --triggers --no-tablespaces ' .
                '--skip-add-drop-table --skip-comments ' .
                '%s %s >> %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $escapedPassword,
                escapeshellarg($database),
                escapeshellarg($table),
                escapeshellarg($filepath)
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->warn("      Warning: Issue exporting table '{$table}'");
            }
        }

        // Append footer
        $handle = fopen($filepath, 'a');
        fwrite($handle, "\n-- ============================================================\n");
        fwrite($handle, "-- END OF EXPORT\n");
        fwrite($handle, "-- ============================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fwrite($handle, "COMMIT;\n");
        fclose($handle);
    }

    /**
     * Create import instructions file.
     */
    protected function createImportInstructions(string $exportPath): void
    {
        $instructions = <<<'INSTRUCTIONS'
================================================================================
DATABASE IMPORT INSTRUCTIONS
================================================================================

These SQL files have tables ordered by foreign key dependencies, so they can
be imported cleanly without constraint errors.

IMPORT ORDER:
-------------
1. FIRST: Import the CENTRAL database (central_*.sql)
2. THEN: Import TENANT databases (tenant_*.sql) in any order

METHOD 1: MySQL Command Line
----------------------------
mysql -u YOUR_USERNAME -p < central_fran_ugketravel36.sql
mysql -u YOUR_USERNAME -p < tenant_demo_tenant_000001.sql
mysql -u YOUR_USERNAME -p < tenant_victorianileschool_tenant_000002.sql
... (repeat for each tenant)

METHOD 2: phpMyAdmin
--------------------
1. Create the database first (if it doesn't exist)
2. Select the database
3. Go to "Import" tab
4. Choose the SQL file
5. Click "Go"

METHOD 3: Plesk Database Import
-------------------------------
1. Go to Databases in Plesk
2. Click on the database
3. Click "Import Dump"
4. Upload the SQL file

IMPORTANT NOTES:
----------------
- Foreign key checks are DISABLED at the start of each file
- Foreign key checks are RE-ENABLED at the end of each file
- Tables are created in dependency order (parent tables first)
- If import fails, check that the database user has full privileges

================================================================================
INSTRUCTIONS;

        file_put_contents("{$exportPath}/IMPORT_INSTRUCTIONS.txt", $instructions);
        $this->info("📄 Created IMPORT_INSTRUCTIONS.txt");
    }

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
