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
    }
}
