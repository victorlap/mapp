<?php

namespace Victorlap\Mapp;

use Victorlap\Mapp\Exceptions\MissingPropertyException;

class Mapper
{
    private Inspector $inspector;

    public function __construct(Inspector $inspector = null)
    {
        $this->inspector = $inspector ?? new Inspector();
    }

    /**
     * @psalm-param string|array|object $json
     * @psalm-param class-string|object $class
     */
    public function map(string | array | object $json, string | object $class): object | array
    {
        $class = is_string($class) ? $class : get_class($class);

        if (is_string($json)) {
            $json = json_decode($json, true, JSON_THROW_ON_ERROR);
        }

        if (array_is_list((array)$json)) {
            return $this->mapArray($json, $class);
        }

        return $this->mapObject((array)$json, $class);
    }

    /**
     * @psalm-param class-string $class
     * @psalm-return
     */
    private function mapArray(array $array, string $class): array
    {
        return array_map(fn ($item) => $this->mapObject($item, $class), $array);
    }

    /**
     * @psalm-param class-string $class
     * @throws MissingPropertyException
     */
    private function mapObject(array $json, string $class): object
    {
        $inspectionResult = $this->inspector->inspect($class);

        $result = new $class;

        foreach ($inspectionResult->properties as $property) {
            if (! array_key_exists($property->jsonName, $json)) {
                throw MissingPropertyException::create($class, $property->jsonName);
            }

            $result->{$property->propertyName} = $this->mapProperty(
                $json[$property->jsonName],
                $property
            );
        }

        return $result;
    }

    public function mapProperty($value, PropertyInspectionResult $property)
    {
        return $value;
    }
}
