<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\Steward\Utils\Language\LanguageList;

class Language
{
    /**
     * @return Language\LanguageItem[]
     */
    public static function getList(): array
    {
        return LanguageList::toArray();
    }

    public static function getFromName(string $name): ?Language\LanguageItem
    {
        return LanguageList::getFromName($name);
    }
}
