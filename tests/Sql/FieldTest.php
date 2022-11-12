<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    NullValue,
    RawValue,
};

/**
 * @requires PHP 8.1
 */
class FieldTest extends TestCase
{
    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(mixed $value)
    {
        $column = new Column('any-column');
        $field = new Field($column, '=', $value);

        $this->assertEquals($value->getValue(), $field->getValue());
        $this->assertEquals($column->getName(), $field->getColumnName());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals('`any-column` = ?', $field);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            [new StringValue('any-string')],
            [new NumberValue(5)],
            [new NumberValue(12.5)],
            [new BooleanValue(true)],
            [new NullValue()],
        ];
    }

    public function testShouldAcceptARawValue()
    {
        $column = new Column('any-column');
        $value = new RawValue('COUNT(*)');
        $field = new Field($column, '=', $value);

        $this->assertEquals($value->getValue(), $field->getValue());
        $this->assertEquals($column->getName(), $field->getColumnName());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals('`any-column` = COUNT(*)', $field);
    }

    /**
     * @dataProvider shouldShouldDisregardColumnRenamingAndTableNameProvider
     */
    public function testShouldDisregardColumnRenamingAndTableName(string|Column $column)
    {
        $field = new Field($column, '=', 'any-value');

        $this->assertEquals('`any-column` = ?', $field);
    }

    public function shouldShouldDisregardColumnRenamingAndTableNameProvider()
    {
        return [
            ['`any-column` AS `any-aliases`'],
            ['any-column AS any-aliases'],
            ['`any-table`.`any-column` AS `any-aliases`'],
            ['any-table.any-column AS any-aliases'],
        ];
    }

    /**
     * @dataProvider shouldAcceptAColumnInPlaceOfAValueAndDisregardColumnRenamingAndTableNameProvider
     */
    public function testShouldAcceptAColumnInPlaceOfAValueAndDisregardColumnRenamingAndTableName(Column $column)
    {
        $field = new Field('any-column', '=', $column);

        $this->assertEquals($column->getName(), $field->getValue());
        $this->assertEquals('`any-column` = `other-column`', $field);
    }

    public function shouldAcceptAColumnInPlaceOfAValueAndDisregardColumnRenamingAndTableNameProvider()
    {
        return [
            [new Column('other-column')],
            [new Column('`other-column`')],
            [new Column('`other-column` AS `any-aliases`')],
            [new Column('other-column AS any-aliases')],
            [new Column('`any-table`.`other-column` AS `any-aliases`')],
            [new Column('any-table.other-column AS any-aliases')],
        ];
    }
}
