<?php

namespace Tests\Sql\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\Comparators\LogicalInstructions;
use QueryBuilder\Sql\Comparators\Operators\{
    Between,
    In,
};

class LogicalInstructionsMock extends LogicalInstructions {}

/**
 * @requires PHP 8.1
 */
class LogicalInstructionsTest extends TestCase
{
    public function testShouldCreateLogicalAndComparisonOperators()
    {
        $logicalInstructions = new LogicalInstructionsMock();
        $logicalInstructions
            ->and(new Field('name', 'LIKE', '%any-name%'))
            ->or(new Field('salary', '>', 800))
            ->and(new Field('isTeacher', '=', true))
            ->or(new Field('created_at', '=', null))
            ->or(new Field('updated_at', '<=', new RawValue('NOW()')));

        $this->assertEquals('`name` LIKE ? OR `salary` > ? AND `isTeacher` = ? OR `created_at` = ? OR `updated_at` <= NOW()', $logicalInstructions);
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $logicalInstructions = new LogicalInstructionsMock();

        $this->assertEquals('', $logicalInstructions);
    }

    public function testShouldSupportBetweenOperator()
    {
        $logicalInstructions = new LogicalInstructionsMock();
        $logicalInstructions
            ->and(new Between('age', [10, 20]))
            ->or(new Between('age', [10, new Column('any-column')]))
            ->and(
                (new Between('age', [10, 20]))->not()
            );

        $this->assertEquals('`age` BETWEEN 10 AND 20 OR `age` BETWEEN 10 AND `any-column` AND `age` NOT BETWEEN 10 AND 20', $logicalInstructions);
    }

    public function testShouldSupportInOperator()
    {
        $logicalInstructions = new LogicalInstructionsMock();
        $logicalInstructions
            ->and(new In('age', [5, 10, 20.5]))
            ->or(new In('birthday', ['2000-01-01', new RawValue('NOW()')]))
            ->and(
                (new In('age', [10, 20]))->not()
            );

        $this->assertEquals('`age` IN (5, 10, 20.5) OR `birthday` IN (\'2000-01-01\', NOW()) AND `age` NOT IN (10, 20)', $logicalInstructions);
    }
}
