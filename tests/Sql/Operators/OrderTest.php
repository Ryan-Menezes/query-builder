<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\StringValue;
use QueryBuilder\Sql\Operators\Order;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnsException;

/**
 * @requires PHP 8.1
 */
class OrderTest extends TestCase
{
    public function makeSut(array $columns): Order
    {
        $sqlMock = $this->createMock(SqlWithValuesInterface::class);
        $sqlMock
            ->method('__toString')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table`');
        $sqlMock
            ->method('toSql')
            ->willReturn('SELECT CONTACT(name, ?) FROM `any-table`');
        $sqlMock
            ->method('getValues')
            ->willReturn([new StringValue('any-value-sql')]);

        return new Order($sqlMock, $columns);
    }

    /**
     * @dataProvider shouldCreateAnOrderStatementCorrectlyProvider
     */
    public function testShouldCreateAnOrderStatementCorrectly(
        array $columns,
        string $expected,
    ) {
        $sut = $this->makeSut($columns);

        $this->assertEquals($expected, $sut->toSql());
    }

    public function shouldCreateAnOrderStatementCorrectlyProvider()
    {
        return [
            [
                [
                    'any-column' => 'DESC',
                ],
                'SELECT CONTACT(name, ?) FROM `any-table` ORDER BY any-column DESC',
            ],
            [
                [
                    'any-column' => 'DESC',
                    'any-column2' => 'ASC',
                ],
                'SELECT CONTACT(name, ?) FROM `any-table` ORDER BY any-column DESC, any-column2 ASC',
            ],
            [
                [
                    'any-column' => 'asc',
                    'any-column2' => 'desc',
                ],
                'SELECT CONTACT(name, ?) FROM `any-table` ORDER BY any-column ASC, any-column2 DESC',
            ],
        ];
    }

    public function testShouldThrowAnErrorIfItIsAnEmptyArrayToTheConstructor()
    {
        $this->expectException(InvalidArgumentColumnsException::class);
        $this->expectExceptionMessage('Column array cannot be empty.');

        $this->makeSut([]);
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnyColumnNameInTheArrayIsInvalidProvider
     */
    public function testShouldThrowAnErrorIfAnyColumnNameInTheArrayIsInvalid(
        array $columns,
    ) {
        $this->expectException(InvalidArgumentColumnsException::class);
        $this->expectExceptionMessage(
            'Column name must be a string of length greater than 1.',
        );

        $this->makeSut($columns);
    }

    public function shouldThrowAnErrorIfAnyColumnNameInTheArrayIsInvalidProvider()
    {
        return [[['' => 'ASC']], [[1 => 'ASC']], [['ASC']]];
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnySortStatementInTheArrayIsInvalidProvider
     */
    public function testShouldThrowAnErrorIfAnySortStatementInTheArrayIsInvalid(
        array $columns,
    ) {
        $this->expectException(InvalidArgumentColumnsException::class);
        $this->expectExceptionMessage(
            'The sort statement must be a string and its value must be "ASC" or "DESC".',
        );

        $this->makeSut($columns);
    }

    public function shouldThrowAnErrorIfAnySortStatementInTheArrayIsInvalidProvider()
    {
        return [
            [['any-column' => '']],
            [['any-column' => 1]],
            [['any-column' => 'invalid-sort']],
        ];
    }
}
