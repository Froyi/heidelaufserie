<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Competition\Competition;
use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;
use Project\Module\Runner\Runner;
use Project\TimeMeasure\TimeMeasure;

/**
 * Class CompetitionData
 * @package Project\Module\Competition
 */
class CompetitionData extends DefaultModel
{
    /** @var Id $competitionDataId */
    protected $competitionDataId;

    /** @var Id $competitionId */
    protected $competitionId;

    /** @var Competition $competition */
    protected $competition;

    /** @var Id $runnerId */
    protected $runnerId;

    /** @var Runner $runner */
    protected $runner;

    /** @var Date $date */
    protected $date;

    /** @var StartNumber $startNumber */
    protected $startNumber;

    /** @var TransponderNumber $transponderNumber */
    protected $transponderNumber;

    /** @var Club $club */
    protected $club;

    /** @var array $timeMeasureList */
    protected $timeMeasureList = [];

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
        parent::__construct();

        $this->competitionDataId = $competitionDataId;
        $this->competitionId = $competitionId;
        $this->runnerId = $runnerId;
        $this->date = $date;
        $this->startNumber = $startNumber;
        $this->transponderNumber = $transponderNumber;
    }

    /**
     * @return array
     */
    public function getTimeMeasureList(): array
    {
        return $this->timeMeasureList;
    }

    /**
     * @param array $timeMeasureList
     */
    public function setTimeMeasureList(array $timeMeasureList): void
    {
        $this->timeMeasureList = $timeMeasureList;
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
    public function getClub(): ?Club
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

    /**
     * @return Runner
     */
    public function getRunner(): ?Runner
    {
        return $this->runner;
    }

    /**
     * @param Runner $runner
     */
    public function setRunner(?Runner $runner): void
    {
        $this->runner = $runner;
    }

    /**
     * @return Competition
     */
    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    /**
     * @param Competition $competition
     */
    public function setCompetition(?Competition $competition): void
    {
        $this->competition = $competition;
    }

    /**
     * @return array
     */
    public function getRoundTimes(): array
    {
        $rounds = [];
        $lastRoundTime = null;

        /** @var Datetime $startingTime */
        $startingTime = Datetime::fromValue($this->configuration->getEntryByName('startingTime'));

        usort($this->timeMeasureList, [$this, 'sortByTimestamp']);

        /** @var TimeMeasure $timeMeasure */
        foreach ($this->timeMeasureList as $timeMeasure) {
            $roundTime = $timeMeasure->getTimestamp();
            if ($lastRoundTime === null) {
                $lastRoundTime = $startingTime;
            }
            $roundTimeDiff = $roundTime->getDifference($lastRoundTime);
            $roundsTimes['round'] = $roundTimeDiff;

            if ($lastRoundTime->toString() === $startingTime->toString()) {
                $roundsTimes['timeOverall'] = $roundTimeDiff;
            } else {
                $roundsTimes['timeOverall'] = $roundTime->getDifference($startingTime);
            }

            $rounds[] = $roundsTimes;

            $lastRoundTime = $roundTime;
        }

        return $rounds;
    }

    /**
     * @return bool
     */
    public function isLastRound(): bool
    {
        return $this->getActualRound() === $this->competition->getCompetitionType()->getRounds()->getRound();
    }

    /**
     * @return int
     */
    public function getActualRound(): int
    {
        return \count($this->timeMeasureList);
    }

    /**
     * @return bool
     */
    public function hasMoreRounds(): bool
    {
        return $this->getActualRound() > $this->competition->getCompetitionType()->getRounds()->getRound();
    }

    /**
     * @param TimeMeasure $timeMeasure1
     * @param TimeMeasure $timeMeasure2
     *
     * @return int
     */
    public function sortByTimestamp(TimeMeasure $timeMeasure1, TimeMeasure $timeMeasure2): int
    {
        if ($timeMeasure1->getTimestamp()->toString() === $timeMeasure2->getTimestamp()->toString()) {
            return 0;
        }

        return ($timeMeasure1->getTimestamp()->toString() < $timeMeasure2->getTimestamp()->toString()) ? -1 : 1;
    }
}