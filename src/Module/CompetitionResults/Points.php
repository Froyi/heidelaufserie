<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

use Project\Configuration;
use Project\Module\Competition\CompetitionTypeId;
use Project\Module\GenericValueObject\DefaultGenericValueObject;

class Points extends DefaultGenericValueObject
{
    /**
     *
     */
    public const ROUND_PRECISION = 2;

    /** @var  float $points */
    protected $points;

    /**
     * Points constructor.
     * @param float $points
     */
    protected function __construct(float $points)
    {
        $this->points = $points;
    }

    /**
     * @param $points
     * @return Points
     */
    public static function fromValue($points): self
    {
        self::ensurePointsIsValid($points);
        return new self(self::convertPoints($points));
    }

    /**
     * @param $points
     */
    protected static function ensurePointsIsValid($points): void
    {
        if ((\is_float($points) === false && \is_string($points) === false && \is_int($points) === false) || (float)$points < 0) {
            throw new \InvalidArgumentException('Points is not valid: ' . $points);
        }
    }

    /**
     * @param $points
     * @return float
     */
    protected static function convertPoints($points): float
    {
        $value = str_replace(',', '.', $points);

        return round($value, self::ROUND_PRECISION);
    }

    /**
     * @return Configuration
     */
    protected static function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @return float
     */
    public function getPoints(): float
    {
        return $this->points;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getPoints();
    }

    /**
     * @param TimeOverall $timeOverall
     * @param Round $rounds
     * @param CompetitionTypeId $competitionTypeId
     *
     * @return null|Points
     */
    public static function fromTimeAndRounds(TimeOverall $timeOverall, Round $rounds, CompetitionTypeId $competitionTypeId): ?self
    {
        if ($rounds->getRound() === 0 || $timeOverall->getTimeOverall() === null) {
            return null;
        }
        $competitionTime = self::getConfiguration()->getEntryByName('competitionTime');

        $defaultTime = $competitionTime[$competitionTypeId->getCompetitionTypeId()];

        if ($rounds->getRound() !== $defaultTime['rounds']) {
            return null;
        }

        return self::fromValue($defaultTime['time'] / $timeOverall->getTimeOverall() * 100);
    }
}