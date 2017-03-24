<?php namespace _20TRIES\Test;

use _20TRIES\Arr\Arr;
use PHPUnit_Framework_TestCase;

class FirstTest extends PHPUnit_Framework_TestCase
{
    public function test_first_element_is_returned_when_integer_keys()
    {
        $this->assertEquals('foo', Arr::first(['foo', 'bar', 'baz']));
    }

    public function test_first_element_is_returned_when_string_keys()
    {
        $this->assertEquals(1, Arr::first(['foo' => 1, 'bar' => 2, 'baz' => 3]));
    }

    public function test_callback_parameters()
    {
        $passed_key = null;
        $passed_item = null;
        Arr::first(['foo', 'bar', 'baz'], function ($item, $key) use (&$passed_key, &$passed_item) {
            $passed_key = $key;
            $passed_item = $item;
            return true;
        });
        $this->assertEquals($passed_key, 0);
        $this->assertEquals($passed_item, 'foo');
    }

    public function test_first_matching_element_is_returned_when_callback_is_provided()
    {
        $this->assertEquals('bar', Arr::first(['foo', 'bar', 'baz'], function ($item, $key) use (&$passed_key, &$passed_item) {
            return substr($item, 0, 1) === 'b';
        }));
    }

    public function test_that_null_is_returned_when_callback_does_not_match_any_elements()
    {
        $this->assertNull(Arr::first(['foo', 'bar', 'baz'], function ($item, $key) use (&$passed_key, &$passed_item) {
            return substr($item, 0, 1) === 'z';
        }));
    }

    /**
     * @test
     */
    public function defaultValueIsNull()
    {
        $this->assertNull(Arr::first([]));
    }

    /**
     * @test
     */
    public function defaultValueCanBeSet()
    {
        $this->assertSame(5, Arr::first([], null, 5));
    }
}