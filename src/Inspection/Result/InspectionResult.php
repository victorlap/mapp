<?php


namespace Victorlap\Mapp\Inspection\Result;

class InspectionResult
{
    /**
     * @psam-param class-string $class
     * @psalm-param PropertyInspectionResult[] $properties
     */
    public function __construct(
        public string $class,
        public array $properties
    ) {
    }
}
