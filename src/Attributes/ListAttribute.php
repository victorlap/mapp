<?php


namespace Victorlap\Mapp\Attributes;

use Attribute;

#[Attribute]
class ListAttribute
{
    /**
     * @psam-param class-string $class
     */
    public function __construct($class)
    {
    }
}
