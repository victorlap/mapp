<?php


namespace Victorlap\Mapp;


use ReflectionClass;
use ReflectionProperty;

class Inspector
{
    public static $inspectionResultCache = [];

    public function inspect($class): InspectionResult
    {
        if (isset(static::$inspectionResultCache[$class])) {
            return static::$inspectionResultCache[$class];
        }

        $result = new InspectionResult(
            $class,
            $this->inspectProperties($class)
        );

        static::$inspectionResultCache[$class] = $result;

        return $result;
    }

    private function inspectProperties($class)
    {
        $reflector = new ReflectionClass($class);

        return array_map(function (ReflectionProperty $property) use ($class) {
            return $this->inspectProperty($property, $class);
        }, $reflector->getProperties(ReflectionProperty::IS_PUBLIC));
    }

    private function inspectProperty(ReflectionProperty $property, $class)
    {
        return new PropertyInspectionResult($property->getType()?->getName(), $property->getName(), $property->getName());
    }
}
