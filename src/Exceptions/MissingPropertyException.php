<?php


namespace Victorlap\Mapp\Exceptions;

use Exception;

class MissingPropertyException extends Exception
{
    public static function create($class, $property)
    {
        return new self(sprintf("Missing property %s in %s", $property, $class));
    }
}
