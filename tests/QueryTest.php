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
}
