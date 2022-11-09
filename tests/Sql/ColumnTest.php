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
        $this->assertEquals('any-column', $column->getName());
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

    /**
     * @dataProvider shouldSupportRenamedColumnsProvider
     */
    public function testShouldSupportRenamedColumns(Column $column)
    {
        $this->assertEquals('any-column', $column->getName());
        $this->assertEquals('any-nickname', $column->getNickname());
        $this->assertEquals($column, '`any-column` AS `any-nickname`');
    }

    public function shouldSupportRenamedColumnsProvider()
    {
        return [
            [new Column('any-column as any-nickname')],
            [new Column('any-column AS any-nickname')],
            [new Column('any-column As any-nickname')],
            [new Column('any-column aS any-nickname')],
            [new Column('`any-column` AS `any-nickname`')],
            [new Column('`any-column AS `any-nickname`')],
            [new Column('any-column` AS `any-nickname`')],
            [new Column('`any-column` AS any-nickname`')],
            [new Column('`any-column` AS `any-nickname')],
            [new Column('```any-column``` AS ```any-nickname```')],
        ];
    }

    public function shouldReturnAnErrorIfAnEmptyColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnException::class);

        new Column('');
    }
}
