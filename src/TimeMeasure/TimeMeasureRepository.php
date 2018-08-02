<?php
declare(strict_types=1);

namespace Project\TimeMeasure;

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
}