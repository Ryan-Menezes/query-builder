<?php

namespace Tests\Sql\Commands\Dml;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Commands\Dml\Insert;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    NullValue,
    RawValue,
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
            'token' => null,
            'created_at' => new RawValue('NOW()'),
        ]);

        $this->assertEquals('INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`, `token`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())', $insert);
        $this->assertEquals([
            [
                new StringValue('John'),
                new NumberValue(18),
                new BooleanValue(true),
                new NumberValue(1.80),
                new NullValue(),
                new RawValue('NOW()'),
            ],
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
                'token' => null,
                'created_at' => new RawValue('NOW()'),
            ],
            [
                'name' => 'Ana',
                'age' => 22,
                'isStudent' => false,
                'height' => 1.60,
                'token' => null,
                'created_at' => new RawValue('NOW()'),
            ],
        ]);

        $this->assertEquals('INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`, `token`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW()), (?, ?, ?, ?, ?, NOW())', $insert);
        $this->assertEquals([
            [
                new StringValue('John'),
                new NumberValue(18),
                new BooleanValue(true),
                new NumberValue(1.80),
                new NullValue(),
                new RawValue('NOW()'),
            ],
            [
                new StringValue('Ana'),
                new NumberValue(22),
                new BooleanValue(false),
                new NumberValue(1.60),
                new NullValue(),
                new RawValue('NOW()'),
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
