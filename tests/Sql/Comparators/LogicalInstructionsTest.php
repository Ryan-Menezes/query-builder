<?php

namespace Tests\Sql\Comparators;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Sql\Column;
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
            ->and(FieldFactory::createField('name', 'LIKE', '%any-name%'))
            ->or(FieldFactory::createField('salary', '>', 800))
            ->and(FieldFactory::createField('isTeacher', '=', true))
            ->or(FieldFactory::createField('created_at', '=', null))
            ->or(FieldFactory::createFieldWithRawValue('updated_at', '<=', 'NOW()'));

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
            ->and(new Between(new Column('age'), [10, 20]))
            ->or(new Between(new Column('age'), [10, new Column('any-column')]))
            ->and(
                (new Between(new Column('age'), [10, 20]))->not()
            );

        $this->assertEquals('`age` BETWEEN 10 AND 20 OR `age` BETWEEN 10 AND `any-column` AND `age` NOT BETWEEN 10 AND 20', $logicalInstructions);
    }

    public function testShouldSupportInOperator()
    {
        $logicalInstructions = new LogicalInstructionsMock();
        $logicalInstructions
            ->and(new In(new Column('age'), [5, 10, 20.5]))
            ->or(new In(new Column('birthday'), ['2000-01-01', new RawValue('NOW()')]))
            ->and(
                (new In(new Column('age'), [10, 20]))->not()
            );

        $this->assertEquals('`age` IN (?, ?, ?) OR `birthday` IN (?, NOW()) AND `age` NOT IN (?, ?)', $logicalInstructions);
    }
}
