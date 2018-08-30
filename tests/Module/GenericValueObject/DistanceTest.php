<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Class Test
 */
class DistanceTest extends TestCase
{
    /**
     * @dataProvider distanceValidProvider
     */
    public function testDistanceIsValid($distance, $isMeter, $result)
    {
        $this->assertSame($result, \Project\Module\GenericValueObject\Distance::fromValue($distance, $isMeter)->getDistance());
    }

    /**
     * @dataProvider distanceTypeErrorProvider
     *
     * @expectedException TypeError
     */
    public function testDistanceTypeError($distance)
    {
        \Project\Module\GenericValueObject\Distance::fromValue($distance);
    }

    /**
     * @dataProvider distanceInvalidProvider
     *
     * @expectedException InvalidArgumentException
     */
    public function testDistanceInvlidArgument($distance)
    {
        \Project\Module\GenericValueObject\Distance::fromValue($distance);
    }

    public function distanceValidProvider()
    {
        return [
            [3, true, 3],
            ['3', true, 3],
            ['0.3', true, 0],
            [0, true, 0],
            ['0.5', true, 1],
            [1207876, true, 1207876],
            [4, false, 4000],
            ['4', false, 4000],
            ['0.4', false, 400],
        ];
    }

    public function distanceTypeErrorProvider()
    {
        return [
            [2],
            [true],
            [[]],
            [new stdClass()],
        ];
    }

    public function distanceInvalidProvider()
    {
        return [
            ['a'],
            ['3948274sadiasdf']
        ];
    }
}
