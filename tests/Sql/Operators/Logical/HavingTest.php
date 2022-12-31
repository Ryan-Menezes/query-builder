<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Operators\Logical\Having;
use QueryBuilder\Interfaces\{LogicalInstructionsInterface, FieldInterface};

/**
 * @requires PHP 8.1
 */
class HavingTest extends TestCase
{
    private LogicalInstructionsInterface $logicalInstructions;

    public function setUp(): void
    {
        $this->logicalInstructions = new Having();
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

        $this->assertEquals(
            'HAVING name = ?',
            $this->logicalInstructions->toSql(),
        );
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodOr()
    {
        $fieldMock = $this->createFieldMock('name = ?');

        $this->logicalInstructions->or($fieldMock);

        $this->assertEquals(
            'HAVING name = ?',
            $this->logicalInstructions->toSql(),
        );
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
            'HAVING name = ? AND age = ? OR birth = ?',
            $this->logicalInstructions->toSql(),
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $this->assertEquals('', $this->logicalInstructions->toSql());
    }
}
