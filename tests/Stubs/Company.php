<?php


namespace Victorlap\Mapp\Tests\Stubs;

use Victorlap\Mapp\Attributes\ListAttribute;

class Company
{
    public string $name;

    public Address $address;

    #[ListAttribute(Employee::class)]
    public array $employees;
}
