<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\StringValue;
use QueryBuilder\Sql\Operators\Offset;
use QueryBuilder\Interfaces\SqlWithValuesInterface;

/**
 * @requires PHP 8.1
 */
class OffsetTest extends TestCase
{
    public function makeSut(int $value): Offset
    {
        $sqlMock = $this->createMock(SqlWithValuesInterface::class);
        $sqlMock
            ->method('__toString')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table` LIMIT 10');
        $sqlMock
            ->method('toSql')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table` LIMIT 10');
        $sqlMock
            ->method('getValues')
            ->willReturn([new StringValue('any-value-sql')]);

        return new Offset($value, $sqlMock);
    }

    public function testShouldCreateAnOffsetStatementCorrectly()
    {
        $sut = $this->makeSut(0);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` LIMIT 10 OFFSET 0',
            $sut->toSql(),
        );
    }
}
