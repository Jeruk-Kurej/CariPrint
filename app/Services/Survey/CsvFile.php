<?php

namespace App\Services\Survey;

use InvalidArgumentException;
use RuntimeException;

/**
 * Membaca dan menulis berkas CSV survei (format di docs/SURVEY.md).
 */
class CsvFile
{
    /**
     * @return list<array<string, string>>
     */
    public static function read(string $path): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("Berkas tidak ditemukan: $path");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Berkas tidak bisa dibuka: $path");
        }

        $header = fgetcsv($handle) ?: [];
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null]) {
                continue;
            }
            $rows[] = array_combine($header, array_pad($line, count($header), ''));
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @param  list<string>  $columns
     * @param  list<array<string, string>>  $rows
     */
    public static function write(string $path, array $columns, array $rows): void
    {
        $handle = fopen($path, 'w');

        if ($handle === false) {
            throw new RuntimeException("Berkas tidak bisa ditulis: $path");
        }

        fputcsv($handle, $columns);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(fn (string $column) => $row[$column] ?? '', $columns));
        }

        fclose($handle);
    }
}
