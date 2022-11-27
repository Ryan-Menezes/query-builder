<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Columns;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;

/**
 * @requires PHP 8.1
 */
class ColumnsTest extends TestCase
{
    /**
     * @dataProvider shouldAcceptColumnAndStringTypeValues​InTheArrayPassedToTheConstructorAndFormatTheDataCorrectlyProvider
     */
    public function testShouldAcceptColumnAndStringTypeValues​InTheArrayPassedToTheConstructorAndFormatTheDataCorrectly($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    public function shouldAcceptColumnAndStringTypeValues​InTheArrayPassedToTheConstructorAndFormatTheDataCorrectlyProvider()
    {
        $columns = new Columns([
            new Column('any-column1'),
            'any-column2',
            'any-column AS any-aliases',
            'any-table.any-column AS any-aliases',
        ]);

        return [
            [4, $columns->count()],
            ['`any-column1`, `any-column2`, `any-column` AS `any-aliases`, `any-table`.`any-column` AS `any-aliases`', $columns],
            [['`any-column1`', '`any-column2`', '`any-column` AS `any-aliases`', '`any-table`.`any-column` AS `any-aliases`'], $columns->all()],
        ];
    }

    /**
     * @dataProvider shouldThrowAnExceptionIfColumnArrayHasAnyInvalidColumnProvider
     */
    public function testShouldThrowAnExceptionIfColumnArrayHasAnyInvalidColumn($invalidColumns)
    {
        $this->expectException(InvalidArgumentColumnException::class);

        new Columns($invalidColumns);
    }

    public function shouldThrowAnExceptionIfColumnArrayHasAnyInvalidColumnProvider()
    {
        return [
            [[123]],            // Int
            [['']],             // Empty String
            [[12.5]],           // Float
            [[new \StdClass]],  // Object
            [[null]],           // Null
            [[[]]],             // Array
            [[true]],           // Boolean
            [[function () {}]], // Callable
        ];
    }

    public function testShouldSupportRepeatedColumns()
    {
        $columns = new Columns([
            'any-column1',
            'any-column1',
            'any-column2',
            'any-column2',
            'any-column3',
            'any-column3',
        ]);

        $this->assertEquals(6, $columns->count());
        $this->assertEquals('`any-column1`, `any-column1`, `any-column2`, `any-column2`, `any-column3`, `any-column3`', $columns);
        $this->assertEquals([
            '`any-column1`',
            '`any-column1`',
            '`any-column2`',
            '`any-column2`',
            '`any-column3`',
            '`any-column3`'
        ], $columns->all());
    }

    public function testShouldSupportRawColumns()
    {
        $columns = new Columns([
            ValueFactory::createRawValue('COUNT(*) AS `count`'),
            ValueFactory::createRawValue('AVG(salary) AS `avg`'),
            ValueFactory::createRawValue('SUM(salary) AS sum'),
        ]);

        $this->assertEquals(3, $columns->count());
        $this->assertEquals('COUNT(*) AS `count`, AVG(salary) AS `avg`, SUM(salary) AS sum', $columns);
        $this->assertEquals([
            ValueFactory::createRawValue('COUNT(*) AS `count`'),
            ValueFactory::createRawValue('AVG(salary) AS `avg`'),
            ValueFactory::createRawValue('SUM(salary) AS sum'),
        ], $columns->all());
    }
}
