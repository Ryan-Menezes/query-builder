<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\StringValue;
use QueryBuilder\Sql\Operators\Limit;
use QueryBuilder\Interfaces\SqlWithValuesInterface;

/**
 * @requires PHP 8.1
 */
class LimitTest extends TestCase
{
    public function makeSut(int $value): Limit
    {
        $sqlMock = $this->createMock(SqlWithValuesInterface::class);
        $sqlMock
            ->method('__toString')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table`');
        $sqlMock
            ->method('toSql')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table`');
        $sqlMock
            ->method('getValues')
            ->willReturn([new StringValue('any-value-sql')]);

        return new Limit($sqlMock, $value);
    }

    public function testShouldCreateAnLimitStatementCorrectly()
    {
        $sut = $this->makeSut(10);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` LIMIT 10',
            $sut->toSql(),
        );
    }
}
