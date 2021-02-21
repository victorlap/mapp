<?php


namespace Victorlap\Mapp\Helpers;

use ReflectionAttribute;
use Victorlap\Mapp\Attributes\AttributeInterface;

class TypeHelper
{
    public static function isSimpleType(string $type): bool
    {
        return in_array($type, [
            'string', 'int', 'bool', 'double',
        ]);
    }

    /**
     * @psalm-param string|int|float|object|array $value
     * @psalm-param  list<string> $types
     */
    public static function determineSuitableType(mixed $value, array $types): ?string
    {
        // TODO

        if ($types[0] instanceof AttributeInterface) {
            return $types[0]->getType();
        }

        return $types[0];
    }

    /**
     * @psalm-param $reflectionAttributes list<ReflectionAttribute>
     *
     * @psalm-return list<AttributeInterface>
     */
    public static function determineTypeAttributes(array $reflectionAttributes): array
    {
        return array_values(array_filter(
            array_map(
                static fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
                $reflectionAttributes
            ),
            static fn ($attribute) => $attribute instanceof AttributeInterface
        ));
    }
}
