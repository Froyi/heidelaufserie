<?php
declare(strict_types=1);

namespace Project\Module\CompetitionResults;

/**
 * Class RoundTime
 * @package Project\Module\CompetitionResults
 */
class RoundTime
{
    /** @var int $roundTime */
    protected $roundTime;

    /**
     * Round constructor.
     *
     * @param int $roundTime
     */
    protected function __construct(int $roundTime)
    {
        $this->roundTime = $roundTime;
    }

    /**
     * @param $roundTime
     *
     * @return RoundTime
     * @throws \InvalidArgumentException
     */
    public static function fromValue($roundTime): self
    {
        self::ensureRoundTimeIsValid($roundTime);

        return new self(self::convertRoundTime($roundTime));
    }

    /**
     * @param $roundTime
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureRoundTimeIsValid($roundTime): void
    {
        if ((\is_float($roundTime) === false &&  \is_int($roundTime) === false && \is_string($roundTime) === false) || (int)$roundTime < 0) {
            throw new \InvalidArgumentException('RoundTime is not valid: ' . $roundTime);
        }
    }

    /**
     * @param $roundTime
     *
     * @return int
     */
    protected static function convertRoundTime($roundTime): int
    {
        if (\is_string($roundTime) === true && strpos($roundTime, ':') !== false) {
            $roundTimeData = explode(':', $roundTime);

            return (int)$roundTimeData[0] * 3600 + (int)$roundTimeData[1] * 60 + (int)$roundTimeData[2];
        }

        return (int)$roundTime;
    }

    /**
     * @return int
     */
    public function getRoundTime(): int
    {
        return $this->roundTime;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->recreateRoundTime();
    }

    /**
     * @return string
     */
    protected function recreateRoundTime(): string
    {
        $roundTimeHour = floor($this->roundTime / 3600);
        $roundTimeMinute = floor(($this->roundTime - $roundTimeHour * 3600) / 60);
        $roundTimeSecond = $this->roundTime - $roundTimeHour * 3600 - $roundTimeMinute * 60;

        if ($roundTimeHour < 10 ) {
            $roundTimeHour = '0' . $roundTimeHour;
        }

        if ($roundTimeMinute < 10 ) {
            $roundTimeMinute = '0' . $roundTimeMinute;
        }

        if ($roundTimeSecond < 10 ) {
            $roundTimeSecond = '0' . $roundTimeSecond;
        }

        return $roundTimeHour . ':' . $roundTimeMinute . ':' . $roundTimeSecond;
    }
}