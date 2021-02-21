<?php


namespace Victorlap\Mapp;

class MapperOptions
{
    public function __construct(
        public bool $allow_missing_properties = false,
        public bool $allow_missing_property_type = false,
    ) {
    }
}
