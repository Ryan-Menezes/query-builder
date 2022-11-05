<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Fields;

use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    RawValue,
};

class FieldsTest extends TestCase
{
    public function testShouldBeAbleToFormatAndReturnAStringWithTheFieldsAndTheirValues()
    {
        $fields = new Fields([
            'name' => 'John',
            'age' => 27,
            'isStudent' => true,
            'money' => 12.5,
            'tax' => new RawValue('money * 0.3'),
        ]);

        $this->assertEquals(5, $fields->count());
        $this->assertEquals('`name` = \'John\', `age` = 27, `isStudent` = 1, `money` = 12.5, `tax` = money * 0.3', $fields);
        $this->assertEquals([
            '`name`' => ['=', new StringValue('John')],
            '`age`' => ['=', new NumberValue(27)],
            '`isStudent`' => ['=', new BooleanValue(true)],
            '`money`' => ['=', new NumberValue(12.5)],
            '`tax`' => ['=', new RawValue('money * 0.3')],
        ], $fields->all());
    }

    public function testShouldAcceptValuesWithAssignmentAndComparisonOperators()
    {
        $fields = new Fields([
            'name' => 'John',
            'age' => ['>=', 27],
            'isStudent' => true,
            'money' => ['<=', 12.5],
            'tax' => ['IS', new RawValue('NULL')],
        ]);

        $this->assertEquals(5, $fields->count());
        $this->assertEquals('`name` = \'John\', `age` >= 27, `isStudent` = 1, `money` <= 12.5, `tax` IS NULL', $fields);
        $this->assertEquals([
            '`name`' => ['=', new StringValue('John')],
            '`age`' => ['>=', new NumberValue(27)],
            '`isStudent`' => ['=', new BooleanValue(true)],
            '`money`' => ['<=', new NumberValue(12.5)],
            '`tax`' => ['IS', new RawValue('NULL')],
        ], $fields->all());
    }
}
