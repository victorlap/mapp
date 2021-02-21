<?php


namespace Victorlap\Mapp\Inspection;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;
use Victorlap\Mapp\Exceptions\MissingPropertyTypeException;
use Victorlap\Mapp\Inspection\Result\InspectionResult;
use Victorlap\Mapp\Inspection\Result\PropertyInspectionResult;
use Victorlap\Mapp\Inspection\Result\TypeInspectionResult;
use Victorlap\Mapp\TypeHelper;

class Inspector
{
    /** @psalm-var array<class-string,InspectionResult> */
    public static array $inspectionResultCache = [];

    /**
     * @param class-string $class
     */
    public function inspect(string $class): InspectionResult
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

    /**
     * @psalm-return list<PropertyInspectionResult>
     * @param class-string $class
     */
    private function inspectProperties(string $class): array
    {
        $reflector = new ReflectionClass($class);

        return array_map(function (ReflectionProperty $property) {
            return $this->inspectProperty($property);
        }, $reflector->getProperties(ReflectionProperty::IS_PUBLIC));
    }

    private function inspectProperty(ReflectionProperty $property): PropertyInspectionResult
    {
        return new PropertyInspectionResult(
            $this->inspectType($property),
            $property->getName(),
            $property->getName()
        );
    }

    /**
     * @psalm-param $reflectionAttributes list<ReflectionAttribute>
     *
     * @return TypeInspectionResult
     */
    private function inspectType(ReflectionProperty $property): TypeInspectionResult
    {
        $type = $property->getType();
        $typeAttributes = TypeHelper::determineTypeAttributes($property->getAttributes());

        if (count($typeAttributes) > 0) {
            return new TypeInspectionResult($typeAttributes, ! $type || $type->allowsNull());
        }

        if (is_null($type)) {
            throw MissingPropertyTypeException::create($property->class, $property->getName());
        }

        if ($type instanceof ReflectionUnionType) {
            $types = [];
            $allowsNull = false;

            foreach ($type->getTypes() as $subType) {
                $types[] = $subType->getName();
                if ($subType->allowsNull()) {
                    $allowsNull = true;
                }
            }

            return new TypeInspectionResult($types, $allowsNull);
        }

        if ($type instanceof ReflectionNamedType) {
            return new TypeInspectionResult([$type->getName()], $type->allowsNull());
        }

        throw new RuntimeException("ReflectionType not an instance of \ReflectionUnionType or \ReflectionNamedType");
    }
}
