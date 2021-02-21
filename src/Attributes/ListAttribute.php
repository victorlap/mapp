<?php


namespace Victorlap\Mapp\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ListAttribute implements AttributeInterface
{
    /**
     * @psam-param class-string $class
     */
    public function __construct(public string $class)
    {
    }

    public function getType(): string
    {
        return $this->class;
    }
}
