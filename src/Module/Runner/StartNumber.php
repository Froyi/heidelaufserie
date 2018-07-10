<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\GenericValueObject\DefaultGenericValueObject;

/**
 * Class StartNumber
 * @package     Project\Module\Runner
 */
class StartNumber extends DefaultGenericValueObject
{
    /** @var int $startNumber */
    protected $startNumber;

    /**
     * StartNumber constructor.
     *
     * @param int $startNumber
     */
    protected function __construct(int $startNumber)
    {
        $this->startNumber = $startNumber;
    }

    /**
     * @param int $startNumber
     *
     * @return StartNumber
     * @throws \InvalidArgumentException
     */
    public static function fromValue(int $startNumber): self
    {
        self::ensureStartNumberIsValid($startNumber);

        return new self($startNumber);
    }

    /**
     * @param int $startNumber
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureStartNumberIsValid(int $startNumber): void
    {
        if ($startNumber < 0) {
            throw new \InvalidArgumentException('This Number is not valid: ' . $startNumber);
        }
    }

    /**
     * @return int
     */
    public function getStartNumber(): int
    {
        return $this->startNumber;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getStartNumber();
    }


}