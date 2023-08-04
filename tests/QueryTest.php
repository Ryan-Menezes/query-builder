<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Exceptions\InvalidArgumentArrayException;
use QueryBuilder\Exceptions\InvalidArgumentOperatorException;
use QueryBuilder\Sql\Values\{
    RawValue,
    StringValue,
    NumberValue,
    CollectionValue,
};
use QueryBuilder\Query;

/**
 * @requires PHP 8.1
 */
class QueryTest extends TestCase
{
    /**
     * @dataProvider shouldCorrectlyCreateAnSelectCommandProvider
     */
    public function testShouldCorrectlyCreateAnSelectCommand(
        $query,
        $expectedSql,
    ) {
        $this->assertEquals($expectedSql, $query->toSql());
    }

    public function shouldCorrectlyCreateAnSelectCommandProvider()
    {
        return [
            [Query::table('users'), 'SELECT * FROM `users`'],
            [Query::table('users')->select(), 'SELECT * FROM `users`'],
            [Query::table('users')->select('id'), 'SELECT id FROM `users`'],
            [
                Query::table('users')
                    ->select('id')
                    ->distinct(),
                'SELECT DISTINCT id FROM `users`',
            ],
            [
                Query::table('users')->select(['id', 'name as user_name']),
                'SELECT id, name as user_name FROM `users`',
            ],
            [
                Query::table('users')
                    ->select(['id', 'name as user_name'])
                    ->distinct(),
                'SELECT DISTINCT id, name as user_name FROM `users`',
            ],
        ];
    }

    public function testShouldAddNewColumnsInAQueryInstance()
    {
        $query = Query::table('users')->select(['id', 'name']);

        $this->assertEquals('SELECT id, name FROM `users`', $query->toSql());

        $query->addSelect('email');

        $this->assertEquals(
            'SELECT id, name, email FROM `users`',
            $query->toSql(),
        );

        $query->distinct();
        $query->addSelect(['birth', 'gender']);

        $this->assertEquals(
            'SELECT DISTINCT id, name, email, birth, gender FROM `users`',
            $query->toSql(),
        );
    }

    /**
     * @dataProvider shouldCorrectlyCreateAnSelectCommandWithWhereProvider
     */
    public function testShouldCorrectlyCreateAnSelectCommandWithWhere(
        $query,
        $expectedSql,
        $expectedValues,
    ) {
        $this->assertEquals($expectedSql, $query->toSql());

        $this->assertEquals($expectedValues, $query->getValues());
    }

    public function shouldCorrectlyCreateAnSelectCommandWithWhereProvider()
    {
        return [
            [
                Query::table('users')->where('id', 1),
                'SELECT * FROM `users` WHERE id = ?',
                [new NumberValue(1)],
            ],
            [
                Query::table('users')
                    ->where('id', 1)
                    ->orWhere('name', 'any-name'),
                'SELECT * FROM `users` WHERE id = ? OR name = ?',
                [new NumberValue(1), new StringValue('any-name')],
            ],
            [
                Query::table('users')
                    ->where('id', 1)
                    ->orWhere('name', 'any-name')
                    ->where('age', '>=', 18),
                'SELECT * FROM `users` WHERE id = ? OR name = ? AND age >= ?',
                [
                    new NumberValue(1),
                    new StringValue('any-name'),
                    new NumberValue(18),
                ],
            ],
            [
                Query::table('users')->where([
                    ['id', 1],
                    ['name', 'any-name'],
                    ['age', '>=', 18],
                ]),
                'SELECT * FROM `users` WHERE id = ? AND name = ? AND age >= ?',
                [
                    new NumberValue(1),
                    new StringValue('any-name'),
                    new NumberValue(18),
                ],
            ],
            [
                Query::table('users')->orWhere([
                    ['id', 1],
                    ['name', 'any-name'],
                    ['age', '>=', 18],
                ]),
                'SELECT * FROM `users` WHERE id = ? OR name = ? OR age >= ?',
                [
                    new NumberValue(1),
                    new StringValue('any-name'),
                    new NumberValue(18),
                ],
            ],
            [
                Query::table('users')
                    ->where([
                        ['id', 1],
                        ['name', 'any-name'],
                        ['age', '>=', 18],
                    ])
                    ->orWhere([
                        ['name', 'other-any-name'],
                        ['email', 'john@mail.com'],
                    ]),
                'SELECT * FROM `users` WHERE id = ? AND name = ? AND age >= ? OR name = ? OR email = ?',
                [
                    new NumberValue(1),
                    new StringValue('any-name'),
                    new NumberValue(18),
                    new StringValue('other-any-name'),
                    new StringValue('john@mail.com'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidOperatorIsPassedForWhereMethodProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidOperatorIsPassedForWhereMethod(
        mixed $operator,
    ) {
        $this->expectException(InvalidArgumentOperatorException::class);
        $this->expectExceptionMessage(
            'The operator must be a string of length greater than zero.',
        );

        Query::table('users')->where('id', $operator, 1);
    }

    public function shouldThrowAnErrorIfAnInvalidOperatorIsPassedForWhereMethodProvider()
    {
        return [[null], ['']];
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidOperatorIsPassedForOrWhereMethodProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidOperatorIsPassedForOrWhereMethod(
        mixed $operator,
    ) {
        $this->expectException(InvalidArgumentOperatorException::class);
        $this->expectExceptionMessage(
            'The operator must be a string of length greater than zero.',
        );

        Query::table('users')->orWhere('id', $operator, 1);
    }

    public function shouldThrowAnErrorIfAnInvalidOperatorIsPassedForOrWhereMethodProvider()
    {
        return [[null], ['']];
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheWhereMethodProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheWhereMethod(
        array $fields,
    ) {
        $this->expectException(InvalidArgumentArrayException::class);
        $this->expectExceptionMessage(
            'The first parameter should be of type string or array, and each element of the array must be another array with 2 or 3 elements.',
        );

        Query::table('users')->where($fields);
    }

    public function shouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheWhereMethodProvider()
    {
        return [
            [[[null]]],
            [[['']]],
            [[['any-column']]],
            [[['any-column', '=', 1, 'invalid-param']]],
        ];
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheOrWhereMethodProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheOrWhereMethod(
        array $fields,
    ) {
        $this->expectException(InvalidArgumentArrayException::class);
        $this->expectExceptionMessage(
            'The first parameter should be of type string or array, and each element of the array must be another array with 2 or 3 elements.',
        );

        Query::table('users')->orWhere($fields);
    }

    public function shouldThrowAnErrorIfAnInvalidArrayIsPassedToFirstParamOfTheOrWhereMethodProvider()
    {
        return [
            [[[null]]],
            [[['']]],
            [[['any-column']]],
            [[['any-column', '=', 1, 'invalid-param']]],
        ];
    }

    /**
     * @dataProvider shouldCorrectlyCreateAnSelectCommandWithLimitProvider
     */
    public function testShouldCorrectlyCreateAnSelectCommandWithLimit(
        $query,
        $expectedSql,
        $expectedValues,
    ) {
        $this->assertEquals($expectedSql, $query->toSql());

        $this->assertEquals($expectedValues, $query->getValues());
    }

    public function shouldCorrectlyCreateAnSelectCommandWithLimitProvider()
    {
        return [
            [
                Query::table('users')->limit(10),
                'SELECT * FROM `users` LIMIT 10',
                [],
            ],
            [
                Query::table('users')
                    ->where('id', 1)
                    ->orWhere('name', 'any-name')
                    ->limit(10),
                'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10',
                [new NumberValue(1), new StringValue('any-name')],
            ],
        ];
    }

    /**
     * @dataProvider shouldCorrectlyCreateAnSelectCommandWithOffsetProvider
     */
    public function testShouldCorrectlyCreateAnSelectCommandWithOffset(
        $query,
        $expectedSql,
        $expectedValues,
    ) {
        $this->assertEquals($expectedSql, $query->toSql());

        $this->assertEquals($expectedValues, $query->getValues());
    }

    public function shouldCorrectlyCreateAnSelectCommandWithOffsetProvider()
    {
        return [
            [
                Query::table('users')->offset(5),
                'SELECT * FROM `users` OFFSET 5',
                [],
            ],
            [
                Query::table('users')
                    ->where('id', 1)
                    ->orWhere('name', 'any-name')
                    ->offset(5),
                'SELECT * FROM `users` WHERE id = ? OR name = ? OFFSET 5',
                [new NumberValue(1), new StringValue('any-name')],
            ],
            [
                Query::table('users')
                    ->where('id', 1)
                    ->orWhere('name', 'any-name')
                    ->offset(5)
                    ->limit(10),
                'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10 OFFSET 5',
                [new NumberValue(1), new StringValue('any-name')],
            ],
        ];
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereBetween()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereBetween('id', [10, 30]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id BETWEEN ? AND ?',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([10, 30])],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereBetween()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereBetween('id', [10, 30]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id BETWEEN ? AND ?',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([10, 30])],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereNotBetween()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereNotBetween('id', [10, 30]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id NOT BETWEEN ? AND ?',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([10, 30])],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereNotBetween()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNotBetween('id', [10, 30]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id NOT BETWEEN ? AND ?',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([10, 30])],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereBetweenColumns()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereBetweenColumns('weight', [
                'minimum_allowed_weight',
                'maximum_allowed_weight',
            ]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND weight BETWEEN minimum_allowed_weight AND maximum_allowed_weight',
            $query->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new CollectionValue([
                    new RawValue('minimum_allowed_weight'),
                    new RawValue('maximum_allowed_weight'),
                ]),
            ],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereBetweenColumns()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereBetweenColumns('weight', [
                'minimum_allowed_weight',
                'maximum_allowed_weight',
            ]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR weight BETWEEN minimum_allowed_weight AND maximum_allowed_weight',
            $query->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new CollectionValue([
                    new RawValue('minimum_allowed_weight'),
                    new RawValue('maximum_allowed_weight'),
                ]),
            ],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereNotBetweenColumns()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereNotBetweenColumns('weight', [
                'minimum_allowed_weight',
                'maximum_allowed_weight',
            ]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND weight NOT BETWEEN minimum_allowed_weight AND maximum_allowed_weight',
            $query->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new CollectionValue([
                    new RawValue('minimum_allowed_weight'),
                    new RawValue('maximum_allowed_weight'),
                ]),
            ],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereNotBetweenColumns()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNotBetweenColumns('weight', [
                'minimum_allowed_weight',
                'maximum_allowed_weight',
            ]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR weight NOT BETWEEN minimum_allowed_weight AND maximum_allowed_weight',
            $query->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new CollectionValue([
                    new RawValue('minimum_allowed_weight'),
                    new RawValue('maximum_allowed_weight'),
                ]),
            ],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereIn()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereIn('id', [1, 2, 3]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id IN (?, ?, ?)',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([1, 2, 3])],
            $query->getValues(),
        );
    }

    public function testTheSecondParameterOfTheWhereInMethodShouldAlsoReceiceAQueryInstance()
    {
        $query = Query::table('users')
            ->select(['id'])
            ->where('is_active', 1);

        $queryIn = Query::table('users')
            ->where('name', 'any-name')
            ->whereIn('id', $query);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id IN (SELECT id FROM `users` WHERE is_active = ?)',
            $queryIn->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new NumberValue(1),
                new CollectionValue([
                    new RawValue('SELECT id FROM `users` WHERE is_active = ?'),
                ]),
            ],
            $queryIn->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereIn()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereIn('id', [1, 2, 3]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id IN (?, ?, ?)',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([1, 2, 3])],
            $query->getValues(),
        );
    }

    public function testTheSecondParameterOfTheOrWhereInMethodShouldAlsoReceiceAQueryInstance()
    {
        $query = Query::table('users')
            ->select(['id'])
            ->where('is_active', 1);

        $queryIn = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereIn('id', $query);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id IN (SELECT id FROM `users` WHERE is_active = ?)',
            $queryIn->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new NumberValue(1),
                new CollectionValue([
                    new RawValue('SELECT id FROM `users` WHERE is_active = ?'),
                ]),
            ],
            $queryIn->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereNotIn()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereNotIn('id', [1, 2, 3]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id NOT IN (?, ?, ?)',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([1, 2, 3])],
            $query->getValues(),
        );
    }

    public function testTheSecondParameterOfTheWhereNotInMethodShouldAlsoReceiceAQueryInstance()
    {
        $query = Query::table('users')
            ->select(['id'])
            ->where('is_active', 1);

        $queryIn = Query::table('users')
            ->where('name', 'any-name')
            ->whereNotIn('id', $query);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND id NOT IN (SELECT id FROM `users` WHERE is_active = ?)',
            $queryIn->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new NumberValue(1),
                new CollectionValue([
                    new RawValue('SELECT id FROM `users` WHERE is_active = ?'),
                ]),
            ],
            $queryIn->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereNotIn()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNotIn('id', [1, 2, 3]);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id NOT IN (?, ?, ?)',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new CollectionValue([1, 2, 3])],
            $query->getValues(),
        );
    }

    public function testTheSecondParameterOfTheOrWhereNotInMethodShouldAlsoReceiceAQueryInstance()
    {
        $query = Query::table('users')
            ->select(['id'])
            ->where('is_active', 1);

        $queryIn = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNotIn('id', $query);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR id NOT IN (SELECT id FROM `users` WHERE is_active = ?)',
            $queryIn->toSql(),
        );

        $this->assertEquals(
            [
                new StringValue('any-name'),
                new NumberValue(1),
                new CollectionValue([
                    new RawValue('SELECT id FROM `users` WHERE is_active = ?'),
                ]),
            ],
            $queryIn->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereNull()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereNull('deleted_at');

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND deleted_at IS NULL',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new RawValue('NULL')],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereNotNull()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->whereNotNull('deleted_at');

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? AND deleted_at IS NOT NULL',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new RawValue('NULL')],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereNull()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNull('deleted_at');

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR deleted_at IS NULL',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new RawValue('NULL')],
            $query->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOrWhereNotNull()
    {
        $query = Query::table('users')
            ->where('name', 'any-name')
            ->orWhereNotNull('deleted_at');

        $this->assertEquals(
            'SELECT * FROM `users` WHERE name = ? OR deleted_at IS NOT NULL',
            $query->toSql(),
        );

        $this->assertEquals(
            [new StringValue('any-name'), new RawValue('NULL')],
            $query->getValues(),
        );
    }

    /**
     * @dataProvider shouldCorrectlyCreateAnSelectCommandWithOrderByProvider
     */
    public function testShouldCorrectlyCreateAnSelectCommandWithOrderBy(
        $query,
        $expectedSql,
        $expectedValues,
    ) {
        $this->assertEquals($expectedSql, $query->toSql());

        $this->assertEquals($expectedValues, $query->getValues());
    }

    public function shouldCorrectlyCreateAnSelectCommandWithOrderByProvider()
    {
        return [
            [
                Query::table('users')->orderBy('name'),
                'SELECT * FROM `users` ORDER BY name ASC',
                [],
            ],
            [
                Query::table('users')->orderBy('name', 'DESC'),
                'SELECT * FROM `users` ORDER BY name DESC',
                [],
            ],
            [
                Query::table('users')->inRandomOrder(),
                'SELECT * FROM `users` ORDER BY RAND() ASC',
                [],
            ],
            [
                Query::table('users')
                    ->where('name', 'any-name')
                    ->orderBy('name'),
                'SELECT * FROM `users` WHERE name = ? ORDER BY name ASC',
                [new StringValue('any-name')],
            ],
            [
                Query::table('users')
                    ->where('name', 'any-name')
                    ->orderBy('name', 'DESC'),
                'SELECT * FROM `users` WHERE name = ? ORDER BY name DESC',
                [new StringValue('any-name')],
            ],
            [
                Query::table('users')
                    ->where('name', 'any-name')
                    ->inRandomOrder('name', 'DESC'),
                'SELECT * FROM `users` WHERE name = ? ORDER BY RAND() ASC',
                [new StringValue('any-name')],
            ],
            [
                Query::table('users')
                    ->orderBy('name')
                    ->offset(5)
                    ->limit(10),
                'SELECT * FROM `users` ORDER BY name ASC LIMIT 10 OFFSET 5',
                [],
            ],
            [
                Query::table('users')
                    ->orderBy('name', 'DESC')
                    ->offset(5)
                    ->limit(10),
                'SELECT * FROM `users` ORDER BY name DESC LIMIT 10 OFFSET 5',
                [],
            ],
            [
                Query::table('users')
                    ->inRandomOrder()
                    ->offset(5)
                    ->limit(10),
                'SELECT * FROM `users` ORDER BY RAND() ASC LIMIT 10 OFFSET 5',
                [],
            ],
        ];
    }

    public function testShouldReorderAnSelectCommandWithOrderBy()
    {
        $query = Query::table('users')->orderBy('name');

        $this->assertEquals(
            'SELECT * FROM `users` ORDER BY name ASC',
            $query->toSql(),
        );

        $query = $query->reorder();

        $this->assertEquals('SELECT * FROM `users`', $query->toSql());
    }
}
