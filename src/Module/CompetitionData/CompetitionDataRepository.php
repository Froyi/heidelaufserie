<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Competition\CompetitionTypeId;
use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Gender;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionDataRepository
 * @package Project\Module\CompetitionData
 */
class CompetitionDataRepository extends DefaultRepository
{
    protected const TABLE = 'competitionData';

    /**
     * @param CompetitionData $competitionData
     *
     * @return bool
     */
    public function saveCompetitionData(CompetitionData $competitionData): bool
    {
        if (empty($this->getCompetitionDataByCompetitionDataId($competitionData->getCompetitionDataId()) === true)) {
            $query = $this->database->getNewInsertQuery(self::TABLE);
            $query->insert('competitionDataId', $competitionData->getCompetitionDataId()->toString());
            $query->insert('competitionId', $competitionData->getCompetitionId()->toString());
            $query->insert('runnerId', $competitionData->getRunnerId()->toString());
            $query->insert('startNumber', $competitionData->getStartNumber()->getStartNumber());
            $query->insert('date', $competitionData->getDate()->toString());
            $query->insert('transponderNumber', $competitionData->getTransponderNumber()->getTransponderNumber());
            if ($competitionData->getClub() !== null) {
                $query->insert('clubId', $competitionData->getClub()->getClubId()->toString());
            }

            return $this->database->execute($query);
        }

        return false;
    }

    /**
     * @param array $allCompetitionData
     * @return bool
     */
    public function saveAllCompetitionData(array $allCompetitionData): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($allCompetitionData as $competitionData) {
                $this->saveCompetitionData($competitionData);
            }

            $this->database->commit();
        } catch (\Exception $exception) {
            $this->database->rollBack();

            return false;
        }

        return true;
    }

    /**
     * @param array $allCompetitionData
     *
     * @return bool
     */
    public function updateAllCompetitionData(array $allCompetitionData): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($allCompetitionData as $competitionData) {
                if ($this->updateCompetitionData($competitionData) === false) {
                    throw new \RuntimeException('This update failed. Revert all!');
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
     * @param CompetitionData $competitionData
     *
     * @return bool
     */
    public function updateCompetitionData(CompetitionData $competitionData): bool
    {
        $query = $this->database->getNewUpdateQuery(self::TABLE);
        $query->set('competitionId', $competitionData->getCompetitionId()->toString());
        $query->set('runnerId', $competitionData->getRunnerId()->toString());
        $query->set('startNumber', $competitionData->getStartNumber()->getStartNumber());
        $query->set('date', $competitionData->getDate()->toString());
        $query->set('transponderNumber', $competitionData->getTransponderNumber()->getTransponderNumber());

        if ($competitionData->getClub() !== null) {
            $query->set('clubId', $competitionData->getClub()->getClubId()->toString());
        } else {
            $query->set('clubId', null);
        }

        $query->where('competitionDataId', '=', $competitionData->getCompetitionDataId()->toString());

        return $this->database->execute($query);
    }

    /**
     * @param Id $competitionDataId
     *
     * @return mixed
     */
    public function getCompetitionDataByCompetitionDataId(Id $competitionDataId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('competitionDataId', '=', $competitionDataId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Id $runnerId
     *
     * @return array
     */
    public function getCompetitionDataByRunnerId(Id $runnerId): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('runnerId', '=', $runnerId->toString());

        return $this->database->fetchAll($query);
    }

    /**
     * @param Date $date
     *
     * @return array
     */
    public function getCompetitionDataByDate(Date $date): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('date', '=', $date->toString());

        return $this->database->fetchAll($query);
    }

    /**
     * @param Date $date
     * @param StartNumber $startNumber
     *
     * @return mixed
     */
    public function getCompetitionDataByDateAndStartNumber(Date $date, StartNumber $startNumber)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('date', '=', $date->toString());
        $query->andWhere('startNumber', '=', $startNumber->getStartNumber());

        return $this->database->fetch($query);
    }

    /**
     * @param Date $date
     *
     * @return array
     */
    public function getSpeakerCompetitionDataByCompetitionDate(Date $date): array
    {
        $query = /** @lang text */
            'SELECT * FROM competitionData, timeMeasure WHERE competitionData.transponderNumber = timeMeasure.transponderNumber AND timeMeasure.shown = 0 AND competitionData.date = "' . $date->toString() . '" ORDER BY timeMeasure.timestamp DESC';

        return $this->database->fetchAllQueryString($query);
    }

    /**
     * @param Date $date
     * @param Gender $gender
     * @param CompetitionTypeId $competitionTypeId
     *
     * @return array
     */
    public function getSpeakerRankingUpdateData(Date $date, Gender $gender, CompetitionTypeId $competitionTypeId): array
    {
        $query = /** @lang text */
            'SELECT DISTINCT (competitionDataId)
            FROM competitiondata CD,
                 timemeasure TM,
                 competition C,
                 runner R
            WHERE CD.transponderNumber = TM.transponderNumber
              AND CD.competitionId = C.competitionId
              AND CD.runnerId = R.runnerId
              AND CD.date = "' . $date->toString() . '"
              AND C.competitionTypeId = ' . $competitionTypeId->getCompetitionTypeId() . '
              AND R.gender = "' . $gender->getGender() . '"';

        return $this->database->fetchAllQueryString($query);
    }

    /**
     * @return array
     */
    public function getAllCompetitionData(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        return $this->database->fetchAll($query);
    }

    /**
     * @param Date $date
     *
     * @return bool
     */
    public function deleteCompetitionDataByDate(Date $date): bool
    {
        $query = $this->database->getNewDeleteQuery(self::TABLE);

        $query->where('date', '=', $date->toString());

        return $this->database->execute($query);
    }

    /**
     * @param Date $date
     * @param int $limit
     *
     * @return array
     */
    public function getRandomCompetitionDataByDate(Date $date, int $limit): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        $query->where('date', '=', $date->toString());

        if ($limit !== null) {
            $query->limit($limit);
        }
        
        $query->orderRandom();

        return $this->database->fetchAll($query);
    }

    /**
     * @param Id $clubId
     *
     * @return array
     */
    public function getCompetitionDataByClubId(Id $clubId): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        $query->where('clubId', '=', $clubId->toString());

        return $this->database->fetchAll($query);
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return mixed
     */
    public function competitionDataExist(CompetitionData $competitionData)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        $query->where('startNumber', '=', $competitionData->getStartNumber()->getStartNumber());
        $query->andWhere('date', '=', $competitionData->getDate()->toString());

        return $this->database->fetch($query);
    }
}