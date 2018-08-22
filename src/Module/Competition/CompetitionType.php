<?php
declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\CompetitionResults\Round;
use Project\Module\GenericValueObject\Distance;
use Project\Module\GenericValueObject\Name;

/**
 * Class CompetitionType
 * @package Project\Module\Competition
 */
class CompetitionType
{
    /** @var CompetitionTypeId $competitionTypeId */
    protected $competitionTypeId;

    /** @var Name $competitionName */
    protected $competitionName;

    /** @var Distance $distance */
    protected $distance;

    /** @var Round $rounds */
    protected $rounds;

    /** @var bool $standardSet */
    protected $standardSet;

    /** @var StartTimeGroup $startTimeGroup */
    protected $startTimeGroup;

    /**
     * CompetitionType constructor.
     *
     * @param CompetitionTypeId $competitionTypeId
     * @param Name $competitionName
     * @param Distance $distance
     * @param Round $rounds
     * @param bool $standardSet
     * @param StartTimeGroup $startTimeGroup
     */
    public function __construct(CompetitionTypeId $competitionTypeId, Name $competitionName, Distance $distance, Round $rounds, bool $standardSet, StartTimeGroup $startTimeGroup)
    {
        $this->competitionTypeId = $competitionTypeId;
        $this->competitionName = $competitionName;
        $this->distance = $distance;
        $this->rounds = $rounds;
        $this->standardSet = $standardSet;
        $this->startTimeGroup = $startTimeGroup;
    }

    /**
     * @return CompetitionTypeId
     */
    public function getCompetitionTypeId(): CompetitionTypeId
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

    /**
     * @return StartTimeGroup
     */
    public function getStartTimeGroup(): StartTimeGroup
    {
        return $this->startTimeGroup;
    }
}