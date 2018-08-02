<?php
declare(strict_types=1);

namespace Project\Module\CompetitionResults;

/**
 * Class TimeOverall
 * @package Project\Module\CompetitionResults
 */
class TimeOverall
{
    /** @var int $timeOverall */
    protected $timeOverall;

    /**
     * Round constructor.
     *
     * @param int $timeOverall
     */
    protected function __construct(int $timeOverall)
    {
        $this->timeOverall = $timeOverall;
    }

    /**
     * @param $timeOverall
     *
     * @return TimeOverall
     * @throws \InvalidArgumentException
     */
    public static function fromValue($timeOverall): self
    {
        self::ensureTimeOverallIsValid($timeOverall);

        return new self(self::convertTimeOverall($timeOverall));
    }

    /**
     * @param $timeOverall
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureTimeOverallIsValid($timeOverall): void
    {
        if ((\is_int($timeOverall) === false && \is_string($timeOverall) === false) || (int)$timeOverall < 0) {
            throw new \InvalidArgumentException('TimeOverall is not valid: ' . $timeOverall);
        }
    }

    /**
     * @param $timeOverall
     *
     * @return int
     */
    protected static function convertTimeOverall($timeOverall): int
    {
        return (int)$timeOverall;
    }

    /**
     * @return int
     */
    public function getTimeOverall(): int
    {
        return $this->timeOverall;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTimeOverall();
    }
}