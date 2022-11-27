<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\{
    Field,
    Column,
};

/**
 * @requires PHP 8.1
 */
class FieldTest extends TestCase
{
    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(mixed $value, string $expected)
    {
        $column = new Column('any-column');
        $value = ValueFactory::createValue($value);
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals($expected, $field);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            ['any-string', '`any-column` = ?'],
            [5, '`any-column` = ?'],
            [12.5, '`any-column` = ?'],
            [true, '`any-column` = ?'],
            [null, '`any-column` = ?'],
            [[1, 2, 3], '`any-column` = (?, ?, ?)'],
        ];
    }

    public function testShouldAcceptARawValue()
    {
        $column = new Column('any-column');
        $value = ValueFactory::createRawValue('COUNT(*)');
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('`any-column` = COUNT(*)', $field);
    }

    /**
     * @dataProvider shouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameterProvider
     */
    public function testShouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameter(string $column, string $expected)
    {
        $column = new Column($column);
        $value = ValueFactory::createValue('any-value');
        $field = new Field($column, '=', $value);

        $this->assertEquals($expected, $field);
    }

    public function shouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameterProvider()
    {
        return [
            ['`any-column`', '`any-column` = ?'],
            ['any-column', '`any-column` = ?'],
            ['`any-table`.`any-column`', '`any-table`.`any-column` = ?'],
            ['any-table.any-column', '`any-table`.`any-column` = ?'],
        ];
    }

    /**
     * @dataProvider shouldAcceptAColumnAsSecondParameterProvider
     */
    public function testShouldAcceptAColumnAsSecondParameter(string $value, string $expected)
    {
        $column = new Column('any-column');
        $value = ValueFactory::createRawValue(new Column($value));
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals($expected, $field);
    }

    public function shouldAcceptAColumnAsSecondParameterProvider()
    {
        return [
            ['other-column', '`any-column` = `other-column`'],
            ['`other-column`', '`any-column` = `other-column`'],
            ['`any-table`.`other-column`', '`any-column` = `any-table`.`other-column`'],
            ['any-table.other-column', '`any-column` = `any-table`.`other-column`'],
        ];
    }
}
