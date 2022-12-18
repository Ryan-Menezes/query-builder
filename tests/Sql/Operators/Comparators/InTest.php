<?php

namespace Tests\Sql\Operators\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Values\CollectionValue;
use QueryBuilder\Sql\Operators\Comparators\In;
use InvalidArgumentException;

/**
 * @requires PHP 8.1
 */
class InTest extends TestCase
{
    /**
     * @dataProvider shouldCreateAInOperatorCorrectlyProvider
     */
    public function testShouldCreateAInOperatorCorrectly(
        string $column,
        array $values,
        string $expected,
    ) {
        $in = new In($column, $values);

        $this->assertEquals($column, $in->getColumn());
        $this->assertEquals(new CollectionValue($values), $in->getValue());
        $this->assertEquals($expected, $in);
    }

    public function shouldCreateAInOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10, 20.5], 'any-column IN (?, ?, ?)'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column IN (?, ?)',
            ],
            ['any-column', [true], 'any-column IN (?)'],
            [
                'any-column',
                [ValueFactory::createRawValue('NOW()')],
                'any-column IN (NOW())',
            ],
        ];
    }

    /**
     * @dataProvider shouldCreateANotInOperatorCorrectlyProvider
     */
    public function testShouldCreateANotInOperatorCorrectly(
        string $column,
        array $values,
        string $expected,
    ) {
        $in = new In($column, $values);

        $this->assertEquals($expected, $in->not());
    }

    public function shouldCreateANotInOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10, 20.5], 'any-column NOT IN (?, ?, ?)'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column NOT IN (?, ?)',
            ],
            ['any-column', [true], 'any-column NOT IN (?)'],
            [
                'any-column',
                [ValueFactory::createRawValue('NOW()')],
                'any-column NOT IN (NOW())',
            ],
        ];
    }
    public function testShouldReturnAnErrorIfTheSecondParameterOfTheConstructorIsAnEmptyArray()
    {
        $this->expectException(InvalidArgumentException::class);

        new In('any-column', []);
    }
}
