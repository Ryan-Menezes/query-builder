<?php

namespace Tests\Sql\Commands\Dql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Commands\Dql\Select;
use QueryBuilder\Sql\Values\{RawValue, StringValue};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentColumnNameException,
};

/**
 * @requires PHP 8.1
 */
class SelectTest extends TestCase
{
    private function makeSut(
        string $tableName,
        array $columns = ['*'],
        array $values = [],
    ): Select {
        return new Select($tableName, $columns, $values);
    }

    public function testShouldCreateASqlSelectCommandCorrectly()
    {
        $sut = $this->makeSut('any-table');

        $this->assertEquals('SELECT * FROM `any-table`', $sut->toSql());
        $this->assertEquals(['*'], $sut->getColumns());
    }

    public function testShouldCreateASqlSelectCommandWithColumnsCorrectly()
    {
        $sut = $this->makeSut('any-table', [
            'name',
            'birth',
            'email AS user_email',
        ]);

        $this->assertEquals(
            'SELECT name, birth, email AS user_email FROM `any-table`',
            $sut->toSql(),
        );
        $this->assertEquals(
            [
                new RawValue('name'),
                new RawValue('birth'),
                new RawValue('email AS user_email'),
            ],
            $sut->getColumns(),
        );
    }

    public function testShouldCreateASqlSelectCommandWithDistinctStatementCorrectly()
    {
        $sut = $this->makeSut('any-table', ['name', 'birth']);

        $this->assertEquals(
            'SELECT DISTINCT name, birth FROM `any-table`',
            $sut->distinct()->toSql(),
        );
        $this->assertEquals(
            [new RawValue('name'), new RawValue('birth')],
            $sut->getColumns(),
        );
    }

    public function testShouldCreateASqlSelectCommandWithValues()
    {
        $sut = $this->makeSut('any-table', ['CONTACT(name, ?)'], ['any-value']);

        $this->assertEquals(
            'SELECT CONTACT(name, ?) FROM `any-table`',
            $sut->toSql(),
        );
        $this->assertEquals(
            [new RawValue('CONTACT(name, ?)')],
            $sut->getColumns(),
        );
        $this->assertEquals([new StringValue('any-value')], $sut->getValues());
    }

    public function testShouldThrowAnErrorIfAnInvalidTableNameIsPassed()
    {
        $this->expectException(InvalidArgumentTableNameException::class);
        $this->expectExceptionMessage(
            'The table name must be a string of length greater than zero.',
        );

        $invalidTableName = '';
        $this->makeSut($invalidTableName);
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

        $this->makeSut('any-table', [$column]);
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
