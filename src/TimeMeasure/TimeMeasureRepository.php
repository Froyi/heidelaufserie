<?php
declare(strict_types=1);

namespace Project\TimeMeasure;

use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\Database\Query;
use Project\Module\DefaultRepository;

/**
 * Class TimeMeasureRepository
 * @package Project\TimeMeasure
 */
class TimeMeasureRepository extends DefaultRepository
{
    /** @var string TABLE */
    protected const TABLE = 'timeMeasure';

    /**
     * @param TransponderNumber $transponderNumber
     *
     * @return array
     */
    public function getTimeMeasureByTransponderNumber(TransponderNumber $transponderNumber): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('transponderNumber', '=', $transponderNumber->getTransponderNumber());
        $query->orderBy('timestamp', Query::ASC);

        return $this->database->fetchAll($query);
    }

    /**
     * @return array
     */
    public function getNewTimeMeasures(): array
    {
        /*$queryString = 'SELECT DISTINCT transponderNumber FROM timemeasure WHERE shown = 0';
        return $this->database->fetchAllQueryString($queryString);*/

        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('shown', '=', false);

        return $this->database->fetchAll($query);
    }

    /**
     * @param TimeMeasure $timeMeasure
     *
     * @return bool
     */
    public function saveTimeMeasure(TimeMeasure $timeMeasure): bool
    {
        $query = $this->database->getNewInsertQuery(self::TABLE);
        $query->insert('timeMeasureId', $timeMeasure->getTimeMeasureId()->toString());
        $query->insert('transponderNumber', $timeMeasure->getTransponderNumber()->getTransponderNumber());
        $query->insert('timestamp', $timeMeasure->getTimestamp()->toString());
        $query->insert('shown', $timeMeasure->isShown());

        return $this->database->execute($query);
    }

    /**
     * @param array $allCompetitionData
     * @return bool
     */
    public function markAllTimeMeasureListsAsShown(array $allCompetitionData): bool
    {
        $this->database->beginTransaction();
        try {
            /** @var CompetitionData $competitionData */
            foreach ($allCompetitionData as $competitionData) {
                $timeMeasureList = $competitionData->getTimeMeasureList();
                /** @var TimeMeasure $timeMeasure */
                foreach ($timeMeasureList as $timeMeasure) {
                    $timeMeasure->setShown(true);
                    $this->updateTimeMeasure($timeMeasure);
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
     * @param TimeMeasure $timeMeasure
     *
     * @return bool
     */
    public function updateTimeMeasure(TimeMeasure $timeMeasure): bool
    {
        $query = $this->database->getNewUpdateQuery(self::TABLE);
        $query->set('transponderNumber', $timeMeasure->getTransponderNumber()->getTransponderNumber());
        $query->set('timestamp', $timeMeasure->getTimestamp()->toString());
        $query->set('shown', $timeMeasure->isShown());

        $query->where('timeMeasureId', '=', $timeMeasure->getTimeMeasureId()->toString());

        return $this->database->execute($query);
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        return $this->database->truncateTable(self::TABLE);
    }

    /**
     * @return int
     */
    public function countAll(): int
    {
        return $this->database->count(self::TABLE);
    }
}