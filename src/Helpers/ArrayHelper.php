<?php


namespace Victorlap\Mapp\Helpers;

class ArrayHelper
{
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * Sourced from https://github.com/symfony/polyfill-php81/blob/main/Php81.php
     */
    public static function array_is_list(array $array): bool
    {
        if ([] === $array) {
            return true;
        }

        $nextKey = -1;

        foreach ($array as $k => $v) {
            if ($k !== ++$nextKey) {
                return false;
            }
        }

        return true;
    }
}
