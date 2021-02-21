<?php

namespace Victorlap\Mapp;

use Victorlap\Mapp\Exceptions\MissingPropertyException;
use Victorlap\Mapp\Helpers\ArrayHelper;
use Victorlap\Mapp\Helpers\NameHelper;
use Victorlap\Mapp\Helpers\TypeHelper;
use Victorlap\Mapp\Inspection\Inspector;
use Victorlap\Mapp\Inspection\Result\PropertyInspectionResult;

class Mapper
{
    private MapperOptions $options;
    private Inspector $inspector;

    public function __construct(MapperOptions $options = null, Inspector $inspector = null)
    {
        $this->options = $options ?? new MapperOptions();
        $this->inspector = $inspector ?? new Inspector($this->options);
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

        if (is_object($json) || ! ArrayHelper::array_is_list($json)) {
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
            $jsonName = $property->jsonName;

            if (! array_key_exists($jsonName, $json)) {
                $jsonName = NameHelper::hyphenate($jsonName);
            }

            if (! array_key_exists($jsonName, $json)) {
                if ($this->options->allow_missing_properties) {
                    continue;
                }

                throw MissingPropertyException::create($class, $jsonName);
            }

            $result->{$property->propertyName} = $this->mapProperty(
                $json[$jsonName],
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
        if (is_null($property->type)) {
            return $value;
        }

        $type = TypeHelper::determineSuitableType($value, $property->type->types);

        if (is_array($value)) {
            return $this->mapArray($value, $type);
        }

        if (is_null($value) && $property->type->allowsNull) {
            return null;
        }

        if (class_exists($type)) {
            return $this->mapObject($value, $type);
        }

        if ($type === 'mixed') {
            return $value;
        }

        settype($value, $type);

        return $value;
    }
}
