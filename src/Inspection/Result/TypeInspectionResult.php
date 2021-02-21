<?php


namespace Victorlap\Mapp\Inspection\Result;

class TypeInspectionResult
{
    public function __construct(
        public array $types,
        public bool $allowsNull,
    ) {
    }
}
