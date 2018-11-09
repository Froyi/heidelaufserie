<?php
declare (strict_types=1);

namespace Project\Module\CompetitionResults;

use phpDocumentor\Reflection\Types\Self_;
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
     * @param array $competitionResultsArray
     *
     * @return bool
     */
    public function saveAllCompetitionResults(array $competitionResultsArray): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($competitionResultsArray as $competitionResults) {
                $this->saveCompetitionResults($competitionResults);
            }
            $this->database->commit();
        } catch (\Exception $exception) {
            $this->database->rollBack();

            return false;
        }

        return true;
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
     * @param CompetitionResults $competitionResults
     *
     * @return bool
     */
    public function updateCompetitionResults(CompetitionResults $competitionResults): bool
    {
        $query = $this->database->getNewUpdateQuery(self::TABLE);
        $query->set('competitionDataId', $competitionResults->getCompetitionDataId()->toString());
        $query->set('runnerId', $competitionResults->getRunnerId()->toString());

        if ($competitionResults->getTimeOverall() !== null) {
            $query->set('timeOverall', $competitionResults->getTimeOverall()->getTimeOverall());
        } else {
            $query->set('timeOverall', null);
        }

        if ($competitionResults->getPoints() !== null) {
            $query->set('points', $competitionResults->getPoints()->getPoints());
        } else {
            $query->set('points', null);
        }

        if ($competitionResults->getFirstRound() !== null) {
            $query->set('firstRound', $competitionResults->getFirstRound()->getRoundTime());
        } else {
            $query->set('firstRound', null);
        }

        if ($competitionResults->getSecondRound() !== null) {
            $query->set('secondRound', $competitionResults->getSecondRound()->getRoundTime());
        } else {
            $query->set('secondRound', null);
        }

        if ($competitionResults->getThirdRound() !== null) {
            $query->set('thirdRound', $competitionResults->getThirdRound()->getRoundTime());
        } else {
            $query->set('thirdRound', null);
        }

        $query->where('competitionResultsId', '=', $competitionResults->getCompetitionResultsId()->toString());

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

    /**
     * @param array $competitionResultsArray
     *
     * @return bool
     */
    public function updateAllCompetitionResults(array $competitionResultsArray): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($competitionResultsArray as $competitionResults) {
                if ($this->updateCompetitionResults($competitionResults) === false) {
                    throw new \Exception('This update failed. Revert all!');
                }
            }

            $this->database->commit();
        } catch (\Exception $exception) {
            $this->database->rollBack();

            return false;
        }

        return true;
    }

    /**
     * @param Id $competitionDataId
     *
     * @return bool
     */
    public function deleteCompetitionResultsByCompetitionDataId(Id $competitionDataId): bool
    {
        $query = $this->database->getNewDeleteQuery(self::TABLE);

        $query->where('competitionDataId', '=', $competitionDataId->toString());

        return $this->database->execute($query);
    }

    /**
     * @param Id $competitionResultsId
     *
     * @return bool
     */
    public function deleteCompetitionResultsByCompetitionResultsId(Id $competitionResultsId): bool
    {
        $query = $this->database->getNewDeleteQuery(self::TABLE);

        $query->where('competitionResultsId', '=', $competitionResultsId->toString());

        return $this->database->execute($query);
    }
}