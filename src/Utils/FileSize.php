<?php

namespace Kiwilan\Steward\Utils;

class FileSize
{
    /**
     * Convert a file size to a human readable format.
     *
     * @param  string|int|null  $bytes  The file size in bytes.
     */
    public static function humanReadable(mixed $bytes, ?int $precision = 2): string
    {
        if ($bytes === null) {
            return '0 B';
        }

        $size = [
            'B',
            'Ko',
            'Mo',
            'Go',
            'To',
            'Po',
        ];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)).' '.@$size[$factor];
    }
}
