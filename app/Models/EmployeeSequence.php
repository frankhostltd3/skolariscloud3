<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSequence extends Model
{
    protected $fillable = [
        'prefix',
        'last_number',
        'format',
        'description',
    ];

    protected $casts = [
        'last_number' => 'integer',
    ];

    /**
     * Get the next employee number for a given prefix
     *
     * @param string $prefix
     * @return string
     */
    public static function getNextNumber(string $prefix): string
    {
        // Use database transaction with locking for thread-safety
        return \DB::transaction(function () use ($prefix) {
            // Lock the row for update to prevent race conditions
            $sequence = static::lockForUpdate()
                ->where('prefix', $prefix)
                ->first();

            if (!$sequence) {
                // Create new sequence if doesn't exist
                $sequence = static::create([
                    'prefix' => $prefix,
                    'last_number' => 0,
                    'format' => '{PREFIX}-{NUMBER:4}',
                    'description' => "Sequence for {$prefix} employees",
                ]);
            }

            // Increment the sequence
            $sequence->increment('last_number');
            $sequence->refresh();

            // Format the employee number
            return static::formatNumber($sequence);
        });
    }

    /**
     * Format the employee number based on the format template
     *
     * @param EmployeeSequence $sequence
     * @return string
     */
    protected static function formatNumber(EmployeeSequence $sequence): string
    {
        $format = $sequence->format;
        $number = $sequence->last_number;
        $prefix = $sequence->prefix;

        // Parse format string
        // {PREFIX} - The prefix
        // {NUMBER:4} - The number with padding (4 digits)
        // {YEAR} - Current year (4 digits)
        // {YEAR:2} - Current year (2 digits)
        // {MONTH} - Current month (2 digits)

        $year = date('Y');
        $yearShort = date('y');
        $month = date('m');

        $formatted = $format;
        $formatted = str_replace('{PREFIX}', $prefix, $formatted);
        $formatted = str_replace('{YEAR}', $year, $formatted);
        $formatted = str_replace('{YEAR:2}', $yearShort, $formatted);
        $formatted = str_replace('{MONTH}', $month, $formatted);

        // Handle {NUMBER:X} pattern
        if (preg_match('/\{NUMBER:(\d+)\}/', $formatted, $matches)) {
            $padding = (int) $matches[1];
            $paddedNumber = str_pad($number, $padding, '0', STR_PAD_LEFT);
            $formatted = preg_replace('/\{NUMBER:\d+\}/', $paddedNumber, $formatted);
        } else {
            // Default: just replace {NUMBER}
            $formatted = str_replace('{NUMBER}', $number, $formatted);
        }

        return $formatted;
    }

    /**
     * Reset sequence to a specific number
     *
     * @param string $prefix
     * @param int $number
     * @return bool
     */
    public static function resetSequence(string $prefix, int $number = 0): bool
    {
        return static::where('prefix', $prefix)
            ->update(['last_number' => $number]);
    }

    /**
     * Get current number without incrementing
     *
     * @param string $prefix
     * @return int
     */
    public static function getCurrentNumber(string $prefix): int
    {
        $sequence = static::where('prefix', $prefix)->first();
        return $sequence ? $sequence->last_number : 0;
    }

    /**
     * Preview next number without incrementing
     *
     * @param string $prefix
     * @return string
     */
    public static function previewNextNumber(string $prefix): string
    {
        $sequence = static::where('prefix', $prefix)->first();
        
        if (!$sequence) {
            $sequence = new static([
                'prefix' => $prefix,
                'last_number' => 0,
                'format' => '{PREFIX}-{NUMBER:4}',
            ]);
        }

        // Create temporary sequence with incremented number
        $tempSequence = clone $sequence;
        $tempSequence->last_number = $sequence->last_number + 1;

        return static::formatNumber($tempSequence);
    }
}
