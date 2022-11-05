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
            'new-column1',
            'new-column2',
            'new-column3',
        ];

        $columns = new Columns($list);

        return [
            [3, $columns->count()],
            ['`new-column1`, `new-column2`, `new-column3`', $columns],
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
        $columns = new Columns();

        $columns
            ->add('new-column1')
            ->add('new-column2')
            ->add('new-column3');

        return [
            [3, $columns->count()],
            ['`new-column1`, `new-column2`, `new-column3`', $columns],
            [['new-column1','new-column2','new-column3'], $columns->all()],
        ];
    }

    public function testShouldNotContainRepeatedColumns()
    {
        $columns = new Columns([
            'new-column1',
            'new-column1',
            'new-column2',
        ]);

        $columns
            ->add('new-column2')
            ->add('new-column2')
            ->add('new-column3')
            ->add('new-column3');

        $this->assertEquals(3, $columns->count());
        $this->assertEquals('`new-column1`, `new-column2`, `new-column3`', $columns);
    }
}
