<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Dml\Insert;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
};

class InsertTest extends TestCase
{
    public function testShouldCreateASqlInsertCommandCorrectly()
    {
        $insert = new Insert('any-table', [
            'name' => 'John',
            'age' => 18,
            'isStudent' => true,
            'height' => 1.80,
        ]);

        $this->assertEquals('INSERT INTO `any-table` (`name`, `age`, `isStudent`, `height`) VALUES (?, ?, ?, ?)', $insert);
    }

    public function testShouldAssociateEachValuePassedToItsGivenClass()
    {
        $insert = new Insert('any-table', [
            'name' => 'John',
            'age' => 18,
            'isStudent' => true,
            'height' => 1.80,
        ]);

        $this->assertEquals([
            new StringValue('John'),
            new NumberValue(18),
            new BooleanValue(true),
            new NumberValue(1.80),
        ], $insert->getValues());
    }
}
