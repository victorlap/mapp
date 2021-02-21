<?php


namespace Victorlap\Mapp\Helpers;

class NameHelper
{
    public static function camelcase(string $name): string
    {
        if (! str_contains($name, '-')) {
            return $name;
        }

        $parts = explode('-', $name);

        return array_shift($parts) . implode('', array_map(static fn ($part) => ucfirst($part), $parts));
    }

    public static function hyphenate(string $name): string
    {
        $parts = preg_split('/(?=[A-Z])/', $name);

        return implode('-', array_map(fn ($part) => strtolower($part), $parts));
    }
}
