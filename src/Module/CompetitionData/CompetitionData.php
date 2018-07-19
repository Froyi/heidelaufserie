<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionData
 * @package Project\Module\Competition
 */
class CompetitionData
{
    /** @var Id $competitionDataId */
    protected $competitionDataId;

    /** @var Id $competitionId */
    protected $competitionId;

    /** @var Id $runnerId */
    protected $runnerId;

    /** @var Date $date */
    protected $date;

    /** @var StartNumber $startNumber */
    protected $startNumber;

    /** @var TransponderNumber $transponderNumber */
    protected $transponderNumber;

    /** @var Club $club */
    protected $club;

    /**
     * CompetitionData constructor.
     *
     * @param Id $competitionDataId
     * @param Id $competitionId
     * @param Id $runnerId
     * @param Date $date
     * @param StartNumber $startNumber
     * @param TransponderNumber $transponderNumber
     */
    public function __construct(Id $competitionDataId, Id $competitionId, Id $runnerId, Date $date, StartNumber $startNumber, TransponderNumber $transponderNumber)
    {
        $this->competitionDataId = $competitionDataId;
        $this->competitionId = $competitionId;
        $this->runnerId = $runnerId;
        $this->date = $date;
        $this->startNumber = $startNumber;
        $this->transponderNumber = $transponderNumber;
    }

    /**
     * @return Id
     */
    public function getCompetitionDataId(): Id
    {
        return $this->competitionDataId;
    }

    /**
     * @return Id
     */
    public function getCompetitionId(): Id
    {
        return $this->competitionId;
    }

    /**
     * @return Id
     */
    public function getRunnerId(): Id
    {
        return $this->runnerId;
    }

    /**
     * @return Date
     */
    public function getDate(): Date
    {
        return $this->date;
    }

    /**
     * @return StartNumber
     */
    public function getStartNumber(): StartNumber
    {
        return $this->startNumber;
    }

    /**
     * @return TransponderNumber
     */
    public function getTransponderNumber(): TransponderNumber
    {
        return $this->transponderNumber;
    }

    /**
     * @return Club
     */
    public function getClub(): Club
    {
        return $this->club;
    }

    /**
     * @param Club $club
     */
    public function setClub(Club $club): void
    {
        $this->club = $club;
    }
}