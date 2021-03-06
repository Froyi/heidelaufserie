<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Club\Club;
use Project\Module\Competition\Competition;
use Project\Module\CompetitionResults\Round;
use Project\Module\CompetitionStatistic\CompetitionStatistic;
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

    /** @var array $finishMeasureList */
    protected $finishMeasureList = [];

    /** @var null|CompetitionStatistic $competitionStatistic */
    protected $competitionStatistic;

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
     * @param Id $runnerId
     */
    public function setRunnerId(Id $runnerId): void
    {
        $this->runnerId = $runnerId;
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
     * @param bool $isFinishMeasure
     *
     * @return array
     */
    public function getRoundTimes($isFinishMeasure = false): array
    {
        $roundNumber = 1;
        $rounds = [];
        $lastRoundTime = null;

        if ($this->competition !== null) {
            /** @var Datetime $startingTime */
            $startingTime = $this->competition->getStartTime();
        } else {
            /** @var Datetime $startingTime */
            $startingTime = Datetime::fromValue($this->configuration->getEntryByName('startingTime'));
        }

        // chose which list should be taken
        $measureList = $this->timeMeasureList;
        if ($isFinishMeasure === true) {
            $measureList = $this->finishMeasureList;
        }

        usort($measureList, [$this, 'sortByTimestamp']);

        /** @var TimeMeasure $timeMeasure */
        foreach ($measureList as $timeMeasure) {
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

            $roundsTimes['timeMeasure'] = $timeMeasure;

            $rounds[$roundNumber] = $roundsTimes;

            $lastRoundTime = $roundTime;
            $roundNumber++;
        }

        return $rounds;
    }

    /**
     * @return int
     */
    public function getLastRoundTime(): ?int
    {
        $rounds = $this->getRoundTimes();
        $lastKey = end($rounds);

        if ($lastKey === false) {
            return null;
        }

        return $lastKey['round'];
    }

    /**
     * @param bool $isFinishMeasure
     *
     * @return int|null
     */
    public function getLastTimeOverall($isFinishMeasure = false): ?int
    {
        $rounds = $this->getRoundTimes($isFinishMeasure);
        $lastKey = end($rounds);

        if ($lastKey === false) {
            return null;
        }

        return $lastKey['timeOverall'];
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
     * @return bool
     */
    public function isRunValid(): bool
    {
        $actualFinishedRounds = \count($this->finishMeasureList);

        return $actualFinishedRounds === $this->competition->getCompetitionType()->getRounds()->getRound();
    }

    /**
     * @param MeasureInterface $timeMeasure1
     * @param MeasureInterface $timeMeasure2
     *
     * @return int
     */
    public function sortByTimestamp(MeasureInterface $timeMeasure1, MeasureInterface $timeMeasure2): int
    {
        if ($timeMeasure1->getTimestamp()->toString() === $timeMeasure2->getTimestamp()->toString()) {
            return 0;
        }

        return ($timeMeasure1->getTimestamp()->toString() < $timeMeasure2->getTimestamp()->toString()) ? -1 : 1;
    }

    /**
     * @return null|CompetitionStatistic
     */
    public function getCompetitionStatistic(): ?CompetitionStatistic
    {
        return $this->competitionStatistic;
    }

    /**
     * @param null|CompetitionStatistic $competitionStatistic
     */
    public function setCompetitionStatistic(?CompetitionStatistic $competitionStatistic): void
    {
        $this->competitionStatistic = $competitionStatistic;
    }

    /**
     * @return array
     */
    public function getFinishMeasureList(): array
    {
        return $this->finishMeasureList;
    }

    /**
     * @param array $finishMeasureList
     */
    public function setFinishMeasureList(array $finishMeasureList): void
    {
        $this->finishMeasureList = $finishMeasureList;
    }

    /**
     * @param TimeMeasure $timeMeasure
     *
     * @return bool
     */
    public function removeUnplausibleTimeMeasure(TimeMeasure $timeMeasure): bool
    {
        if (isset($this->timeMeasureList[$timeMeasure->getTimeMeasureId()->toString()]) === true) {
            unset($this->timeMeasureList[$timeMeasure->getTimeMeasureId()->toString()]);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @todo test it
     */
    public function isUnusualSlowLastRound(): bool
    {
        $lastRoundTime = $this->getLastRoundTime();
        $actualRound = $this->getActualRound();

        // Wenn es keine (letzte) Runde gibt, kann nicht bestimmt werden, ob es sich um eine langsame Runde handelt
        if ($lastRoundTime === null) {
            return false;
        }

        if ($this->getCompetitionStatistic() !== null) {
            if ($actualRound === 1 && $this->getCompetitionStatistic()->getAverageFirstRound() !== null) {
                return ($lastRoundTime > ($this->getCompetitionStatistic()->getAverageFirstRound()->getRoundTime() * Round::UNUSUAL_SLOW));
            }

            if ($actualRound === 2 && $this->getCompetitionStatistic()->getAverageSecondRound() !== null) {
                return ($lastRoundTime > ($this->getCompetitionStatistic()->getAverageSecondRound()->getRoundTime() * Round::UNUSUAL_SLOW));
            }

            if ($actualRound === 3 && $this->getCompetitionStatistic()->getAverageThirdRound() !== null) {
                return ($lastRoundTime > ($this->getCompetitionStatistic()->getAverageThirdRound()->getRoundTime() * Round::UNUSUAL_SLOW));
            }
        }

        // Es muss eine allgemeine Lösung her, wenn es sich um die erste Runde handelt und es keine Referenzzeit aus dem letzten Jahr gibt
        if ($actualRound === 1) {
            return ($lastRoundTime > Round::SLOW_TIME);
        }

        $averageRound = 0;
        foreach ($this->getRoundTimes() as $roundNumber => $roundTime) {
            if ($roundNumber === $actualRound) {
                break;
            }

            $averageRound = ($averageRound + $roundTime['round']) / $roundNumber;
        }

        return ($lastRoundTime >= ($averageRound * Round::UNUSUAL_SLOW));
    }
}