<?php

namespace Tests\Sql\Operators\Join;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\StringValue;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Sql\Operators\Join\CrossJoin;

/**
 * @requires PHP 8.1
 */
class CrossJoinTest extends TestCase
{
    public function makeSut(string $tableName): CrossJoin
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

        return new CrossJoin($tableName, $sqlMock);
    }

    public function testShouldCreateACrossJoinStatementCorrectly()
    {
        $sut = $this->makeSut('test');

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` CROSS JOIN test',
            $sut->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-value-sql')],
            $sut->getValues(),
        );
    }
}
