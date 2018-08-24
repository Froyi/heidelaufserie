<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Class Test
 */
class NameTest extends TestCase
{
    /**
     * @dataProvider testNamesValidProvider
     */
    public function testNameIsValid($name)
    {
        $this->assertSame($name, \Project\Module\GenericValueObject\Name::fromString($name)->getName());
    }

    /**
     * @dataProvider testNamesTypeErrorProvider
     *
     * @expectedException TypeError
     */
    public function testNamesTypeError($name)
    {
        \Project\Module\GenericValueObject\Name::fromString($name);
    }

    /**
     * @dataProvider testNamesInvalidProvider
     *
     * @expectedException InvalidArgumentException
     */
    public function testNamesInvlidArgument($name)
    {
        \Project\Module\GenericValueObject\Name::fromString($name);
    }

    public function testNamesValidProvider()
    {
        return [
            ['Peter'],
            ['Al'],
            ['Hans-Peter'],
            ['Hans Peter'],
        ];
    }

    public function testNamesTypeErrorProvider()
    {
        return [
            [2],
            [true],
            [[]],
            [new stdClass()],
        ];
    }

    public function testNamesInvalidProvider()
    {
        return [
            ['a'],
            ['3948274sadiasdf']
        ];
    }
}
