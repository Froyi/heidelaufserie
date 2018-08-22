<?php
declare (strict_types=1);

namespace Project\Module\CompetitionResults;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Id;

class CompetitionResultsRepository extends DefaultRepository
{
    protected const TABLE = 'competitionResults';

    public function getCompetitionResultsByCompetitionDataId(Id $competitionDataId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('competitionDataId', '=', $competitionDataId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Id $competitionResultsId
     *
     * @return mixed
     */
    public function getCompetitionResultByCompetitionResultsId(Id $competitionResultsId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('competitionResultsId', '=', $competitionResultsId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param CompetitionResults $competitionResults
     *
     * @return bool
     */
    public function saveCompetitionResults(CompetitionResults $competitionResults): bool
    {
        if (empty($this->getCompetitionResultsByCompetitionDataId($competitionResults->getCompetitionDataId())) === false) {
            return false;
        }

        $query = $this->database->getNewInsertQuery(self::TABLE);
        $query->insert('competitionResultsId', $competitionResults->getCompetitionResultsId()->toString());
        $query->insert('competitionDataId', $competitionResults->getCompetitionDataId()->toString());
        $query->insert('runnerId', $competitionResults->getRunnerId()->toString());

        if ($competitionResults->getTimeOverall() !== null) {
            $query->insert('timeOverall', $competitionResults->getTimeOverall()->getTimeOverall());
        }

        if ($competitionResults->getPoints() !== null) {
            $query->insert('points', $competitionResults->getPoints()->getPoints());
        }

        if ($competitionResults->getFirstRound() !== null) {
            $query->insert('firstRound', $competitionResults->getFirstRound()->getRoundTime());
        }

        if ($competitionResults->getSecondRound() !== null) {
            $query->insert('secondRound', $competitionResults->getSecondRound()->getRoundTime());
        }

        if ($competitionResults->getThirdRound() !== null) {
            $query->insert('thirdRound', $competitionResults->getThirdRound()->getRoundTime());
        }

        return $this->database->execute($query);
    }

    /**
     * @param Id $runnerId
     * @return mixed
     */
    public function getCompetitionResultsByRunnerId(Id $runnerId): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('runnerId', '=', $runnerId->toString());

        return $this->database->fetchAll($query);
    }


}