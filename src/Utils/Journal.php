<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Journal
{
    protected function __construct(
        protected ?string $message = null,
        protected string $level = 'info',
        protected array $data = [],
        protected ?Collection $users = null,
    ) {
        $this->log();
    }

    public static function info(string $message, array $data = []): self
    {

        return new self($message, 'info', $data);
    }

    public static function debug(string $message, array $data = []): self
    {
        return new self($message, 'debug', $data);
    }

    public static function warning(string $message, array $data = []): self
    {
        return new self($message, 'warning', $data);
    }

    public static function error(string $message, array $data = []): self
    {
        return new self($message, 'error', $data);
    }

    private function log(): void
    {
        Log::log($this->level, $this->message, $this->data);
    }

    /**
     * Send notification to database for Users with access to Filament admin panel with `filament/notifications` package.
     *
     * @param  Model|Authenticatable|Collection|array|null  $users  To send notification to.
     */
    public function toDatabase(Model|Authenticatable|Collection|array|null $users = null): void
    {
        if (! class_exists('\Filament\Notifications\Notification')) {
            throw new \Exception('Filament notifications is not installed, check https://filamentphp.com/docs/3.x/notifications/installation');
        }

        if (! class_exists('\App\Models\User')) {
            throw new \Exception('User model not found');
        }

        $filamentUsers = $this->users;

        if (! $filamentUsers) {
            $users = '\App\Models\User';
            $filamentUsers = $users::all()->map(fn ($user) => $user->canAccessPanel());
        }

        \Filament\Notifications\Notification::make()
            ->title($this->level)
            ->body($this->message)
            ->sendToDatabase($filamentUsers);
    }
}
