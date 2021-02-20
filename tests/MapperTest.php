<?php

namespace Victorlap\Mapp\Tests;

use PHPUnit\Framework\TestCase;
use Victorlap\Mapp\Mapper;
use Victorlap\Mapp\Tests\Stubs\Address;
use Victorlap\Mapp\Tests\Stubs\Company;

class MapperTest extends TestCase
{
    /** @test */
    public function mapper_can_be_instantiated()
    {
        $mapper = new Mapper();

        self::assertInstanceOf(Mapper::class, $mapper);
    }

    /** @test */
    public function mapper_can_map_by_class_instance()
    {
        $mapper = new Mapper();

        $company = $mapper->map($this->loadStub('simple'), new Company());

        self::assertInstanceOf(Company::class, $company);
    }

    /** @test */
    public function mapper_can_map_by_class_string()
    {
        $mapper = new Mapper();

        $company = $mapper->map($this->loadStub('simple'), Company::class);

        self::assertInstanceOf(Company::class, $company);
    }

    /** @test */
    public function mapper_can_map_single_class()
    {
        $mapper = new Mapper();

        /** @var Address $address */
        $address = $mapper->map($this->loadStub('address'), Address::class);

        self::assertInstanceOf(Address::class, $address);
        self::assertEquals("Fake street 2", $address->line1);
        self::assertEquals("floor 2", $address->line2);
        self::assertEquals("1234", $address->zipcode);
        self::assertEquals("Amsterdam", $address->city);
        self::assertEquals("Netherlands", $address->country);
    }

    /** @test */
    public function mapper_can_map_relations_class()
    {
        $mapper = new Mapper();

        /** @var Company $company */
        $company = $mapper->map($this->loadStub('simple'), Company::class);

        self::assertInstanceOf(Company::class, $company);
        self::assertEquals("Acme Corp", $company->name);

        self::assertInstanceOf(Address::class, $company->address);
        self::assertEquals("Fake street 2", $company->address->line1);
        self::assertEquals("floor 2", $company->address->line2);
        self::assertEquals("1234", $company->address->zipcode);
        self::assertEquals("Amsterdam", $company->address->city);
        self::assertEquals("Netherlands", $company->address->country);
    }

    private function loadStub(string $name)
    {
        $path = __DIR__ . '/json/' . $name . '.json';

        $contents = file_get_contents($path);

        return json_decode($contents);
    }
}
