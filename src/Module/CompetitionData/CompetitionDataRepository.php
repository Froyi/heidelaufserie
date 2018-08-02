<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Date;
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
                $query->insert('club', $competitionData->getClub()->getClub());
            }

            return $this->database->execute($query);
        }

        return false;
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
     *
     * @return array
     */
    public function getSpeakerCompetitionDataByCompetitionDate(Date $date): array
    {
        $query = /** @lang text */
            'SELECT * FROM competitionData, timeMeasure WHERE competitionData.transponderNumber = timeMeasure.transponderNumber AND timeMeasure.shown = 0 AND competitionData.date = "' . $date->toString() . '"';

        return $this->database->fetchAllQueryString($query);
    }
}