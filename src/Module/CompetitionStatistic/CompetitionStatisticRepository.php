<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;

/**
 * Class CompetitionStatisticRepository
 * @package Project\Module\CompetitionStatistic
 */
class CompetitionStatisticRepository extends DefaultRepository
{
    /** @var string TABLE */
    protected const TABLE = 'competitionStatistic';

    /**
     * @param Year $year
     *
     * @return array
     */
    public function getStatisticsByYear(Year $year): array
    {
        $query = /** @lang text */
            "SELECT *
             FROM competitiondata CD, competitionresults CR
             WHERE CD.date LIKE '" . $year->getYear() . "%'
             AND CD.competitionDataId = CR.competitionDataId
             ORDER BY CR.points DESC";

        return $this->database->fetchAllQueryString($query);
    }

    /**
     * @param Year $year
     * @param Id $runnerId
     *
     * @return mixed
     */
    public function getCompetitionStatisticByYearAndRunnerId(Year $year, Id $runnerId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('runnerId', '=', $runnerId->toString());
        $query->andWhere('year', '=', $year->getYear());

        return $this->database->fetch($query);
    }

    /**
     * @param array $competitionStatistics
     *
     * @return bool
     */
    public function saveAllCompetitionStatistics(array $competitionStatistics): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($competitionStatistics as $competitionStatistic) {
                $this->saveCompetitionStatistic($competitionStatistic);
            }

            $this->database->commit();
        } catch (\Exception $exception) {
            $this->database->rollback();

            return false;
        }

        return true;
    }

    /**
     * @param Year $year
     *
     * @return bool
     */
    public function deleteOldStatisticsByYear(Year $year): bool
    {
        $query = $this->database->getNewDeleteQuery(self::TABLE);
        $query->where('year', '=', $year->getYear());

        return $this->database->execute($query);
    }

    /**
     * @param CompetitionStatistic $competitionStatistic
     *
     * @return bool
     */
    public function saveCompetitionStatistic(CompetitionStatistic $competitionStatistic): bool
    {
        if (empty($this->getCompetitionStatisticByYearAndRunnerId($competitionStatistic->getYear(), $competitionStatistic->getRunnerId())) === true) {
            $query = $this->database->getNewInsertQuery(self::TABLE);
            $query->insert('competitionStatisticId', $competitionStatistic->getCompetitionStatisticId()->toString());
            $query->insert('runnerId', $competitionStatistic->getRunnerId()->toString());
            $query->insert('year', $competitionStatistic->getYear()->getYear());
            $query->insert('competitionCount', $competitionStatistic->getCompetitionCount()->getCompetitionCount());

            if ($competitionStatistic->getTotalPoints() !== null) {
                $query->insert('totalPoints', $competitionStatistic->getTotalPoints()->getPoints());
            }

            if ($competitionStatistic->getAveragePoints() !== null) {
                $query->insert('averagePoints', $competitionStatistic->getAveragePoints()->getPoints());
            }

            if ($competitionStatistic->getRankingPoints() !== null) {
                $query->insert('rankingPoints', $competitionStatistic->getRankingPoints()->getPoints());
            }

            if ($competitionStatistic->getBestTimeOverall() !== null) {
                $query->insert('bestTimeOverall', $competitionStatistic->getBestTimeOverall()->getTimeOverall());
            }

            if ($competitionStatistic->getAverageTimeOverall() !== null) {
                $query->insert('averageTimeOverall', $competitionStatistic->getAverageTimeOverall()->getTimeOverall());
            }

            if ($competitionStatistic->getBestFirstRound() !== null) {
                $query->insert('bestFirstRound', $competitionStatistic->getBestFirstRound()->getRoundTime());
            }

            if ($competitionStatistic->getAverageFirstRound() !== null) {
                $query->insert('averageFirstRound', $competitionStatistic->getAverageFirstRound()->getRoundTime());
            }

            if ($competitionStatistic->getBestSecondRound() !== null) {
                $query->insert('bestSecondRound', $competitionStatistic->getBestSecondRound()->getRoundTime());
            }

            if ($competitionStatistic->getAverageSecondRound() !== null) {
                $query->insert('averageSecondRound', $competitionStatistic->getAverageSecondRound()->getRoundTime());
            }

            if ($competitionStatistic->getBestThirdRound() !== null) {
                $query->insert('bestThirdRound', $competitionStatistic->getBestThirdRound()->getRoundTime());
            }

            if ($competitionStatistic->getAverageThirdRound() !== null) {
                $query->insert('averageThirdRound', $competitionStatistic->getAverageThirdRound()->getRoundTime());
            }

            if ($competitionStatistic->getRanking() !== null) {
                $query->insert('ranking', $competitionStatistic->getRanking()->getRanking());
            }

            if ($competitionStatistic->getAkRanking() !== null) {
                $query->insert('akRanking', $competitionStatistic->getAkRanking()->getRanking());
            }

            return $this->database->execute($query);
        }

        return true;
    }
}