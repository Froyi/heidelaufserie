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

    /** @var  RoundTime $firstRound */
    protected $firstRound;

    /** @var  RoundTime $secondRound */
    protected $secondRound;

    /** @var  RoundTime $thirdRound */
    protected $thirdRound;

    /**
     * CompetitionResults constructor.
     * @param Id $competitionResultsId
     * @param Id $competitionDataId
     * @param Id $runnerId
     */
    public function __construct(Id $competitionResultsId, Id $competitionDataId, Id $runnerId)
    {
        parent::__construct();

        $this->competitionResultsId = $competitionResultsId;
        $this->competitionDataId = $competitionDataId;
        $this->runnerId = $runnerId;
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
     * @return null|TimeOverall
     */
    public function getTimeOverall(): ?TimeOverall
    {
        return $this->timeOverall;
    }

    /**
     * @param TimeOverall $timeOverall
     */
    public function setTimeOverall(TimeOverall $timeOverall): void
    {
        $this->timeOverall = $timeOverall;
    }

    /**
     * @return null|Points
     */
    public function getPoints(): ?Points
    {
        return $this->points;
    }

    /**
     * @param Points $points
     */
    public function setPoints(Points $points): void
    {
        $this->points = $points;
    }

    /**
     * @return null|RoundTime
     */
    public function getFirstRound(): ?RoundTime
    {
        return $this->firstRound;
    }

    /**
     * @param RoundTime $firstRound
     */
    public function setFirstRound(RoundTime $firstRound): void
    {
        $this->firstRound = $firstRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getSecondRound(): ?RoundTime
    {
        return $this->secondRound;
    }

    /**
     * @param RoundTime $secondRound
     */
    public function setSecondRound(RoundTime $secondRound): void
    {
        $this->secondRound = $secondRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getThirdRound(): ?RoundTime
    {
        return $this->thirdRound;
    }

    /**
     * @param RoundTime $thirdRound
     */
    public function setThirdRound(RoundTime $thirdRound): void
    {
        $this->thirdRound = $thirdRound;
    }

    /**
     * @return null|Round
     */
    public function getRoundsRun(): ?Round
    {
        if ($this->getThirdRound() !== null) {
            return Round::fromValue(3);
        }

        if ($this->getSecondRound() !== null) {
            return Round::fromValue(2);
        }

        if ($this->getFirstRound() !== null) {
            return Round::fromValue(1);
        }

        return null;
    }

    /**
     * @param Id $runnerId
     */
    public function setRunnerId(Id $runnerId)
    {
        $this->runnerId = $runnerId;
    }
}