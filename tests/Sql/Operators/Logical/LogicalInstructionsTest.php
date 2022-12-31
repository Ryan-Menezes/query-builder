<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Interfaces\LogicalInstructionsInterface;
use QueryBuilder\Sql\Operators\Logical\LogicalInstructions;

/**
 * @requires PHP 8.1
 */
class LogicalInstructionsTest extends TestCase
{
    private LogicalInstructionsInterface $logicalInstructions;

    public function setUp(): void
    {
        $this->logicalInstructions = $this->getMockForAbstractClass(
            LogicalInstructions::class,
        );
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
        $fieldMock = $this->createFieldMock('name = ?');

        $this->logicalInstructions->and($fieldMock);

        $this->assertEquals('name = ?', $this->logicalInstructions->toSql());
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodOr()
    {
        $fieldMock = $this->createFieldMock('name = ?');

        $this->logicalInstructions->or($fieldMock);

        $this->assertEquals('name = ?', $this->logicalInstructions->toSql());
    }

    public function testShouldStackStatementsNextToEachOther()
    {
        $fieldMock1 = $this->createFieldMock('name = ?');
        $fieldMock2 = $this->createFieldMock('age = ?');
        $fieldMock3 = $this->createFieldMock('birth = ?');

        $this->logicalInstructions->or($fieldMock1);
        $this->logicalInstructions->and($fieldMock2);
        $this->logicalInstructions->or($fieldMock3);

        $this->assertEquals(
            'name = ? AND age = ? OR birth = ?',
            $this->logicalInstructions->toSql(),
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $this->assertEquals('', $this->logicalInstructions->toSql());
    }
}
