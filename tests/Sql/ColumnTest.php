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
        $this->assertEquals('`any-column`', $column);
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
        $this->assertEquals('`any-column` AS `any-aliases`', $column);
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

    /**
     * @dataProvider shouldAcceptColumnsWithTableNameSpecificationProvider
     */
    public function testShouldAcceptColumnsWithTableNameSpecification(Column $column, $expected)
    {
        $this->assertEquals($expected, $column);
    }

    public function shouldAcceptColumnsWithTableNameSpecificationProvider() {
        return [
            [new Column('any-table.any-column'), '`any-table`.`any-column`'],
            [new Column('`any-table`.`any-column`'), '`any-table`.`any-column`'],
            [new Column('any-table.any-column AS any-aliases'), '`any-table`.`any-column` AS `any-aliases`'],
            [new Column('`any-table`.`any-column` AS `any-aliases`'), '`any-table`.`any-column` AS `any-aliases`'],
        ];
    }
}
