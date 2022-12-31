<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Operators\Logical\Where;
use QueryBuilder\Interfaces\{
    LogicalInstructionsInterface,
    FieldInterface,
    SqlInterface,
};

/**
 * @requires PHP 8.1
 */
class WhereTest extends TestCase
{
    public function makeSut(): LogicalInstructionsInterface
    {
        $sqlMock = $this->createMock(SqlInterface::class);
        $sqlMock->method('__toString')->willReturn('SELECT * FROM `any-table`');
        $sqlMock->method('toSql')->willReturn('SELECT * FROM `any-table`');

        return new Where($sqlMock);
    }

    private function createFieldMock(string $toStringReturn): FieldInterface
    {
        $fieldMock = $this->createMock(FieldInterface::class);
        $fieldMock->method('toSql')->willReturn($toStringReturn);
        $fieldMock->method('__toString')->willReturn($toStringReturn);

        return $fieldMock;
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodAnd()
    {
        $sut = $this->makeSut();
        $fieldMock = $this->createFieldMock('name = ?');

        $sut->and($fieldMock);

        $this->assertEquals(
            'SELECT * FROM `any-table` WHERE name = ?',
            $sut->toSql(),
        );
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodOr()
    {
        $sut = $this->makeSut();
        $fieldMock = $this->createFieldMock('name = ?');

        $sut->or($fieldMock);

        $this->assertEquals(
            'SELECT * FROM `any-table` WHERE name = ?',
            $sut->toSql(),
        );
    }

    public function testShouldStackStatementsNextToEachOther()
    {
        $sut = $this->makeSut();

        $fieldMock1 = $this->createFieldMock('name = ?');
        $fieldMock2 = $this->createFieldMock('age = ?');
        $fieldMock3 = $this->createFieldMock('birth = ?');

        $sut->or($fieldMock1);
        $sut->and($fieldMock2);
        $sut->or($fieldMock3);

        $this->assertEquals(
            'SELECT * FROM `any-table` WHERE name = ? AND age = ? OR birth = ?',
            $sut->toSql(),
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $sut = $this->makeSut();
        $this->assertEquals('', $sut->toSql());
    }
}
