<?php

namespace Victorlap\Mapp\Tests;

use PHPUnit\Framework\TestCase;
use Victorlap\Mapp\Exceptions\MissingPropertyException;
use Victorlap\Mapp\Exceptions\MissingPropertyTypeException;
use Victorlap\Mapp\Mapper;
use Victorlap\Mapp\MapperOptions;
use Victorlap\Mapp\Tests\Stubs\Simple;

class SimpleTest extends TestCase
{
    private Mapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Mapper(
            new MapperOptions(
                allow_missing_properties: true,
                allow_missing_property_type: true
            )
        );
    }

    public function test_map_missing_data_throws_exception()
    {
        $this->expectException(MissingPropertyException::class);
        $this->expectExceptionMessage("Missing property string in Victorlap\Mapp\Tests\Stubs\Simple");

        $mapper = new Mapper(new MapperOptions(allow_missing_property_type: true));
        $mapper->map(
            json_decode('{}'),
            new Simple()
        );
    }

    public function test_map_missing_type_throws_exception()
    {
        $this->expectException(MissingPropertyTypeException::class);
        $this->expectExceptionMessage("Missing property type for notype in Victorlap\Mapp\Tests\Stubs\Simple");

        $mapper = new Mapper(new MapperOptions(allow_missing_properties: true));
        $mapper->map(
            json_decode('{}'),
            new Simple()
        );
    }

    public function test_map_simple_string()
    {
        $result = $this->mapper->map(
            json_decode('{"string":"stringvalue"}'),
            new Simple()
        );
        self::assertIsString($result->string);
        self::assertEquals('stringvalue', $result->string);
    }

    public function test_map_simple_float()
    {
        $result = $this->mapper->map(
            json_decode('{"float":"1.2"}'),
            new Simple()
        );
        self::assertIsFloat($result->float);
        self::assertEquals(1.2, $result->float);
    }

    public function test_map_simple_bool()
    {
        $result = $this->mapper->map(
            json_decode('{"bool":"1"}'),
            new Simple()
        );
        self::assertIsBool($result->bool);
        self::assertEquals(true, $result->bool);
    }

    public function test_map_simple_int()
    {
        $result = $this->mapper->map(
            json_decode('{"int":"123"}'),
            new Simple()
        );
        self::assertIsInt($result->int);
        self::assertEquals(123, $result->int);
    }

    public function test_map_simple_mixed()
    {
        $result = $this->mapper->map(
            json_decode('{"mixed":12345}'),
            new Simple()
        );
        self::assertIsInt($result->mixed);
        self::assertEquals(12345, $result->mixed);

        $result = $this->mapper->map(
            json_decode('{"mixed":"12345"}'),
            new Simple()
        );
        self::assertIsString($result->mixed);
        self::assertEquals('12345', $result->mixed);
    }

    public function test_map_simple_nullable_int()
    {
        $result = $this->mapper->map(
            json_decode('{"nullable_int":0}'),
            new Simple()
        );
        self::assertIsInt($result->nullable_int);
        self::assertEquals(0, $result->nullable_int);
    }

    public function test_map_simple_nullable_int_null()
    {
        $result = $this->mapper->map(
            json_decode('{"nullable_int":null}'),
            new Simple()
        );
        self::assertNull($result->nullable_int);
        self::assertEquals(null, $result->nullable_int);
    }

    public function test_map_simple_nullable_int_string()
    {
        $result = $this->mapper->map(
            json_decode('{"nullable_int":"12345"}'),
            new Simple()
        );
        self::assertIsInt($result->nullable_int);
        self::assertEquals(12345, $result->nullable_int);
    }

    public function test_map_simple_notype()
    {
        $result = $this->mapper->map(
            json_decode('{"notype":{"k":"v"}}'),
            new Simple()
        );
        self::assertIsObject($result->notype);
        self::assertEquals((object)['k' => 'v'], $result->notype);
    }

    public function test_map_simple_underscore()
    {
        $result = $this->mapper->map(
            json_decode('{"under_score":"f"}'),
            new Simple()
        );
        self::assertIsString($result->under_score);
        self::assertEquals('f', $result->under_score);
    }

    public function test_map_simple_hyphen()
    {
        $result = $this->mapper->map(
            json_decode('{"hyphen-value":"test"}'),
            new Simple()
        );

        self::assertIsString($result->hyphenValue);
        self::assertEquals('test', $result->hyphenValue);
    }
}
