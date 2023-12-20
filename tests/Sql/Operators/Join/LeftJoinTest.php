<?php

namespace Tests\Sql\Operators\Join;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\Values\StringValue;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Sql\Operators\Join\LeftJoin;
use QueryBuilder\Sql\Values\NumberValue;

/**
 * @requires PHP 8.1
 */
class LeftJoinTest extends TestCase
{
    public function makeSut(string $tableName): LeftJoin
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

        return new LeftJoin($tableName, $sqlMock);
    }

    private function createFieldMock(
        string $toStringReturn,
        mixed $value,
    ): FieldInterface {
        $value = ValueFactory::createValue($value);
        $fieldMock = $this->createMock(FieldInterface::class);
        $fieldMock->method('toSql')->willReturn($toStringReturn);
        $fieldMock->method('__toString')->willReturn($toStringReturn);
        $fieldMock->method('getValue')->willReturn($value);

        return $fieldMock;
    }

    public function testShouldCreateALeftJoinStatementCorrectly()
    {
        $sut = $this->makeSut('test');

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` LEFT JOIN test',
            $sut->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-value-sql')],
            $sut->getValues(),
        );
    }

    public function testShouldAcceptLogicalInstrutions()
    {
        $sut = $this->makeSut('test');

        $fieldMock1 = $this->createFieldMock('name = ?', 'any-value');
        $fieldMock2 = $this->createFieldMock('age = ?', 23);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` LEFT JOIN test ON name = ?',
            $sut->on($fieldMock1)->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-value-sql'), new StringValue('any-value')],
            $sut->getValues(),
        );

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` LEFT JOIN test ON name = ? OR age = ?',
            $sut->orOn($fieldMock2)->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-value-sql'),
                new StringValue('any-value'),
                new NumberValue(23),
            ],
            $sut->getValues(),
        );
    }
}
