<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Routing\ViewController;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
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
    ) {}

    public static function make(bool $verbose = false): self
    {
        $self = new self;

        $self->list = collect([]);
        $self->verbose = $verbose;
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
                'method' => 'GET',
                'uri' => 'uri',
                'name' => 'name',
                'action' => 'action',
                'middleware' => 'middleware',
            ],
        ]);

        $routes = $routes->map(
            fn ($route) => array_merge($route, [
                'action' => $this->formatActionForCli($route),
                'method' => $route['method'] == 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS' ? 'ANY' : $route['method'],
                // 'uri' => $route['domain'] ? ($route['domain'].'/'.ltrim($route['uri'], '/')) : $route['uri'],
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
                str_replace('   ', ' › ', $action),
            ), $this->verbose && ! empty($middleware) ? "<fg=#6C7280>$middleware</>" : null];
        })
            ->flatten()
            ->filter()
            ->prepend('')
            ->push('')->push($this->countOutput)->push('')
            ->toArray();
    }

    /**
     * Get the formatted action for display on the CLI.
     *
     * @param  array  $route
     * @return string
     */
    private function formatActionForCli($route)
    {
        ['action' => $action, 'name' => $name] = $route;

        if ($action === 'Closure' || $action === ViewController::class) {
            return $name;
        }

        $name = $name ? "$name   " : null;

        $rootControllerNamespace = 'App\\Http\\Controllers';

        if (str_starts_with($action, $rootControllerNamespace)) {
            return $name.substr($action, mb_strlen($rootControllerNamespace) + 1);
        }

        $actionClass = explode('@', $action)[0];

        if (class_exists($actionClass) && str_starts_with((new ReflectionClass($actionClass))->getFilename(), base_path('vendor'))) {
            $actionCollection = collect(explode('\\', $action));

            return $name.$actionCollection->take(2)->implode('\\').'   '.$actionCollection->last();
        }

        return $name.$action;
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
