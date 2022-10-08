<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Utils\ClassMetadata;

/**
 * Class Watcher.
 */
trait HasClassWatcher
{
    protected ?ClassMetadata $class_watcher = null;

    public function initializeHasClassWatcher()
    {
        $this->class_watcher = ClassMetadata::create($this);
    }

    public function getClassName(bool $withPlural = false)
    {
        $class = strtolower(str_replace('App\Models\\', '', get_class($this)));
        if ($withPlural) {
            return $class.'s';
        }

        return $class;
    }

    public function getClassNamespace()
    {
        return get_class($this);
    }
}
