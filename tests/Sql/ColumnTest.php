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
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(string $column)
    {
        $column = new Column($column);

        $this->assertEquals('`any-column`', $column);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            ['any-column'],
            ['`any-column`'],
            ['`any-column'],
            ['any-column`'],
            ['```any-column```'],
        ];
    }

    /**
     * @dataProvider shouldSupportRenamedColumnsProvider
     */
    public function testShouldSupportRenamedColumns(string $column)
    {
        $column = new Column($column);

        $this->assertEquals('`any-column` AS `any-aliases`', $column);
    }

    public function shouldSupportRenamedColumnsProvider()
    {
        return [
            ['any-column as any-aliases'],
            ['any-column AS any-aliases'],
            ['any-column As any-aliases'],
            ['any-column aS any-aliases'],
            ['`any-column` AS `any-aliases`'],
            ['`any-column AS `any-aliases`'],
            ['any-column` AS `any-aliases`'],
            ['`any-column` AS any-aliases`'],
            ['`any-column` AS `any-aliases'],
            ['```any-column``` AS ```any-aliases```'],
            ['.`any-column` AS `any-aliases`'],
            ['.any-column` AS any-aliases'],
            ['`.any-column` AS any-aliases'],
        ];
    }

    public function testShouldReturnAnErrorIfAnEmptyColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnException::class);

        new Column('');
    }

    /**
     * @dataProvider shouldAcceptColumnsWithTableNameSpecificationProvider
     */
    public function testShouldAcceptColumnsWithTableNameSpecification(string $column, $expected)
    {
        $column = new Column($column);

        $this->assertEquals($expected, $column);
    }

    public function shouldAcceptColumnsWithTableNameSpecificationProvider() {
        return [
            ['any-table.any-column', '`any-table`.`any-column`'],
            ['`any-table`.`any-column`', '`any-table`.`any-column`'],
            ['any-table.any-column AS any-aliases', '`any-table`.`any-column` AS `any-aliases`'],
            ['`any-table`.`any-column` AS `any-aliases`', '`any-table`.`any-column` AS `any-aliases`'],
        ];
    }
}
