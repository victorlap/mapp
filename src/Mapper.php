<?php

namespace Victorlap\Mapp;

use Victorlap\Mapp\Exceptions\MissingPropertyException;
use Victorlap\Mapp\Inspection\Inspector;
use Victorlap\Mapp\Inspection\Result\PropertyInspectionResult;

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

        if (is_object($json) || ! array_is_list($json)) {
            return $this->mapObject($json, $class);
        }

        return $this->mapArray($json, $class);
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
    private function mapObject(array | object $json, string $class): object
    {
        $json = (array)$json;

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

    /**
     * @psalm-param string|int|float|object|array $value
     */
    public function mapProperty(mixed $value, PropertyInspectionResult $property): mixed
    {
        $type = TypeHelper::determineSuitableType($value, $property->type->types);

        if (is_array($value)) {
            return $this->mapArray($value, $type);
        }

        if (class_exists($type)) {
            return $this->mapObject($value, $type);
        }

        settype($value, $type);

        return $value;
    }
}
