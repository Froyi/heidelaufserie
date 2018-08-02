<?php
declare (strict_types=1);

namespace Project\Module\CompetitionResults;

use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Id;

class CompetitionResults extends DefaultModel
{
    /** @var  Id $competitionResultsId */
    protected $competitionResultsId;

    /** @var  Id $competitionDataId */
    protected $competitionDataId;

    /** @var  Id $runnerId */
    protected $runnerId;

    /** @var  TimeOverall $timeOverall */
    protected $timeOverall;

    /** @var  Points $points */
    protected $points;

    /** @var  Round $firstRound */
    protected $firstRound;

    /** @var  Round $secondRound */
    protected $secondRound;

    /** @var  Round $thirdRound */
    protected $thirdRound;

    /**
     * CompetitionResults constructor.
     * @param Id $competitionResultsId
     * @param Id $competitionDataId
     * @param Id $runnerId
     */
    public function __construct(Id $competitionResultsId, Id $competitionDataId, Id $runnerId)
    {
        $this->competitionResultsId = $competitionResultsId;
        $this->competitionDataId = $competitionDataId;
        $this->runnerId = $runnerId;
    }

    /**
     * @param TimeOverall $timeOverall
     */
    public function setTimeOverall(TimeOverall $timeOverall)
    {
        $this->timeOverall = $timeOverall;
    }

    /**
     * @param Points $points
     */
    public function setPoints(Points $points)
    {
        $this->points = $points;
    }

    /**
     * @param Round $firstRound
     */
    public function setFirstRound(Round $firstRound)
    {
        $this->firstRound = $firstRound;
    }

    /**
     * @param Round $secondRound
     */
    public function setSecondRound(Round $secondRound)
    {
        $this->secondRound = $secondRound;
    }

    /**
     * @param Round $thirdRound
     */
    public function setThirdRound(Round $thirdRound)
    {
        $this->thirdRound = $thirdRound;
    }

    /**
     * @return Id
     */
    public function getCompetitionResultsId(): Id
    {
        return $this->competitionResultsId;
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
    public function getRunnerId(): Id
    {
        return $this->runnerId;
    }

    /**
     * @return TimeOverall
     */
    public function getTimeOverall(): TimeOverall
    {
        return $this->timeOverall;
    }

    /**
     * @return Points
     */
    public function getPoints(): Points
    {
        return $this->points;
    }

    /**
     * @return Round
     */
    public function getFirstRound(): Round
    {
        return $this->firstRound;
    }

    /**
     * @return Round
     */
    public function getSecondRound(): Round
    {
        return $this->secondRound;
    }

    /**
     * @return Round
     */
    public function getThirdRound(): Round
    {
        return $this->thirdRound;
    }


}