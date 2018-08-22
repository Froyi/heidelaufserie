<?php
declare(strict_types=1);

namespace Project\Module\Competition;

/**
 * Class CompetitionTypeId
 * @package Project\Module\Competition
 */
class CompetitionTypeId
{
    /** @var int $competitionTypeId */
    protected $competitionTypeId;

    /**
     * CompetitionTypeId constructor.
     *
     * @param int $competitionTypeId
     */
    protected function __construct(int $competitionTypeId)
    {
        $this->competitionTypeId = $competitionTypeId;
    }

    /**
     * @param $competitionTypeId
     */
    protected static function ensureCompetitionTypeIdIsValid($competitionTypeId): void
    {
        if (\is_int($competitionTypeId) === false && \is_string($competitionTypeId) === false) {
            throw new \InvalidArgumentException('This competitionTypeId is not valid: ' . $competitionTypeId);
        }

        if ((int)$competitionTypeId < 0) {
            throw new \InvalidArgumentException('This competitionTypeId is lower than minimum value: ' . $competitionTypeId);
        }
    }

    /**
     * @param $competitionTypeId
     *
     * @return CompetitionTypeId
     */
    public static function fromValue($competitionTypeId): self
    {
        self::ensureCompetitionTypeIdIsValid($competitionTypeId);

        return new self((int)$competitionTypeId);
    }

    /**
     * @return int
     */
    public function getCompetitionTypeId(): int
    {
        return $this->competitionTypeId;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getCompetitionTypeId();
    }
}