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
        if ((\is_float($timeOverall) === false && \is_int($timeOverall) === false && \is_string($timeOverall) === false) || (int)$timeOverall < 0 || $timeOverall === 'DNF') {
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
        if (\is_string($timeOverall) === true && strpos($timeOverall, ':') !== false) {
            $timeOverAllData = explode(':', $timeOverall);

            return (int)$timeOverAllData[0] * 3600 + (int)$timeOverAllData[1] * 60 + (int)$timeOverAllData[2];
        }

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
        return $this->recreateTimeOverall();
    }

    /**
     * @return string
     */
    protected function recreateTimeOverall(): string
    {
        $timeOverallHour = floor($this->timeOverall / 3600);
        $timeOverallMinute = floor(($this->timeOverall - $timeOverallHour * 3600) / 60);
        $timeOverallSecond = $this->timeOverall - $timeOverallHour * 3600 - $timeOverallMinute * 60;

        if ($timeOverallHour < 10 ) {
            $timeOverallHour = '0' . $timeOverallHour;
        }

        if ($timeOverallMinute < 10 ) {
            $timeOverallMinute = '0' . $timeOverallMinute;
        }

        if ($timeOverallSecond < 10 ) {
            $timeOverallSecond = '0' . $timeOverallSecond;
        }

        return $timeOverallHour . ':' . $timeOverallMinute . ':' . $timeOverallSecond;
    }
}