<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;
use Project\Module\Runner\Runner;
use Project\Module\Runner\RunnerService;

/**
 * Class CompetitionStatisticService
 * @package Project\Module\CompetitionStatistic
 */
class CompetitionStatisticService
{
    /** @var CompetitionStatisticFactory $competitionStatisticFactory */
    protected $competitionStatisticFactory;

    /** @var CompetitionStatisticRepository $competitionStatisticRepository */
    protected $competitionStatisticRepository;

    /**
     * CompetitionStatisticService constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->competitionStatisticRepository = new CompetitionStatisticRepository($database);
        $this->competitionStatisticFactory = new CompetitionStatisticFactory();
    }

    /**
     * @param Year $year
     * @param RunnerService $runnerService
     *
     * @return array
     */
    public function generateStatisticsByYear(Year $year, RunnerService $runnerService): array
    {
        $statistics = [];
        $runnerStatisticArray = [];
        $competitionStatisticData = $this->competitionStatisticRepository->getStatisticsByYear($year);

        foreach ($competitionStatisticData as $competitionStatisticSingleData) {
            try {
                $runnerId = Id::fromString($competitionStatisticSingleData->runnerId)->toString();
            } catch (\InvalidArgumentException $exception) {
                continue;
            }

            if ((float)$competitionStatisticSingleData->points === 0 || (int)$competitionStatisticSingleData->timeOverall === 0) {
                continue;
            }

            $runnerStatisticArray[$runnerId]['runnerId'] = $runnerId;

            $runnerStatisticArray[$runnerId]['year'] = $year->getYear();

            $runnerStatisticArray[$runnerId]['points'][] = (float)$competitionStatisticSingleData->points;

            $runnerStatisticArray[$runnerId]['timeOverall'][] = (int)$competitionStatisticSingleData->timeOverall;

            if ((int)$competitionStatisticSingleData->firstRound > 0) {
                $runnerStatisticArray[$runnerId]['firstRound'][] = (int)$competitionStatisticSingleData->firstRound;
            }

            if ((int)$competitionStatisticSingleData->secondRound > 0) {
                $runnerStatisticArray[$runnerId]['secondRound'][] = (int)$competitionStatisticSingleData->secondRound;
            }

            if ((int)$competitionStatisticSingleData->thirdRound > 0) {
                $runnerStatisticArray[$runnerId]['thirdRound'][] = (int)$competitionStatisticSingleData->thirdRound;
            }
        }

        foreach ($runnerStatisticArray as $runnerStatistic) {
            $statistic = $this->competitionStatisticFactory->getStatisticsByGeneratedData($runnerStatistic);

            if ($statistic !== null) {
                $runner = $runnerService->getRunnerByRunnerId($statistic->getRunnerId());

                if ($runner !== null) {
                    $statistic->setRunner($runner);
                }
                $statistics[] = $statistic;
            }
        }

        usort($statistics, [$this, 'sortByRankingPoints']);

        $rankingCounter = 1;
        /** @var CompetitionStatistic $statistic */
        foreach ($statistics as $statistic) {
            $statistic->setRanking(Ranking::fromValue($rankingCounter));
            $rankingCounter++;
        }

        usort($statistics, [$this, 'sortByAk']);

        $rankingCounter = 0;
        /** @var Runner $lastRunner */
        $lastRunner = null;
        /** @var CompetitionStatistic $statistic */
        foreach ($statistics as $statistic) {
            if ($lastRunner === null || ($statistic->getRunner() !== null && $lastRunner->getAgeGroup()->getAgeGroup() === $statistic->getRunner()->getAgeGroup()->getAgeGroup())) {
                $rankingCounter++;
            } else {
                $rankingCounter = 1;
            }
            $statistic->setAkRanking(Ranking::fromValue($rankingCounter));
            $lastRunner = $statistic->getRunner();
        }

        return $statistics;
    }

    /**
     * @param Id $runnerId
     * @param Year $year
     *
     * @return null|CompetitionStatistic
     */
    public function getCompetitionStatisticByRunnerIdAndYear(Id $runnerId, Year $year): ?CompetitionStatistic
    {
        $competitionStatisticData = $this->competitionStatisticRepository->getCompetitionStatisticByYearAndRunnerId($year, $runnerId);

        if (empty($competitionStatisticData) === true) {
            return null;
        }

        return $this->competitionStatisticFactory->getCompetitionStatisticByObject($competitionStatisticData);
    }

    /**
     * @param CompetitionStatistic $competitionStatistic
     *
     * @return bool
     */
    public function saveCompetitionStatistic(CompetitionStatistic $competitionStatistic): bool
    {
        return $this->competitionStatisticRepository->saveCompetitionStatistic($competitionStatistic);
    }

    /**
     * @param array $competitionStatistics
     *
     * @return bool
     */
    public function saveAllCompetitionStatistic(array $competitionStatistics): bool
    {
        return $this->competitionStatisticRepository->saveAllCompetitionStatistics($competitionStatistics);
    }

    /**
     * @param Year $year
     *
     * @return bool
     */
    public function deleteOldStatisticsByYear(Year $year): bool
    {
        return $this->competitionStatisticRepository->deleteOldStatisticsByYear($year);
    }

    /**
     * @param CompetitionStatistic $competitionStatistic1
     * @param CompetitionStatistic $competitionStatistic2
     *
     * @return int
     */
    public function sortByRankingPoints(CompetitionStatistic $competitionStatistic1, CompetitionStatistic $competitionStatistic2): int
    {
        if ($competitionStatistic1->getRankingPoints() === null) {
            return 1;
        }

        if ($competitionStatistic2->getRankingPoints() === null) {
            return -1;
        }

        if ($competitionStatistic1->getRankingPoints()->getPoints() === $competitionStatistic2->getRankingPoints()->getPoints()) {
            return 0;
        }

        return ($competitionStatistic1->getRankingPoints()->getPoints() < $competitionStatistic2->getRankingPoints()->getPoints()) ? 1 : -1;
    }

    /**
     * @param CompetitionStatistic $competitionStatistic1
     * @param CompetitionStatistic $competitionStatistic2
     *
     * @return int
     */
    public function sortByAk(CompetitionStatistic $competitionStatistic1, CompetitionStatistic $competitionStatistic2): int
    {
        if ($competitionStatistic1->getRunner() === null) {
            return 1;
        }

        if ($competitionStatistic2->getRunner() === null) {
            return -1;
        }

        $gender = strnatcmp($competitionStatistic1->getRunner()->getAgeGroup()->getGender()->getGender(), $competitionStatistic2->getRunner()->getAgeGroup()->getGender()->getGender());

        if ($gender === 0) {
            $wk = strnatcmp($competitionStatistic1->getRunner()->getAgeGroup()->getAgeGroup(), $competitionStatistic2->getRunner()->getAgeGroup()->getAgeGroup());

            if ($wk === 0) {
                if ($competitionStatistic1->getRankingPoints() === null) {
                    return 1;
                }

                if ($competitionStatistic2->getRankingPoints() === null) {
                    return -1;
                }

                if ($competitionStatistic1->getRankingPoints()->getPoints() === $competitionStatistic2->getRankingPoints()->getPoints()) {
                    return 0;
                }

                return ($competitionStatistic1->getRankingPoints()->getPoints() < $competitionStatistic2->getRankingPoints()->getPoints()) ? 1 : -1;
            }

            return $wk;
        }
        return $gender;
    }
}