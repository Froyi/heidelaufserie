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
}