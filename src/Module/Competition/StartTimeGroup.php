<?php
declare(strict_types=1);

namespace Project\Module\Competition;

/**
 * Class StartTimeGroup
 * @package Project\Module\Competition
 */
class StartTimeGroup
{
    /** @var int $startTimeGroup */
    protected $startTimeGroup;

    /**
     * StartTimeGroup constructor.
     *
     * @param int $startTimeGroup
     */
    protected function __construct(int $startTimeGroup)
    {
        $this->startTimeGroup = $startTimeGroup;
    }

    public static function fromValue($startTimeGroup): self
    {
        self::ensureStartTimeGroupIsValid($startTimeGroup);

        return new self((int)$startTimeGroup);
    }

    /**
     * @param $startTimeGroup
     */
    protected static function ensureStartTimeGroupIsValid($startTimeGroup): void
    {
        if (\is_int($startTimeGroup) === false && \is_float($startTimeGroup) === false && \is_string($startTimeGroup) === false) {
            throw new \InvalidArgumentException('This startTimeGroup is not valid: ' . $startTimeGroup);
        }
    }

    /**
     * @return int
     */
    public function getStartTimeGroup(): int
    {
        return $this->startTimeGroup;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getStartTimeGroup();
    }
}