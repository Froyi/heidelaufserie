<?php declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\GenericValueObject\DefaultGenericValueObject;

/**
 * Class TransponderNumber
 * @package     Project\Module\Runner
 */
class TransponderNumber extends DefaultGenericValueObject
{
    /** @var int $transponderNumber */
    protected $transponderNumber;

    /**
     * TransponderNumber constructor.
     *
     * @param int $transponderNumber
     */
    protected function __construct(int $transponderNumber)
    {
        $this->transponderNumber = $transponderNumber;
    }

    /**
     * @param $transponderNumber
     *
     * @return TransponderNumber
     * @throws \InvalidArgumentException
     */
    public static function fromValue($transponderNumber): self
    {
        self::ensureTransponderNumberIsValid($transponderNumber);

        return new self((int)$transponderNumber);
    }

    /**
     * @param $transponderNumber
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureTransponderNumberIsValid($transponderNumber): void
    {
        if ((\is_int($transponderNumber) === false && \is_string($transponderNumber) === false) || (int)$transponderNumber < 0) {
            throw new \InvalidArgumentException('This Number is not valid.');
        }
    }

    /**
     * @return int
     */
    public function getTransponderNumber(): int
    {
        return $this->transponderNumber;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getTransponderNumber();
    }


}