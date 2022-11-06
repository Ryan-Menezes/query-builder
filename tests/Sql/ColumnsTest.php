<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Columns;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;

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
            new Column('any-column2'),
            'any-column3',
            'any-column4',
            'any-column5',
        ]);

        return [
            [5, $columns->count()],
            ['`any-column1`, `any-column2`, `any-column3`, `any-column4`, `any-column5`', $columns],
            [['`any-column1`', '`any-column2`', '`any-column3`', '`any-column4`', '`any-column5`'], $columns->all()],
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
            [[function(){}]],   // Callable
        ];
    }

    public function testShouldNotContainRepeatedColumns()
    {
        $columns = new Columns([
            'any-column1',
            'any-column1',
            'any-column1',
            'any-column2',
            'any-column2',
            'any-column2',
            'any-column3',
            'any-column3',
            'any-column3',
        ]);

        $this->assertEquals(3, $columns->count());
        $this->assertEquals('`any-column1`, `any-column2`, `any-column3`', $columns);
        $this->assertEquals(['`any-column1`', '`any-column2`', '`any-column3`'], $columns->all());
    }
}
