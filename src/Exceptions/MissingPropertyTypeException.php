<?php


namespace Victorlap\Mapp\Exceptions;

use Exception;

class MissingPropertyTypeException extends Exception
{
    public static function create(string $class, string $property): self
    {
        return new self(sprintf("Missing property type for %s in %s", $property, $class));
    }
}
