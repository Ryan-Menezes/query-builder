<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
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
    public function testShouldCorrectlyCreateAnSelectCommand()
    {
        $query = new Query('users');

        $this->assertEquals('SELECT * FROM `users`', $query->toSql());
        $this->assertEquals('SELECT * FROM `users`', $query->select()->toSql());
        $this->assertEquals('SELECT id, name FROM `users`', $query->select(['id', 'name'])->toSql());
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
                (new Query('users'))->where('id', '=', 1),
                'SELECT * FROM `users` WHERE id = ?',
                [new NumberValue(1)],
            ],
            [
                (new Query('users'))
                    ->where('id', '=', 1)
                    ->orWhere('name', '=', 'any-name'),
                'SELECT * FROM `users` WHERE id = ? OR name = ?',
                [new NumberValue(1), new StringValue('any-name')],
            ],
            [
                (new Query('users'))
                    ->where('id', '=', 1)
                    ->orWhere('name', '=', 'any-name')
                    ->where('age', '>=', 18),
                'SELECT * FROM `users` WHERE id = ? OR name = ? AND age >= ?',
                [
                    new NumberValue(1),
                    new StringValue('any-name'),
                    new NumberValue(18),
                ],
            ],
            [
                (new Query('users'))->where([
                    ['id', '=', 1],
                    ['name', '=', 'any-name'],
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
                (new Query('users'))->orWhere([
                    ['id', '=', 1],
                    ['name', '=', 'any-name'],
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
                (new Query('users'))
                    ->where([
                        ['id', '=', 1],
                        ['name', '=', 'any-name'],
                        ['age', '>=', 18],
                    ])
                    ->orWhere([
                        ['name', '=', 'other-any-name'],
                        ['email', '=', 'john@mail.com'],
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
                (new Query('users'))->limit(10),
                'SELECT * FROM `users` LIMIT 10',
                [],
            ],
            [
                (new Query('users'))
                    ->where('id', '=', 1)
                    ->orWhere('name', '=', 'any-name')
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
                (new Query('users'))->offset(5),
                'SELECT * FROM `users` OFFSET 5',
                [],
            ],
            [
                (new Query('users'))
                    ->where('id', '=', 1)
                    ->orWhere('name', '=', 'any-name')
                    ->offset(5),
                'SELECT * FROM `users` WHERE id = ? OR name = ? OFFSET 5',
                [new NumberValue(1), new StringValue('any-name')],
            ],
            [
                (new Query('users'))
                    ->where('id', '=', 1)
                    ->orWhere('name', '=', 'any-name')
                    ->offset(5)
                    ->limit(10),
                'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10 OFFSET 5',
                [new NumberValue(1), new StringValue('any-name')],
            ],
        ];
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhereBetween()
    {
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->select(['id'])
            ->where('is_active', '=', 1);

        $queryIn = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->select(['id'])
            ->where('is_active', '=', 1);

        $queryIn = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->select(['id'])
            ->where('is_active', '=', 1);

        $queryIn = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->select(['id'])
            ->where('is_active', '=', 1);

        $queryIn = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
        $query = (new Query('users'))
            ->where('name', '=', 'any-name')
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
}
