<?php

namespace App\Services;

class SqlImportParser
{
    /**
     * Parse SQL INSERT statements for leave_types table
     *
     * @param string $sql
     * @return array
     */
    public function parseLeaveTypesInsert(string $sql): array
    {
        $data = [];

        // Simple approach: split by semicolon and process each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (empty($statement)) continue;

            // Match INSERT INTO leave_types statements
            if (preg_match('/INSERT\s+INTO\s+leave_types\s*\((.*?)\)\s*VALUES\s*\((.*?)\)/i', $statement, $matches)) {
                $columns = $this->parseColumns($matches[1]);
                $values = $this->parseValues($matches[2]);

                if (count($columns) === count($values)) {
                    $rowData = array_combine($columns, $values);
                    if ($this->validateLeaveTypeData($rowData)) {
                        $data[] = $rowData;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Split SQL into individual statements
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = '';
        $parenDepth = 0;

        $chars = str_split($sql);
        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];
            $currentStatement .= $char;

            if (!$inString) {
                if ($char === '(') {
                    $parenDepth++;
                } elseif ($char === ')') {
                    $parenDepth--;
                } elseif (($char === '"' || $char === "'") && ($i === 0 || $chars[$i-1] !== '\\')) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === ';' && $parenDepth === 0) {
                    $statements[] = trim($currentStatement);
                    $currentStatement = '';
                }
            } elseif ($char === $stringChar && ($i === 0 || $chars[$i-1] !== '\\')) {
                $inString = false;
                $stringChar = '';
            }
        }

        // Add remaining statement if any
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        return $statements;
    }

    /**
     * Parse column names from INSERT statement
     */
    private function parseColumns(string $columnsStr): array
    {
        $columns = [];
        $columnsStr = trim($columnsStr);

        // Split by comma
        $parts = array_map('trim', explode(',', $columnsStr));

        foreach ($parts as $part) {
            // Remove quotes if present
            $column = trim($part, '`\'"');
            if (!empty($column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    /**
     * Parse values from INSERT statement
     */
    private function parseValues(string $valuesStr): array
    {
        $values = [];
        $valuesStr = trim($valuesStr);

        // Use a more robust approach to split values
        $parts = [];
        $current = '';
        $inString = false;
        $quoteChar = '';

        $chars = str_split($valuesStr);
        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];

            if (!$inString) {
                if ($char === '"' || $char === "'") {
                    $inString = true;
                    $quoteChar = $char;
                    $current .= $char;
                } elseif ($char === ',') {
                    $parts[] = trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            } else {
                $current .= $char;
                if ($char === $quoteChar && ($i === 0 || $chars[$i-1] !== '\\')) {
                    $inString = false;
                    $quoteChar = '';
                }
            }
        }

        if (!empty($current)) {
            $parts[] = trim($current);
        }

        foreach ($parts as $part) {
            $value = trim($part);

            // Handle NULL values
            if (strtoupper($value) === 'NULL') {
                $values[] = null;
                continue;
            }

            // Handle boolean values
            if (strtoupper($value) === 'TRUE' || $value === '1') {
                $values[] = true;
                continue;
            }
            if (strtoupper($value) === 'FALSE' || $value === '0') {
                $values[] = false;
                continue;
            }

            // Handle quoted strings
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
                // Unescape quotes
                $value = str_replace(['\\"', "\\'"], ['"', "'"], $value);
                $values[] = $value;
                continue;
            }

            // Handle numeric values
            if (is_numeric($value)) {
                $values[] = strpos($value, '.') !== false ? (float) $value : (int) $value;
                continue;
            }

            // Default: treat as string
            $values[] = $value;
        }

        return $values;
    }

    /**
     * Split multiple value sets in INSERT statement
     */
    private function splitValueSets(string $valuesBlock): array
    {
        $sets = [];
        $currentSet = '';
        $parenDepth = 0;
        $inString = false;
        $stringChar = '';

        $chars = str_split($valuesBlock);
        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];
            $currentSet .= $char;

            if (!$inString) {
                if ($char === '(') {
                    $parenDepth++;
                } elseif ($char === ')') {
                    $parenDepth--;
                    if ($parenDepth === 0) {
                        $sets[] = trim($currentSet, '(), ');
                        $currentSet = '';
                    }
                } elseif (($char === '"' || $char === "'") && ($i === 0 || $chars[$i-1] !== '\\')) {
                    $inString = true;
                    $stringChar = $char;
                }
            } elseif ($char === $stringChar && ($i === 0 || $chars[$i-1] !== '\\')) {
                $inString = false;
                $stringChar = '';
            }
        }

        return array_filter($sets);
    }

    /**
     * Validate that the parsed data contains required leave type fields
     */
    private function validateLeaveTypeData(array $data): bool
    {
        $requiredFields = ['name', 'code', 'default_days'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                return false;
            }
        }

        // Validate data types
        if (!is_string($data['name']) || strlen($data['name']) > 255) {
            return false;
        }

        if (!is_string($data['code']) || strlen($data['code']) > 10) {
            return false;
        }

        if (!is_numeric($data['default_days']) || $data['default_days'] < 0 || $data['default_days'] > 365) {
            return false;
        }

        return true;
    }
}