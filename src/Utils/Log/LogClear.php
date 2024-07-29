<?php

namespace Kiwilan\Steward\Utils\Log;

class LogClear
{
    public static function make(): self
    {
        $self = new self;

        return $self;
    }

    /**
     * Clear specific log file.
     */
    public function clearLog(string $log_name): void
    {
        $this->clearFile(storage_path("logs/{$log_name}.log"));
    }

    /**
     * Clear all log files.
     */
    public function clearAll(): void
    {
        $logFiles = glob(storage_path('logs/*.log'));

        foreach ($logFiles as $logFile) {
            $this->clearFile($logFile);
        }

    }

    /**
     * Clear specific level log.
     */
    public function deleteLevels(string $level): void
    {
        $allLogFiles = glob(storage_path('logs/*.log'));

        foreach ($allLogFiles as $logFile) {
            $this->deleteLevel($logFile, $level);
        }
    }

    private function clearFile(string $path): void
    {
        file_put_contents($path, '');
    }

    private function deleteLevel(string $path, string $level): void
    {
        $tempFile = storage_path('logs/temp.log');
        $level = strtoupper($level);

        $handle = fopen($path, 'r');
        $tempHandle = fopen($tempFile, 'w');

        if ($handle && $tempHandle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, "local.$level") === false) {
                    fwrite($tempHandle, $line);
                }
            }

            fclose($handle);
            fclose($tempHandle);

            // Replace original file with temp file
            rename($tempFile, $path);
        } else {
            // Error handling
            echo 'Error opening file!';
        }
    }
}
