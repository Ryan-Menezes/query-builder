<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Dml\Insert;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
};

/**
 * @requires PHP 8.1
 */
class InsertTest extends TestCase
{
    public function testShouldCreateASqlInsertCommandCorrectly()
    {
        $insert = new Insert('any-table', [
            'name' => 'John',
            'age' => 18,
            'isStudent' => true,
            'height' => 1.80,
        ]);

        $this->assertEquals('INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`) VALUES (?, ?, ?, ?)', $insert);
        $this->assertEquals([
            new StringValue('John'),
            new NumberValue(18),
            new BooleanValue(true),
            new NumberValue(1.80),
        ], $insert->getValues());
    }

    public function testShouldAcceptAMultiValuedListAndGenerateACorrectInsertQuery()
    {
        $insert = new Insert('any-table', [
            [
                'name' => 'John',
                'age' => 18,
                'isStudent' => true,
                'height' => 1.80,
            ],
            [
                'name' => 'Ana',
                'age' => 22,
                'isStudent' => false,
                'height' => 1.60,
            ],
        ]);

        $this->assertEquals('INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`) VALUES (?, ?, ?, ?), (?, ?, ?, ?)', $insert);
        $this->assertEquals([
            [
                new StringValue('John'),
                new NumberValue(18),
                new BooleanValue(true),
                new NumberValue(1.80),
            ],
            [
                new StringValue('Ana'),
                new NumberValue(22),
                new BooleanValue(false),
                new NumberValue(1.60),
            ],
        ], $insert->getValues());
    }

    public function testMustAcceptInsertsWithTheIgnoreStatement()
    {
        $insert = new Insert('any-table', [
            'name' => 'John',
            'age' => 18,
            'isStudent' => true,
            'height' => 1.80,
        ]);

        $this->assertEquals('INSERT IGNORE INTO `any-table` (`name`, `age`, `isStudent`, `height`) VALUES (?, ?, ?, ?)', $insert->ignore());
    }
}
