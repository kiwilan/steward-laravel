<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

class Console
{
    use InteractsWithIO;

    public function __construct(
        public ?ConsoleOutput $console_output = null,
    ) {
    }

    public static function make(): self
    {
        $output = new ConsoleOutput();
        $service = new Console();
        $service->console_output = $output;

        return $service;
    }

    public function print(string $message, string $color = 'green', Throwable $th = null): void
    {
        $style = new OutputFormatterStyle($color, '', []);
        $this->console_output->getFormatter()
            ->setStyle('info', $style);

        if ($th) {
            $this->console_output->writeln("<info>Error about {$message}</info>\n");
            $this->console_output->writeln($th->getMessage());
        } else {
            $this->console_output->writeln("<info>{$message}</info>");
        }
    }

    public function newLine()
    {
        $style = new OutputFormatterStyle('red', '', ['bold']);
        $this->console_output->getFormatter()
            ->setStyle('info', $style);
        $this->console_output->writeln('');
    }
}
