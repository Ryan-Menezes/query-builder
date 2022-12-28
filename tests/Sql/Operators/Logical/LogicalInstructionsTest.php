<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Operators\Logical\LogicalInstructions;
use QueryBuilder\Sql\Operators\Comparators\{Between, In};

/**
 * @requires PHP 8.1
 */
class LogicalInstructionsTest extends TestCase
{
    public function testShouldCreateLogicalAndComparisonOperators()
    {
        $logicalInstructions = $this->getMockForAbstractClass(
            LogicalInstructions::class,
        );
        $logicalInstructions
            ->and(FieldFactory::createField('name', 'LIKE', '%any-name%'))
            ->or(FieldFactory::createField('salary', '>', 800))
            ->and(FieldFactory::createField('isTeacher', '=', true))
            ->or(FieldFactory::createField('created_at', '=', null))
            ->or(
                FieldFactory::createFieldWithRawValue(
                    'updated_at',
                    '<=',
                    'NOW()',
                ),
            );

        $this->assertEquals(
            'name LIKE ? OR salary > ? AND isTeacher = ? OR created_at = ? OR updated_at <= NOW()',
            $logicalInstructions,
        );
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $logicalInstructions = $this->getMockForAbstractClass(
            LogicalInstructions::class,
        );

        $this->assertEquals('', $logicalInstructions);
    }

    public function testShouldSupportBetweenOperator()
    {
        $logicalInstructions = $this->getMockForAbstractClass(
            LogicalInstructions::class,
        );
        $logicalInstructions
            ->and(new Between('age', [10, 20]))
            ->or(
                new Between('age', [
                    10,
                    ValueFactory::createRawValue('any-column'),
                ]),
            )
            ->and((new Between('age', [10, 20]))->not());

        $this->assertEquals(
            'age BETWEEN ? AND ? OR age BETWEEN ? AND any-column AND age NOT BETWEEN ? AND ?',
            $logicalInstructions,
        );
    }

    public function testShouldSupportInOperator()
    {
        $logicalInstructions = $this->getMockForAbstractClass(
            LogicalInstructions::class,
        );
        $logicalInstructions
            ->and(new In('age', [5, 10, 20.5]))
            ->or(
                new In('birthday', [
                    '2000-01-01',
                    ValueFactory::createRawValue('NOW()'),
                ]),
            )
            ->and((new In('age', [10, 20]))->not());

        $this->assertEquals(
            'age IN (?, ?, ?) OR birthday IN (?, NOW()) AND age NOT IN (?, ?)',
            $logicalInstructions,
        );
    }
}
