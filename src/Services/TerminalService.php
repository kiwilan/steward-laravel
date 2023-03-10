<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Terminal;

class TerminalService
{
    /** @var Collection<int,mixed> */
    protected ?Collection $list = null;

    /** @var array<string,string> */
    protected array $verbColors = [
        'ANY' => 'red',
        'GET' => 'blue',
        'HEAD' => '#6C7280',
        'OPTIONS' => '#6C7280',
        'POST' => 'yellow',
        'PUT' => 'yellow',
        'PATCH' => 'yellow',
        'DELETE' => 'red',
    ];

    protected function __construct(
        protected int $terminalWidth = 0,
        protected string $countOutput = '0',
        protected bool $verbose = false,
        protected array $output = [],
    ) {
    }

    public static function make(): self
    {
        $self = new self();

        $self->terminalWidth = $self->setTerminalWidth();
        $self->countOutput = $self->setCountOutput();
        $self->output = $self->setStyle();

        return $self;
    }

    /**
     * @return array<string,string>
     */
    public function output(): array
    {
        return $this->output;
    }

    /**
     * @return array<string,string>
     */
    private function setStyle(): array
    {
        $routes = collect([
            [
                'domain' => 'domain',
                'method' => 'method',
                'uri' => 'uri',
                'name' => 'name',
                'action' => 'action',
                'middleware' => 'middleware',
            ],
        ]);

        $routes = $routes->map(
            fn ($route) => array_merge($route, [
                // 'action' => $this->formatActionForCli($route),
                // 'method' => $route['method'] == 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS' ? 'ANY' : $route['method'],
                // 'uri' => $route['domain'] ? ($route['domain'].'/'.ltrim($route['uri'], '/')) : $route['uri'],
                'action' => 'action',
                'method' => 'method',
                'uri' => 'uri',
            ]),
        );

        $maxMethod = mb_strlen($routes->max('method'));

        return $routes->map(function ($route) use ($maxMethod) {
            [
                'action' => $action,
                'domain' => $domain,
                'method' => $method,
                'middleware' => $middleware,
                'uri' => $uri,
            ] = $route;

            $middleware = Str::of($middleware)->explode("\n")->filter()->whenNotEmpty(
                fn ($collection) => $collection->map(
                    fn ($middleware) => sprintf('         %s⇂ %s', str_repeat(' ', $maxMethod), $middleware)
                )
            )->implode("\n");

            $spaces = str_repeat(' ', max($maxMethod + 6 - mb_strlen($method), 0));

            $dots = str_repeat('.', max(
                $this->terminalWidth - mb_strlen($method.$spaces.$uri.$action) - 6 - ($action ? 1 : 0), 0
            ));

            $dots = empty($dots) ? $dots : " $dots";

            if ($action && ! $this->verbose && mb_strlen($method.$spaces.$uri.$action.$dots) > ($this->terminalWidth - 6)) {
                $action = substr($action, 0, $this->terminalWidth - 7 - mb_strlen($method.$spaces.$uri.$dots)).'…';
            }

            $method = Str::of($method)->explode('|')->map(
                fn ($method) => sprintf('<fg=%s>%s</>', $this->verbColors[$method] ?? 'default', $method),
            )->implode('<fg=#6C7280>|</>');

            return [sprintf(
                '  <fg=white;options=bold>%s</> %s<fg=white>%s</><fg=#6C7280>%s %s</>',
                $method,
                $spaces,
                preg_replace('#({[^}]+})#', '<fg=yellow>$1</>', $uri),
                $dots,
                str_replace('   ', ' › ', $action ?? ''),
            ), $this->verbose && ! empty($middleware) ? "<fg=#6C7280>$middleware</>" : null];
        })
            ->flatten()
            ->filter()
            ->prepend('')
            ->push('')->push($this->countOutput)->push('')
            ->toArray()
        ;
    }

    /**
     * Get the terminal width.
     */
    public static function setTerminalWidth(): int
    {
        return (new Terminal)->getWidth();
    }

    /**
     * Determine and return the output for displaying the number of routes in the CLI output.
     */
    protected function setCountOutput(): string
    {
        $countText = 'Showing ['.$this->list->count().'] routes';

        $offset = $this->terminalWidth - mb_strlen($countText) - 2;

        $spaces = str_repeat(' ', $offset);

        return $spaces.'<fg=blue;options=bold>Showing ['.$this->list->count().'] routes</>';
    }
}
