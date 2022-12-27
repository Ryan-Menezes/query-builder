<?php

namespace Tests\Sql\Commands\Dql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Commands\Dql\Select;
use QueryBuilder\Sql\Values\{RawValue};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentColumnNameException,
};

/**
 * @requires PHP 8.1
 */
class SelectTest extends TestCase
{
    public function testShouldCreateASqlSelectCommandCorrectly()
    {
        $select = new Select('any-table');

        $this->assertEquals('SELECT * FROM `any-table`', $select);
        $this->assertEquals(['*'], $select->getColumns());
    }

    public function testShouldCreateASqlSelectCommandWithColumnsCorrectly()
    {
        $select = new Select('any-table', [
            'name',
            'birth',
            'email AS user_email',
        ]);

        $this->assertEquals(
            'SELECT name, birth, email AS user_email FROM `any-table`',
            $select,
        );
        $this->assertEquals(
            [
                new RawValue('name'),
                new RawValue('birth'),
                new RawValue('email AS user_email'),
            ],
            $select->getColumns(),
        );
    }

    public function testShouldCreateASqlSelectCommandWithDistinctStatementCorrectly()
    {
        $select = new Select('any-table', ['name', 'birth']);

        $this->assertEquals(
            'SELECT DISTINCT name, birth FROM `any-table`',
            $select->distinct(),
        );
        $this->assertEquals(
            [new RawValue('name'), new RawValue('birth')],
            $select->getColumns(),
        );
    }

    public function testShouldThrowAnErrorIfAnInvalidTableNameIsPassed()
    {
        $this->expectException(InvalidArgumentTableNameException::class);
        $this->expectExceptionMessage(
            'The table name must be a string of length greater than zero.',
        );

        $invalidTableName = '';
        new Select($invalidTableName);
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidColumnsIsPassedProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidColumnsIsPassed(
        mixed $column,
    ) {
        $this->expectException(InvalidArgumentColumnNameException::class);
        $this->expectExceptionMessage(
            'The column name must be a string of length greater than zero.',
        );

        new Select('any-table', [$column]);
    }

    public function shouldThrowAnErrorIfAnInvalidColumnsIsPassedProvider()
    {
        return [
            [''], // Empty String
            [1], // Integer Number
            [1.5], // Float Number
            [new \StdClass()], // Object
            [function () {}], // Callable
            [new RawValue('any-value')], // Classes that implement the Stringable interface
        ];
    }
}
