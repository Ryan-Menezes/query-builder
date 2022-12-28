<?php

namespace Tests\Factories;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\{FieldFactory, ValueFactory};
use QueryBuilder\Sql\Field;

/**
 * @requires PHP 8.1
 */
class FieldFactoryTest extends TestCase
{
    public function testShouldCreateAnObjectOfTheFieldClass()
    {
        $columnName = 'any-column';
        $operator = '=';
        $value = 'any-value';
        $sut = FieldFactory::createField($columnName, $operator, $value);

        $valueExpected = ValueFactory::createValue($value);
        $expect = new Field($columnName, $operator, $valueExpected);

        $this->assertEquals($expect, $sut);
    }

    public function testShouldCreateAnObjectOfTheFieldClassWithRawValue()
    {
        $columnName = 'any-column';
        $operator = '=';
        $value = 'COUNT(*)';
        $sut = FieldFactory::createFieldWithRawValue(
            $columnName,
            $operator,
            $value,
        );

        $valueExpected = ValueFactory::createRawValue($value);
        $expect = new Field($columnName, $operator, $valueExpected);

        $this->assertEquals($expect, $sut);
    }

    public function testShouldCreateAnObjectOfTheFieldClassOnlyWithColumns()
    {
        $columnName = 'any-column';
        $operator = '=';
        $value = 'other-column';
        $field = FieldFactory::createFieldOnlyWithColumns(
            $columnName,
            $operator,
            $value,
        );

        $valueExpected = ValueFactory::createRawValue($value);
        $expect = new Field($columnName, $operator, $valueExpected);

        $this->assertEquals($expect, $field);
    }
}
