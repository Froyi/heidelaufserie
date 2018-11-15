<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

use Project\Module\CompetitionResults\Points;
use Project\Module\CompetitionResults\RoundTime;
use Project\Module\CompetitionResults\TimeOverall;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;
use Project\Module\Runner\Runner;

/**
 * Class CompetitionStatistic
 * @package Project\Module\CompetitionStatistic
 */
class CompetitionStatistic
{
    /** @var int RANKING_POINTS_AMOUNT */
    public const RANKING_POINTS_AMOUNT = 5;

    /** @var Id $competitionStatisticId
     */
    protected $competitionStatisticId;

    /** @var Id $runnerId */
    protected $runnerId;

    /** @var null|Runner $runner */
    protected $runner;

    /** @var Year */
    protected $year;

    /** @var CompetitionCount $competitionCount */
    protected $competitionCount;

    /** @var null|Points $totalPoints */
    protected $totalPoints;

    /** @var null|Points $averagePoints */
    protected $averagePoints;

    /** @var null|Points $rankingPoints */
    protected $rankingPoints;

    /** @var null|TimeOverall $bestTimeOverall */
    protected $bestTimeOverall;

    /** @var null|TimeOverall $averageTimeOverall */
    protected $averageTimeOverall;

    /** @var null|RoundTime $bestFirstRound */
    protected $bestFirstRound;

    /** @var null|RoundTime $averageFirstRound */
    protected $averageFirstRound;

    /** @var null|RoundTime $bestSecondRound */
    protected $bestSecondRound;

    /** @var null|RoundTime $averageSecondRound */
    protected $averageSecondRound;

    /** @var null|RoundTime $bestThirdRound */
    protected $bestThirdRound;

    /** @var null|RoundTime $averageThirdRound */
    protected $averageThirdRound;

    /** @var null|Ranking $ranking */
    protected $ranking;

    /** @var null|Ranking $akRanking */
    protected $akRanking;

    /**
     * CompetitionStatistic constructor.
     *
     * @param Id $competitionStatisticId
     * @param Id $runnerId
     * @param Year $year
     * @param CompetitionCount $competitionCount
     */
    public function __construct(Id $competitionStatisticId, Id $runnerId, Year $year, CompetitionCount $competitionCount)
    {
        $this->competitionStatisticId = $competitionStatisticId;
        $this->runnerId = $runnerId;
        $this->year = $year;
        $this->competitionCount = $competitionCount;
    }

    /**
     * @return null|Runner
     */
    public function getRunner(): ?Runner
    {
        return $this->runner;
    }

    /**
     * @param null|Runner $runner
     */
    public function setRunner(?Runner $runner): void
    {
        $this->runner = $runner;
    }

    /**
     * @return Id
     */
    public function getCompetitionStatisticId(): Id
    {
        return $this->competitionStatisticId;
    }

    /**
     * @return Id
     */
    public function getRunnerId(): Id
    {
        return $this->runnerId;
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return CompetitionCount
     */
    public function getCompetitionCount(): CompetitionCount
    {
        return $this->competitionCount;
    }

    /**
     * @return null|Points
     */
    public function getTotalPoints(): ?Points
    {
        return $this->totalPoints;
    }

    /**
     * @param null|Points $totalPoints
     */
    public function setTotalPoints(?Points $totalPoints): void
    {
        $this->totalPoints = $totalPoints;
    }

    /**
     * @return null|Points
     */
    public function getAveragePoints(): ?Points
    {
        return $this->averagePoints;
    }

    /**
     * @param null|Points $averagePoints
     */
    public function setAveragePoints(?Points $averagePoints): void
    {
        $this->averagePoints = $averagePoints;
    }

    /**
     * @return null|Points
     */
    public function getRankingPoints(): ?Points
    {
        return $this->rankingPoints;
    }

    /**
     * @param null|Points $rankingPoints
     */
    public function setRankingPoints(?Points $rankingPoints): void
    {
        $this->rankingPoints = $rankingPoints;
    }

    /**
     * @return null|TimeOverall
     */
    public function getBestTimeOverall(): ?TimeOverall
    {
        return $this->bestTimeOverall;
    }

    /**
     * @param null|TimeOverall $bestTimeOverall
     */
    public function setBestTimeOverall(?TimeOverall $bestTimeOverall): void
    {
        $this->bestTimeOverall = $bestTimeOverall;
    }

    /**
     * @return null|TimeOverall
     */
    public function getAverageTimeOverall(): ?TimeOverall
    {
        return $this->averageTimeOverall;
    }

    /**
     * @param null|TimeOverall $averageTimeOverall
     */
    public function setAverageTimeOverall(?TimeOverall $averageTimeOverall): void
    {
        $this->averageTimeOverall = $averageTimeOverall;
    }

    /**
     * @return null|RoundTime
     */
    public function getBestFirstRound(): ?RoundTime
    {
        return $this->bestFirstRound;
    }

    /**
     * @param null|RoundTime $bestFirstRound
     */
    public function setBestFirstRound(?RoundTime $bestFirstRound): void
    {
        $this->bestFirstRound = $bestFirstRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getAverageFirstRound(): ?RoundTime
    {
        return $this->averageFirstRound;
    }

    /**
     * @param null|RoundTime $averageFirstRound
     */
    public function setAverageFirstRound(?RoundTime $averageFirstRound): void
    {
        $this->averageFirstRound = $averageFirstRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getBestSecondRound(): ?RoundTime
    {
        return $this->bestSecondRound;
    }

    /**
     * @param null|RoundTime $bestSecondRound
     */
    public function setBestSecondRound(?RoundTime $bestSecondRound): void
    {
        $this->bestSecondRound = $bestSecondRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getAverageSecondRound(): ?RoundTime
    {
        return $this->averageSecondRound;
    }

    /**
     * @param null|RoundTime $averageSecondRound
     */
    public function setAverageSecondRound(?RoundTime $averageSecondRound): void
    {
        $this->averageSecondRound = $averageSecondRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getBestThirdRound(): ?RoundTime
    {
        return $this->bestThirdRound;
    }

    /**
     * @param null|RoundTime $bestThirdRound
     */
    public function setBestThirdRound(?RoundTime $bestThirdRound): void
    {
        $this->bestThirdRound = $bestThirdRound;
    }

    /**
     * @return null|RoundTime
     */
    public function getAverageThirdRound(): ?RoundTime
    {
        return $this->averageThirdRound;
    }

    /**
     * @param null|RoundTime $averageThirdRound
     */
    public function setAverageThirdRound(?RoundTime $averageThirdRound): void
    {
        $this->averageThirdRound = $averageThirdRound;
    }

    /**
     * @return null|Ranking
     */
    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    /**
     * @param Ranking $ranking
     */
    public function setRanking(Ranking $ranking): void
    {
        $this->ranking = $ranking;
    }

    /**
     * @return null|Ranking
     */
    public function getAkRanking(): ?Ranking
    {
        return $this->akRanking;
    }

    /**
     * @param null|Ranking $akRanking
     */
    public function setAkRanking(?Ranking $akRanking): void
    {
        $this->akRanking = $akRanking;
    }

    /**
     * @param Id $runnerId
     */
    public function setRunnerId(Id $runnerId): void
    {
        $this->runnerId = $runnerId;
    }


}