<?php

namespace Kiwilan\Steward\Services\ClassParser;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use SplFileInfo;

class ClassParserItem
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
        protected ?MetaClassItem $meta = null,
    ) {
    }

    /**
     * @param  string  $class Can be path to file or class string
     */
    public static function make(string $class): self
    {
        $self = new self($class);

        if ($self->isPath($class)) {
            $self->file = new SplFileInfo($class);
            $self->namespace = $self->setNamespace();
            $self->instance = new $self->namespace();
        } else {
            $self->namespace = $class;
            $self->instance = new $class();
        }

        $self->extends = get_parent_class($self->instance);
        $self->traits = class_uses($self->instance);
        $self->implements = class_implements($self->instance);
        $self->reflect = new ReflectionClass($self->namespace);
        $self->name = $self->reflect->getShortName();
        $self->meta = MetaClassItem::make($self->namespace, $self->reflect);

        if ($self->instance instanceof Model) {
            $self->isModel = true;
            $self->model = $self->instance;
        }

        return $self;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getExtends(): string
    {
        return $this->extends;
    }

    public function getInstance(): object
    {
        return $this->instance;
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function getImplements(): array
    {
        return $this->implements;
    }

    public function getReflect(): ReflectionClass
    {
        return $this->reflect;
    }

    public function isModel(): bool
    {
        return $this->isModel;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function getMeta(): MetaClassItem
    {
        return $this->meta;
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
