<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Operators\Logical\Having;
use QueryBuilder\Sql\Values\{StringValue, NumberValue};
use QueryBuilder\Interfaces\{
    LogicalInstructionsInterface,
    FieldInterface,
    SqlWithValuesInterface,
};

/**
 * @requires PHP 8.1
 */
class HavingTest extends TestCase
{
    public function makeSut(): LogicalInstructionsInterface
    {
        $sqlMock = $this->createMock(SqlWithValuesInterface::class);
        $sqlMock
            ->method('__toString')
            ->willReturn(
                'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column`',
            );
        $sqlMock
            ->method('toSql')
            ->willReturn(
                'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column`',
            );
        $sqlMock
            ->method('getValues')
            ->willReturn([new StringValue('any-value-sql')]);

        return new Having($sqlMock);
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

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodAnd()
    {
        $sut = $this->makeSut();
        $fieldMock = $this->createFieldMock('name = ?', 'any-value');

        $sut->and($fieldMock);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column` HAVING name = ?',
            $sut->toSql(),
        );
        $this->assertEquals(
            [new StringValue('any-value-sql'), new StringValue('any-value')],
            $sut->getValues(),
        );
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodOr()
    {
        $sut = $this->makeSut();
        $fieldMock = $this->createFieldMock('name = ?', 'any-value');

        $sut->or($fieldMock);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column` HAVING name = ?',
            $sut->toSql(),
        );
        $this->assertEquals(
            [new StringValue('any-value-sql'), new StringValue('any-value')],
            $sut->getValues(),
        );
    }

    public function testShouldStackStatementsNextToEachOther()
    {
        $sut = $this->makeSut();

        $fieldMock1 = $this->createFieldMock('name = ?', 'any-value');
        $fieldMock2 = $this->createFieldMock('age = ?', 23);
        $fieldMock3 = $this->createFieldMock('birth = ?', '2000-01-01');

        $sut->or($fieldMock1);
        $sut->and($fieldMock2);
        $sut->or($fieldMock3);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column` HAVING name = ? AND age = ? OR birth = ?',
            $sut->toSql(),
        );
        $this->assertEquals(
            [
                new StringValue('any-value-sql'),
                new StringValue('any-value'),
                new NumberValue(23),
                new StringValue('2000-01-01'),
            ],
            $sut->getValues(),
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $sut = $this->makeSut();
        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table` GROUP BY `any-column`',
            $sut->toSql(),
        );
    }
}
