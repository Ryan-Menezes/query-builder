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
        $this->assertEquals('any-aliases', $column->getAliases());
        $this->assertEquals($column, '`any-column` AS `any-aliases`');
    }

    public function shouldSupportRenamedColumnsProvider()
    {
        return [
            [new Column('any-column as any-aliases')],
            [new Column('any-column AS any-aliases')],
            [new Column('any-column As any-aliases')],
            [new Column('any-column aS any-aliases')],
            [new Column('`any-column` AS `any-aliases`')],
            [new Column('`any-column AS `any-aliases`')],
            [new Column('any-column` AS `any-aliases`')],
            [new Column('`any-column` AS any-aliases`')],
            [new Column('`any-column` AS `any-aliases')],
            [new Column('```any-column``` AS ```any-aliases```')],
        ];
    }

    public function shouldReturnAnErrorIfAnEmptyColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnException::class);

        new Column('');
    }
}
