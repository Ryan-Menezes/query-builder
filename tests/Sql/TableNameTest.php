<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Exceptions\InvalidArgumentTableNameException;
use QueryBuilder\Sql\TableName;

/**
 * @requires PHP 8.1
 */
class TableNameTest extends TestCase
{
    /**
     * @dataProvider shouldFormatATableName
     */
    public function testShouldFormatATableName(string $tableName, string $expected)
    {
        $tableName = new TableName($tableName);

        $this->assertEquals($expected, $tableName);
    }

    public function shouldFormatATableName()
    {
        return [
            ['any-table-name', '`any-table-name`'],
            ['`any-table-name`', '`any-table-name`'],
            ['any-table-name AS any-aliases', '`any-table-name` AS `any-aliases`'],
            ['`any-table-name` AS `any-aliases`', '`any-table-name` AS `any-aliases`'],
            ['any-table-name as any-aliases', '`any-table-name` AS `any-aliases`'],
            ['`any-table-name` as `any-aliases`', '`any-table-name` AS `any-aliases`'],
            ['AS any-aliases', '`AS any-aliases`'],
            ['AS `any-aliases`', '`AS any-aliases`'],
            ['as any-aliases', '`as any-aliases`'],
            ['as `any-aliases`', '`as any-aliases`'],
            [' AS any-aliases', '`AS any-aliases`'],
            [' AS `any-aliases`', '`AS any-aliases`'],
            [' as any-aliases', '`as any-aliases`'],
            [' as `any-aliases`', '`as any-aliases`'],
        ];
    }

    public function testShouldReturnAnErrorIfAnEmptyTableNameIsPassed()
    {
        $this->expectException(InvalidArgumentTableNameException::class);

        new TableName('');
    }
}
