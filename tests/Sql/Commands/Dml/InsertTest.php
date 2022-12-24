<?php

namespace Tests\Sql\Commands\Dml;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Commands\Dml\Insert;
use QueryBuilder\Sql\Values\{CollectionValue, RawValue};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentDataException,
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
            'height' => 1.8,
            'token' => null,
            'created_at' => new RawValue('NOW()'),
        ]);

        $this->assertEquals(
            'INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`, `token`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())',
            $insert,
        );
        $this->assertEquals(
            [
                new CollectionValue([
                    'John',
                    18,
                    true,
                    1.8,
                    null,
                    new RawValue('NOW()'),
                ]),
            ],
            $insert->getValues(),
        );
    }

    public function testShouldAcceptAMultiValuedListAndGenerateACorrectInsertQuery()
    {
        $insert = new Insert('any-table', [
            [
                'name' => 'John',
                'age' => 18,
                'isStudent' => true,
                'height' => 1.8,
                'token' => null,
                'created_at' => new RawValue('NOW()'),
            ],
            [
                'name' => 'Ana',
                'age' => 22,
                'isStudent' => false,
                'height' => 1.6,
                'token' => null,
                'created_at' => new RawValue('NOW()'),
            ],
        ]);

        $this->assertEquals(
            'INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`, `token`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW()), (?, ?, ?, ?, ?, NOW())',
            $insert,
        );
        $this->assertEquals(
            [
                new CollectionValue([
                    'John',
                    18,
                    true,
                    1.8,
                    null,
                    new RawValue('NOW()'),
                ]),
                new CollectionValue([
                    'Ana',
                    22,
                    false,
                    1.6,
                    null,
                    new RawValue('NOW()'),
                ]),
            ],
            $insert->getValues(),
        );
    }

    public function testShouldAcceptInsertsWithTheIgnoreStatement()
    {
        $insert = new Insert('any-table', [
            'name' => 'John',
            'age' => 18,
            'isStudent' => true,
            'height' => 1.8,
        ]);

        $this->assertEquals(
            'INSERT IGNORE INTO `any-table` (`name`, `age`, `isStudent`, `height`) VALUES (?, ?, ?, ?)',
            $insert->ignore(),
        );
    }

    public function testShouldThrowAnErrorIfAnInvalidTableNameIsPassed()
    {
        $this->expectException(InvalidArgumentTableNameException::class);
        $this->expectExceptionMessage('The table name must be a string of length greater than zero.');

        new Insert('', [
            'name' => 'John',
        ]);
    }

    public function testShouldThrowAnErrorIfAnInvalidDataIsPassed()
    {
        $this->expectException(InvalidArgumentDataException::class);
        $this->expectExceptionMessage('The array of values ​​must contain at least one value to be inserted.');

        new Insert('any-table', []);
    }
}
