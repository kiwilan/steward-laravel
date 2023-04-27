<?php

namespace Kiwilan\Steward\Services\Class;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use SplFileInfo;

class ClassItem
{
    /** @var string[] */
    protected array $traits = [];

    /** @var string[] */
    protected array $implements = [];

    protected function __construct(
        protected string $path,
        protected ?SplFileInfo $file = null,
        protected ?string $name = null,
        protected ?string $namespace = null,
        protected ?string $extends = null,
        protected ?object $instance = null,
        protected bool $isModel = false,
        protected ?Model $model = null,
        protected ?ReflectionClass $reflect = null,
    ) {
    }

    /**
     * @param  string  $path Can be path to file or namespace
     */
    public static function make(string $path): self
    {
        $self = new self($path);

        if ($self->isPath($path)) {
            $self->file = new SplFileInfo($path);
            $self->namespace = $self->setNamespace();
            $self->instance = new $self->namespace();
        } else {
            $self->namespace = $path;
            $self->instance = new $path();
        }

        $self->extends = get_parent_class($self->instance);
        $self->traits = class_uses($self->instance);
        $self->implements = class_implements($self->instance);
        $self->reflect = new ReflectionClass($self->namespace);
        $self->name = $self->reflect->getShortName();

        if ($self->instance() instanceof Model) {
            $self->isModel = true;
            $self->model = $self->instance();
        }

        return $self;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function file(): ?SplFileInfo
    {
        return $this->file;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function extends(): string
    {
        return $this->extends;
    }

    public function instance(): object
    {
        return $this->instance;
    }

    public function traits(): array
    {
        return $this->traits;
    }

    public function implements(): array
    {
        return $this->implements;
    }

    public function reflect(): ReflectionClass
    {
        return $this->reflect;
    }

    public function isModel(): bool
    {
        return $this->isModel;
    }

    public function model(): ?Model
    {
        return $this->model;
    }

    public function useTrait(string $current): bool
    {
        $inArray = in_array($current, $this->traits);

        if (! $inArray) {
            $traits = [];

            foreach ($this->traits as $trait) {
                $traits[] = explode('\\', $trait)[count(explode('\\', $trait)) - 1];
            }

            $inArray = in_array($current, $traits);
        }

        return $inArray;
    }

    public function methodExists(string $method): bool
    {
        return method_exists($this->instance, $method);
    }

    public function propertyExists(string $property): bool
    {
        return property_exists($this->instance, $property);
    }

    private function isPath(string $path): bool
    {
        if (file_exists($path)) {
            return true;
        }

        if (! class_exists($path)) {
            throw new \Exception("Class {$path} does not exist.");
        }

        return false;
    }

    private function setNamespace(): string
    {
        $path = $this->file->getPathName();
        $name = $this->file->getBasename('.php');

        $ns = null;
        $handle = fopen($path, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');

                    break;
                }
            }
            fclose($handle);
        }

        return "{$ns}\\{$name}";
    }
}
