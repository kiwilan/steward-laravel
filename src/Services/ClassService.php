<?php

namespace Kiwilan\Steward\Services;

use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ClassService
{
    /** @var string[] */
    protected array $traits = [];

    protected function __construct(
        protected string $path,
        protected ?string $name = null,
        protected ?string $namespace = null,
        protected ?string $extends = null,
        protected ?string $implements = null,
        protected mixed $instance = null,
        protected ?ReflectionClass $reflect = null,
    ) {
    }

    public static function make(string $path): self
    {
        $self = new self($path);

        /** @var SplFileInfo[] */
        $file = iterator_to_array(
            Finder::create()->files()->path($path),
            false
        );
        $current = current($file);

        $self->namespace = $self->setNamespace($current);

        return $self;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function reflect(): ReflectionClass
    {
        return $this->reflect;
    }

    private function setNamespace(SplFileInfo $file): string
    {
        $path = $file->getPathName();
        $name = $file->getBasename('.php');

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
