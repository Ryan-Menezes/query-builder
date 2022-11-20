<?php

namespace Tests\Factories;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\{
    FieldFactory,
    ValueFactory,
};
use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Values\RawValue;

/**
 * @requires PHP 8.1
 */
class FieldFactoryTest extends TestCase
{
    public function testShouldCreateAnObjectOfTheFieldClass()
    {
        $field = FieldFactory::createField('any-column', '=', 'any-value');

        $column = new Column('any-column');
        $value = ValueFactory::createValue('any-value');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }

    public function testShouldCreateAnObjectOfTheFieldClassWithRawValue()
    {
        $field = FieldFactory::createFieldWithRawValue('any-column', '=', 'COUNT(*)');

        $column = new Column('any-column');
        $value = new RawValue('COUNT(*)');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }

    public function testShouldCreateAnObjectOfTheFieldClassOnlyWithColumns()
    {
        $field = FieldFactory::createFieldOnlyWithColumns('any-column', '=', 'other-column');

        $column = new Column('any-column');
        $value = new Column('other-column');
        $expect = new Field($column, '=', $value);

        $this->assertEquals($expect, $field);
    }
}
