<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Columns;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;

class ColumnsTest extends TestCase
{
    /**
     * @dataProvider shouldCreateAColumnsClassObjectWithThreeColumnsProvider
     */
    public function testShouldCreateAColumnsClassObjectWithThreeColumnsFromItsConstructor($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    public function shouldCreateAColumnsClassObjectWithThreeColumnsProvider()
    {
        $list = [
            'any-column1',
            'any-column2',
            'any-column3',
        ];

        $columns = new Columns($list);

        return [
            [3, $columns->count()],
            ['`any-column1`, `any-column2`, `any-column3`', $columns],
            [$list, $columns->all()],
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

    /**
     * @dataProvider shouldAddNewColumnsProvider
     */
    public function testShouldAddNewColumns($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    public function shouldAddNewColumnsProvider()
    {
        $list = [
            'any-column1',
            'any-column2',
            'any-column3',
        ];

        $columns = new Columns($list);

        return [
            [3, $columns->count()],
            ['`any-column1`, `any-column2`, `any-column3`', $columns],
            [$list, $columns->all()],
        ];
    }

    public function testShouldNotContainRepeatedColumns()
    {
        $columns = new Columns([
            'any-column1',
            'any-column1',
            'any-column2',
            'any-column2',
            'any-column2',
            'any-column3',
            'any-column3',
        ]);

        $this->assertEquals(3, $columns->count());
        $this->assertEquals('`any-column1`, `any-column2`, `any-column3`', $columns);
    }
}
