<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
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
        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ?',
            (new Query('users'))->where('id', '=', 1),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ?',
            (new Query('users'))
                ->where('id', '=', 1)
                ->orWhere('name', '=', 'any-name'),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? AND age >= ?',
            (new Query('users'))
                ->where('id', '=', 1)
                ->orWhere('name', '=', 'any-name')
                ->where('age', '>=', '18'),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithLimit()
    {
        $this->assertEquals(
            'SELECT * FROM `users` LIMIT 10',
            (new Query('users'))->limit(10),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10',
            (new Query('users'))
                ->where('id', '=', 1)
                ->orWhere('name', '=', 'any-name')
                ->limit(10),
        );
    }

    public function testShouldCorrectlyCreateAnSelectCommandWithOffset()
    {
        $this->assertEquals(
            'SELECT * FROM `users` OFFSET 5',
            (new Query('users'))->offset(5),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? OFFSET 5',
            (new Query('users'))
                ->where('id', '=', 1)
                ->orWhere('name', '=', 'any-name')
                ->offset(5),
        );

        $this->assertEquals(
            'SELECT * FROM `users` WHERE id = ? OR name = ? LIMIT 10 OFFSET 5',
            (new Query('users'))
                ->where('id', '=', 1)
                ->orWhere('name', '=', 'any-name')
                ->offset(5)
                ->limit(10),
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
    }
}
