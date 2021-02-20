<?php


namespace Victorlap\Mapp;

class PropertyInspectionResult
{
    public function __construct(
        public $value,
        public $types,
        public string $propertyName,
        public string $jsonName,
    ) {
    }
}
