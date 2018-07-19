<?php
declare(strict_types=1);

namespace Project\Module\Tracking;

/**
 * Class RoundTime
 * @package Project\Module\Tracking
 */
class RoundTime
{
    /** @var int $roundtime */
    protected $roundtime;

    /**
     * RoundTime constructor.
     *
     * @param int $roundtime
     */
    protected function __construct(int $roundtime)
    {
        $this->roundtime = $roundtime;
    }

    /**
     * @param $value
     *
     * @return RoundTime
     * @throws \InvalidArgumentException
     */
    public static function fromValue($value): self
    {
        self::ensureValueIsValid($value);

        return new self(self::convertValue($value));
    }

    /**
     * @param $value
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureValueIsValid($value): void
    {
        if (\is_int($value) === false && \is_string($value) === false) {
            throw new \InvalidArgumentException('The value is neither an int nor an string: ' . $value);
        }

        if ($value < 0) {
            throw new \InvalidArgumentException('This value is under 0: ' . $value);
        }
    }

    /**
     * @param $value
     *
     * @return int
     */
    protected static function convertValue($value): int
    {
        return (int)$value;
    }
}