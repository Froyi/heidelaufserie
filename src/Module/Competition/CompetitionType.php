<?php
declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\GenericValueObject\Distance;
use Project\Module\GenericValueObject\Name;
use Project\Module\Tracking\Round\Round;

/**
 * Class CompetitionType
 * @package Project\Module\Competition
 */
class CompetitionType
{
    /** @var int $competitionTypeId */
    protected $competitionTypeId;

    /** @var Name $competitionName */
    protected $competitionName;

    /** @var Distance $distance */
    protected $distance;

    /** @var Round $rounds */
    protected $rounds;

    /** @var bool $standardSet */
    protected $standardSet;

    /**
     * CompetitionType constructor.
     *
     * @param int $competitionTypeId
     * @param Name $competitionName
     * @param Distance $distance
     * @param Round $rounds
     * @param bool $standardSet
     */
    public function __construct(int $competitionTypeId, Name $competitionName, Distance $distance, Round $rounds, bool $standardSet)
    {
        $this->competitionTypeId = $competitionTypeId;
        $this->competitionName = $competitionName;
        $this->distance = $distance;
        $this->rounds = $rounds;
        $this->standardSet = $standardSet;
    }

    /**
     * @return int
     */
    public function getCompetitionTypeId(): int
    {
        return $this->competitionTypeId;
    }

    /**
     * @return Name
     */
    public function getCompetitionName(): Name
    {
        return $this->competitionName;
    }

    /**
     * @return Distance
     */
    public function getDistance(): Distance
    {
        return $this->distance;
    }

    /**
     * @return Round
     */
    public function getRounds(): Round
    {
        return $this->rounds;
    }

    /**
     * @return bool
     */
    public function isStandardSet(): bool
    {
        return $this->standardSet;
    }
}