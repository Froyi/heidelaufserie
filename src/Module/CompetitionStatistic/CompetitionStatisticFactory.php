<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

use Project\Module\CompetitionResults\Points;
use Project\Module\CompetitionResults\RoundTime;
use Project\Module\CompetitionResults\TimeOverall;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;

/**
 * Class CompetitionStatisticFactory
 * @package Project\Module\CompetitionStatistic
 */
class CompetitionStatisticFactory
{
    public function getStatisticsByGeneratedData(array $generatedData): ?CompetitionStatistic
    {
        $data = (object)$generatedData;
        try {
            $competitionStatisticId = Id::generateId();
            $runnerId = Id::fromString($data->runnerId);
            $year = Year::fromValue($data->year);
            $competitionCount = CompetitionCount::fromValue(\count($data->points));

            $competitionStatistic = new CompetitionStatistic($competitionStatisticId, $runnerId, $year, $competitionCount);

            if (!empty($data->points)) {
                try {
                    $competitionStatistic->setTotalPoints(Points::fromValue(array_sum($data->points)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setAveragePoints(Points::fromValue(array_sum($data->points) / \count($data->points)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setRankingPoints($this->getRankingPoints($data->points));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }
            }

            if (!empty($data->timeOverall)) {
                try {
                    $competitionStatistic->setBestTimeOverall(TimeOverall::fromValue(min($data->timeOverall)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setAverageTimeOverall(TimeOverall::fromValue(floor(array_sum($data->timeOverall) / \count($data->timeOverall))));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }
            }

            if (!empty($data->firstRound)) {
                try {
                    $competitionStatistic->setBestFirstRound(RoundTime::fromValue(min($data->firstRound)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setAverageFirstRound(RoundTime::fromValue(floor(array_sum($data->firstRound) / \count($data->firstRound))));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }
            }

            if (!empty($data->secondRound)) {
                try {
                    $competitionStatistic->setBestSecondRound(RoundTime::fromValue(min($data->secondRound)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setAverageSecondRound(RoundTime::fromValue(floor(array_sum($data->secondRound) / \count($data->secondRound))));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }
            }

            if (!empty($data->thirdRound)) {
                try {
                    $competitionStatistic->setBestThirdRound(RoundTime::fromValue(min($data->thirdRound)));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }

                try {
                    $competitionStatistic->setAverageThirdRound(RoundTime::fromValue(floor(array_sum($data->thirdRound) / \count($data->thirdRound))));
                } catch (\InvalidArgumentException $exception) {
                    // do nothing
                }
            }
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        return $competitionStatistic;
    }

    /**
     * @param $object
     *
     * @return null|CompetitionStatistic
     */
    public function getCompetitionStatisticByObject($object): ?CompetitionStatistic
    {
        try {
            if (empty($object->competitionStatisticId) === true) {
                $competitionStatisticId = Id::generateId();
            } else {
                $competitionStatisticId = Id::fromString($object->competitionStatisticId);
            }

            $runnerId = Id::fromString($object->runnerId);
            $year = Year::fromValue($object->year);
            $competitionCount = CompetitionCount::fromValue($object->competitionCount);

            $competitionStatistic = new CompetitionStatistic($competitionStatisticId, $runnerId, $year, $competitionCount);

            if (empty($object->totalPoints) === false) {
                $competitionStatistic->setTotalPoints(Points::fromValue($object->totalPoints));
            }

            if (empty($object->averagePoints) === false) {
                $competitionStatistic->setAveragePoints(Points::fromValue($object->averagePoints));
            }

            if (empty($object->rankingPoints) === false) {
                $competitionStatistic->setRankingPoints(Points::fromValue($object->rankingPoints));
            }

            if (empty($object->bestTimeOverall) === false) {
                $competitionStatistic->setBestTimeOverall(TimeOverall::fromValue($object->bestTimeOverall));
            }

            if (empty($object->averageTimeOverall) === false) {
                $competitionStatistic->setAverageTimeOverall(TimeOverall::fromValue($object->averageTimeOverall));
            }

            if (empty($object->bestFirstRound) === false) {
                $competitionStatistic->setBestFirstRound(RoundTime::fromValue($object->bestFirstRound));
            }

            if (empty($object->averageFirstRound) === false) {
                $competitionStatistic->setAverageFirstRound(RoundTime::fromValue($object->averageFirstRound));
            }

            if (empty($object->bestSecondRound) === false) {
                $competitionStatistic->setBestSecondRound(RoundTime::fromValue($object->bestSecondRound));
            }

            if (empty($object->averageSecondRound) === false) {
                $competitionStatistic->setAverageSecondRound(RoundTime::fromValue($object->averageSecondRound));
            }

            if (empty($object->bestThirdRound) === false) {
                $competitionStatistic->setBestThirdRound(RoundTime::fromValue($object->bestThirdRound));
            }

            if (empty($object->averageThirdRound) === false) {
                $competitionStatistic->setAverageThirdRound(RoundTime::fromValue($object->averageThirdRound));
            }

            if (empty($object->ranking) === false) {
                $competitionStatistic->setRanking(Ranking::fromValue($object->ranking));
            }

            if (empty($object->akRanking) === false) {
                $competitionStatistic->setAkRanking(Ranking::fromValue($object->akRanking));
            }

            return $competitionStatistic;
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param array $points
     *
     * @return null|Points
     */
    protected function getRankingPoints(array $points): ?Points
    {
        $count = 0;
        $rankingPoints = 0;
        rsort($points);

        foreach ($points as $point) {
            if ($count >= CompetitionStatistic::RANKING_POINTS_AMOUNT) {
                break;
            }

            $rankingPoints += $point;
            $count++;
        }

        try {
            return Points::fromValue($rankingPoints);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }
}