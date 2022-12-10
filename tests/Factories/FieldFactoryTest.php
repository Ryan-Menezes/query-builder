<?php

namespace Tests\Factories;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\{
    FieldFactory,
    ValueFactory,
};
use QueryBuilder\Sql\Field;

/**
 * @requires PHP 8.1
 */
class FieldFactoryTest extends TestCase
{
    public function testShouldCreateAnObjectOfTheFieldClass()
    {
        $field = FieldFactory::createField('any-column', '=', 'any-value');

        $column = 'any-column';
        $value = ValueFactory::createValue('any-value');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }

    public function testShouldCreateAnObjectOfTheFieldClassWithRawValue()
    {
        $field = FieldFactory::createFieldWithRawValue('any-column', '=', 'COUNT(*)');

        $column = 'any-column';
        $value = ValueFactory::createRawValue('COUNT(*)');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }

    public function testShouldCreateAnObjectOfTheFieldClassOnlyWithColumns()
    {
        $field = FieldFactory::createFieldOnlyWithColumns('any-column', '=', 'other-column');

        $column = 'any-column';
        $value = ValueFactory::createRawValue('other-column');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }
}
