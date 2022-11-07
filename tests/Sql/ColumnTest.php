<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;

/**
 * @requires PHP 8.1
 */
class ColumnTest extends TestCase
{
    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(Column $column)
    {
        $this->assertEquals('any-column', $column->getColumnName());
        $this->assertEquals($column, '`any-column`');
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            [new Column('any-column')],
            [new Column('`any-column`')],
            [new Column('`any-column')],
            [new Column('any-column`')],
            [new Column('```any-column```')],
        ];
    }

    public function shouldReturnAnErrorIfAnEmptyColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnException::class);

        new Column('');
    }
}