<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Operators\Logical\Where;
use QueryBuilder\Interfaces\{LogicalInstructionsInterface, FieldInterface};

/**
 * @requires PHP 8.1
 */
class WhereTest extends TestCase
{
    private LogicalInstructionsInterface $logicalInstructions;

    public function setUp(): void
    {
        $this->logicalInstructions = new Where();
    }

    private function createFieldMock(string $toStringReturn): FieldInterface
    {
        $fieldMock = $this->createMock(FieldInterface::class);
        $fieldMock->method('__toString')->willReturn($toStringReturn);

        return $fieldMock;
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodAnd()
    {
        $fieldMock = $this->createFieldMock('name = ?');

        $this->logicalInstructions->and($fieldMock);

        $this->assertEquals('WHERE name = ?', $this->logicalInstructions);
    }

    public function testShouldNotAddALogicalStatementAtTheBeginningIfTheFirstMethodToBeCalledIsMethodOr()
    {
        $fieldMock = $this->createFieldMock('name = ?');

        $this->logicalInstructions->or($fieldMock);

        $this->assertEquals('WHERE name = ?', $this->logicalInstructions);
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
            'WHERE name = ? AND age = ? OR birth = ?',
            $this->logicalInstructions,
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $this->assertEquals('', $this->logicalInstructions);
    }
}
