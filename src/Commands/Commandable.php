<?php

namespace Kiwilan\Steward\Commands;

use Closure;
use Illuminate\Console\Command;

class Commandable extends Command
{
    public function title(?string $signature = null, ?string $description = null)
    {
        $app = config('app.name');
        $this->newLine();

        $signature_name = explode("\n", $this->signature); // remove options
        $signature_name = explode(' ', $signature_name[0]); // remove arguments

        if (array_key_exists(0, $signature_name)) {
            $signature_name = $signature_name[0];

            if (str_contains($signature_name, ':')) {
                $signature_name = explode(':', $signature_name);
                $signature_name = array_map('ucfirst', $signature_name);
                $signature_name = implode(' ', $signature_name);
            } else {
                $signature_name = ucfirst($signature_name);
            }
        } else {
            $signature_name = $this->signature;
        }

        if ($signature) {
            $signature_name = $signature;
        }
        $this->alert("{$signature_name}");
        $this->warn($this->description);
        $this->newLine();
    }

    public function askOnProduction(?Closure $closure = null, bool $with_force = true, string $option = 'force')
    {
        $force = false;

        if ($with_force) {
            $force = $this->option($option) ?? false;
        }

        if (config('app.env') !== 'local' && ! $force) {
            if ($this->confirm("You're on production, do you really want to continue?", true)) {
                $this->info('Confirmed.');
            } else {
                $this->error('Stop.');

                return Command::FAILURE;
            }
        }
    }

    public function optionArgument(string $option, ?string $default = null): ?string
    {
        $value = $this->option($option);
        if (str_contains($value, '=')) {
            $value = str_replace('=', '', $value);
        }

        if (is_null($value)) {
            return $default;
        }

        return $value;
    }

    public function optionInt(string $option, ?int $default = null): ?int
    {
        return (int) $this->optionArgument($option, $default);
    }

    public function optionBool(string $option, ?bool $default = false): ?bool
    {
        return (bool) $this->optionArgument($option, $default);
    }
}
