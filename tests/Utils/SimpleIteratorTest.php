<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Utils\SimpleIterator;
use StdClass;

class SimpleIteratorTest extends TestCase
{
    private array $items;
    private SimpleIterator $simpleIterator;

    public function setUp(): void
    {
        $this->items = [1, 'any-value', [], 12.5, new StdClass];

        $this->simpleIterator = $this->getMockForAbstractClass(SimpleIterator::class, [
            $this->items
        ]);
    }

    public function testShouldIterateWithAForeachLoop()
    {
        foreach($this->simpleIterator as $key => $value) {
            $this->assertEquals($key, $this->simpleIterator->key());
            $this->assertEquals($value, $this->simpleIterator->current());
        }
    }

    public function testShouldSerializeTheListOfItems()
    {
        $actual = $this->simpleIterator->serialize();
        $expected = serialize($this->items);

        $this->assertEquals($expected, $actual);
    }

    public function testShouldUnserializeTheListOfItems()
    {
        $serialize = $this->simpleIterator->serialize();
        $this->simpleIterator->unserialize($serialize);

        $actual = iterator_to_array($this->simpleIterator);
        $expected = $this->items;

        $this->assertEquals($expected, $actual);
    }
}
