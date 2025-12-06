<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class ExportDatabases extends Command
{
    protected $signature = 'system:export-databases';
    protected $description = 'Export all databases (central and tenants) to SQL files using mysqldump';

    public function handle()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupPath = storage_path("backups/export_{$timestamp}");

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $this->info("Starting export to: {$backupPath}");

        // 1. Export Central Database
        $centralDb = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $this->exportDatabase($host, $port, $username, $password, $centralDb, "{$backupPath}/central_{$centralDb}.sql");

        // 2. Export Tenant Databases
        $schools = School::all();
        foreach ($schools as $school) {
            $tenantDb = $school->database;
            if ($tenantDb) {
                $this->exportDatabase($host, $port, $username, $password, $tenantDb, "{$backupPath}/tenant_{$school->subdomain}_{$tenantDb}.sql");
            }
        }

        $this->info("Export completed successfully!");
        $this->info("Files are located in: {$backupPath}");
    }

    private function exportDatabase($host, $port, $username, $password, $database, $outputPath)
    {
        $this->info("Exporting {$database}...");

        $passwordArg = $password ? "--password=\"{$password}\"" : "";

        // Try to find mysqldump
        $mysqldump = 'mysqldump'; // Default

        // Check common WAMP paths if on Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
             // Attempt to find in WAMP
             $wampPath = 'c:\\wamp5\\bin\\mysql';
             if (is_dir($wampPath)) {
                 $versions = scandir($wampPath);
                 foreach ($versions as $version) {
                     if (str_starts_with($version, 'mysql') && is_file("{$wampPath}\\{$version}\\bin\\mysqldump.exe")) {
                         $mysqldump = "\"{$wampPath}\\{$version}\\bin\\mysqldump.exe\"";
                         break;
                     }
                 }
             }
        }

        // Construct mysqldump command
        // --routines: export stored procedures/functions
        // --triggers: export triggers
        // --add-drop-table: add DROP TABLE before CREATE
        // --disable-keys: faster import
        // --hex-blob: dump binary strings in hex format
        // --no-tablespaces: avoid tablespace issues
        // --column-statistics=0: avoid issues with newer mysqldump on older servers

        $command = "{$mysqldump} --user=\"{$username}\" {$passwordArg} --host=\"{$host}\" --port=\"{$port}\" --routines --triggers --single-transaction --quick --add-drop-table --disable-keys --hex-blob --no-tablespaces --column-statistics=0 \"{$database}\" > \"{$outputPath}\" 2>NUL";

        // Execute
        system($command, $returnVar);

        if ($returnVar === 0) {
            $this->info("✓ Exported {$database}");
        } else {
            // Try without column-statistics=0 if it failed (older mysqldump versions don't support it)
            $commandRetry = "{$mysqldump} --user=\"{$username}\" {$passwordArg} --host=\"{$host}\" --port=\"{$port}\" --routines --triggers --single-transaction --quick --add-drop-table --disable-keys --hex-blob --no-tablespaces \"{$database}\" > \"{$outputPath}\" 2>NUL";
            system($commandRetry, $returnVarRetry);

            if ($returnVarRetry === 0) {
                $this->info("✓ Exported {$database} (retry without column-statistics)");
            } else {
                $this->error("✗ Failed to export {$database}. Return code: {$returnVar}");
                $this->line("Command: {$command}");
            }
        }
    }
}
