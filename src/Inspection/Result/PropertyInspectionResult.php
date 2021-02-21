<?php


namespace Victorlap\Mapp\Inspection\Result;

class PropertyInspectionResult
{
    public function __construct(
        public ?TypeInspectionResult $type,
        public string $propertyName,
        public string $jsonName,
    ) {
    }
}
