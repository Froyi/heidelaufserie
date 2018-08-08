<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

/**
 * Class CompetitionCount
 * @package Project\Module\CompetitionStatistic
 */
class CompetitionCount
{
    /** @var int $competitionCount */
    protected $competitionCount;

    /**
     * CompetitionCount constructor.
     *
     * @param int $competitionCount
     */
    protected function __construct(int $competitionCount)
    {
        $this->competitionCount = $competitionCount;
    }

    /**
     * @param $competitionCount
     *
     * @return CompetitionCount
     * @throws \InvalidArgumentException
     */
    public static function fromValue($competitionCount): self
    {
        self::ensureCompetitionCountIsValid($competitionCount);

        return new self(self::convertCompetitionCount($competitionCount));
    }

    /**
     * @param $competitionCount
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureCompetitionCountIsValid($competitionCount): void
    {
        if ((\is_int($competitionCount) === false && \is_string($competitionCount) === false) || (int)$competitionCount < 0) {
            throw new \InvalidArgumentException('CompetitionCount is not valid: ' . $competitionCount);
        }
    }

    /**
     * @param $competitionCount
     *
     * @return int
     */
    protected static function convertCompetitionCount($competitionCount): int
    {
        return (int)$competitionCount;
    }

    /**
     * @return int
     */
    public function getCompetitionCount(): int
    {
        return $this->competitionCount;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getCompetitionCount();
    }
}