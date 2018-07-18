<?php
declare(strict_types=1);

namespace Project\Module\GenericValueObject;

/**
 * Class Distance
 * @package Project\Module\GenericValueObject
 */
class Distance
{
    /** @var int */
    protected $distance;

    /**
     * Distance constructor.
     *
     * @param int $distance
     */
    protected function __construct(int $distance)
    {
        $this->distance = $distance;
    }

    /**
     * @param $value
     * @param bool|null $isMeter
     *
     * @return Distance
     * @throws \InvalidArgumentException
     */
    public static function fromValue($value, ?bool $isMeter = true): self
    {
        self::ensureValueIsValid($value);

        return new self(self::convertValue($value, $isMeter));
    }

    /**
     * @param $value
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureValueIsValid($value): void
    {
        if (\is_int($value) === false && \is_string($value) === false) {
            throw new \InvalidArgumentException('This value is neither an int nor a string.');
        }
    }

    /**
     * @param $value
     * @param bool $isMeter
     *
     * @return int
     */
    protected static function convertValue($value, bool $isMeter): int
    {
        $value = (int)$value;

        if ($isMeter === false) {
            $value *= 1000;
        }

        return $value;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getDistance();
    }
}