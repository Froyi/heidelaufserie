<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

class Points
{
    /**
     *
     */
    protected const ROUND_PRECISION = 2;

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
        $result = (float)$points;
        return round($result, self::ROUND_PRECISION);
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
}