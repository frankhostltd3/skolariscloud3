<?php

namespace App\Services;

use Illuminate\Support\Str;

class EnvWriter
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?: base_path('.env');
    }

    public function isWritable(): bool
    {
        return is_file($this->path) && is_writable($this->path);
    }

    public function put(array $values): void
    {
        if (empty($values) || ! $this->isWritable()) {
            return;
        }

        $contents = file_get_contents($this->path) ?: '';
        $lineEnding = str_contains($contents, "\r\n") ? "\r\n" : "\n";
        $lines = $contents === '' ? [] : preg_split("/\r\n|\n|\r/", $contents);
        $indexByKey = [];

        foreach ($lines as $index => $line) {
            if (! $line || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key] = explode('=', $line, 2);
            $indexByKey[trim($key)] = $index;
        }

        foreach ($values as $key => $value) {
            $exported = $this->exportValue($value);

            if (array_key_exists($key, $indexByKey)) {
                $lines[$indexByKey[$key]] = $key.'='.$exported;
            } else {
                $lines[] = $key.'='.$exported;
            }
        }

        $output = implode($lineEnding, $lines);

        if ($output !== '' && ! str_ends_with($output, $lineEnding)) {
            $output .= $lineEnding;
        }

        file_put_contents($this->path, $output, LOCK_EX);
    }

    private function exportValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $stringValue = (string) $value;

        if ($stringValue === '') {
            return '';
        }

        $needsQuotes = Str::contains($stringValue, [' ', '#', '=']) || str_contains($stringValue, '"');

        if ($needsQuotes) {
            return '"'.str_replace('"', '\"', $stringValue).'"';
        }

        return $stringValue;
    }
}
