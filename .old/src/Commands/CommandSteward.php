<?php

namespace Kiwilan\Steward\Commands;

use Closure;
use Illuminate\Console\Command;

class CommandSteward extends Command
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

        if ('local' !== config('app.env') && ! $force) {
            if ($this->confirm("You're on production, do you really want to continue?", true)) {
                $this->info('Confirmed.');
            } else {
                $this->error('Stop.');

                return Command::FAILURE;
            }
        }
    }
}
