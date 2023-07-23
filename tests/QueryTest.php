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

        $this->assertEquals('SELECT * FROM `users`', $query);
        $this->assertEquals('SELECT * FROM `users`', $query->select());
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithWhere()
    {
        $queryOne = (new Query('users'))->where('id', '=', 1);

        $queryTwo = (new Query('users'))
            ->where('id', '=', 1)
            ->orWhere('name', '=', 'any-name');

        $queryThree = (new Query('users'))
            ->where('id', '=', 1)
            ->orWhere('name', '=', 'any-name')
            ->where('age', '>=', 18);

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ?',
            $queryOne->toSql(),
        );

        $this->assertEquals([new NumberValue(1)], $queryOne->getValues());

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ?',
            $queryTwo->toSql(),
        );

        $this->assertEquals(
            [new NumberValue(1), new StringValue('any-name')],
            $queryTwo->getValues(),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? AND age >= ?',
            $queryThree->toSql(),
        );

        $this->assertEquals(
            [
                new NumberValue(1),
                new StringValue('any-name'),
                new NumberValue(18),
            ],
            $queryThree->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithLimit()
    {
        $query = (new Query('users'))->limit(10);
        $queryTwo = (new Query('users'))
            ->where('id', '=', 1)
            ->orWhere('name', '=', 'any-name')
            ->limit(10);

        $this->assertEquals('SELECT * FROM `users` LIMIT 10', $query->toSql());

        $this->assertEquals([], $query->getValues());

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10',
            $queryTwo->toSql(),
        );

        $this->assertEquals(
            [new NumberValue(1), new StringValue('any-name')],
            $queryTwo->getValues(),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOffset()
    {
        $query = (new Query('users'))->offset(5);

        $queryTwo = (new Query('users'))
            ->where('id', '=', 1)
            ->orWhere('name', '=', 'any-name')
            ->offset(5);

        $queryThree = (new Query('users'))
            ->where('id', '=', 1)
            ->orWhere('name', '=', 'any-name')
            ->offset(5)
            ->limit(10);

        $this->assertEquals('SELECT * FROM `users` OFFSET 5', $query->toSql());

        $this->assertEquals([], $query->getValues());

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? OFFSET 5',
            $queryTwo->toSql(),
        );

        $this->assertEquals(
            [new NumberValue(1), new StringValue('any-name')],
            $queryTwo->getValues(),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10 OFFSET 5',
            $queryThree->toSql(),
        );

        $this->assertEquals(
            [new NumberValue(1), new StringValue('any-name')],
            $queryThree->getValues(),
        );
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
}
