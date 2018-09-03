<?php
declare(strict_types=1);

/**
 * FinishMeasureRepository.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       02.09.2018
 */

namespace Project\Module\FinishMeasure;

use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\Database\Query;
use Project\Module\DefaultRepository;

/**
 * Class FinishMeasureRepository
 * @package Project\Module\FinishMeasure
 */
class FinishMeasureRepository extends DefaultRepository
{
    /** @var string TABLE */
    protected const TABLE = 'finishMeasure';

    /**
     * @param TransponderNumber $transponderNumber
     *
     * @return array
     */
    public function getFinishMeasureByTransponderNumber(TransponderNumber $transponderNumber): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('transponderNumber', '=', $transponderNumber->getTransponderNumber());
        $query->orderBy('timestamp', Query::ASC);

        return $this->database->fetchAll($query);
    }

    /**
     * @param array $finishMeasureArray
     *
     * @return bool
     */
    public function saveAllFinishMeasures(array $finishMeasureArray): bool
    {
        $this->database->beginTransaction();

        try {
            /** @var FinishMeasure $finishMeasure */
            foreach ($finishMeasureArray as $finishMeasure) {
                if ($this->saveFinishMeasure($finishMeasure) === false) {
                    $this->database->rollBack();
                    return false;
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
     * @param FinishMeasure $finishMeasure
     *
     * @return bool
     */
    protected function saveFinishMeasure(FinishMeasure $finishMeasure): bool
    {
        $query = $this->database->getNewInsertQuery(self::TABLE);
        $query->insert('finishMeasureId', $finishMeasure->getFinishMeasureId()->toString());
        $query->insert('transponderNumber', $finishMeasure->getTransponderNumber()->getTransponderNumber());
        $query->insert('timestamp', $finishMeasure->getTimestamp()->toString());

        return $this->database->execute($query);
    }
}