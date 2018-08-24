<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Class Test
 */
class NameTest extends TestCase
{
    public function testFirst()
    {
        $a = 1;
        $b = 1;

        $this->assertSame($a, $b);
    }
}
