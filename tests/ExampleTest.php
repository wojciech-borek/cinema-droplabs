<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testStringContains(): void
    {
        $haystack = 'Hello World';
        $this->assertStringContainsString('World', $haystack);
    }

    public function testAddition(): void
    {
        $result = 2 + 2;
        $this->assertSame(4, $result);
    }

    public function testArrayHasKey(): void
    {
        $array = ['name' => 'Cinema', 'type' => 'API'];
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('Cinema', $array['name']);
    }
}
