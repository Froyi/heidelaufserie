<?php
declare(strict_types=1);

/**
 * FinishMeasureService.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       02.09.2018
 */

namespace Project\Module\FinishMeasure;

use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\Database\Database;

/**
 * Class FinishMeasureService
 * @package Project\Module\FinishMeasure
 */
class FinishMeasureService
{
    /** @var FinishMeasureFactory $finishMeasureFactory */
    protected $finishMeasureFactory;

    /** @var FinishMeasureRepository $finishMeasureRepository */
    protected $finishMeasureRepository;

    /**
     * Service constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->finishMeasureFactory = new FinishMeasureFactory();
        $this->finishMeasureRepository = new FinishMeasureRepository($database);
    }

    /**
     * @param TransponderNumber $transponderNumber
     *
     * @return array
     */
    public function getFinishMeasureByTransponderNumber(TransponderNumber $transponderNumber): array
    {
        $finishMeasureArray = [];
        $finishMeasureData = $this->finishMeasureRepository->getFinishMeasureByTransponderNumber($transponderNumber);

        if (empty($finishMeasureData) === true) {
            return $finishMeasureArray;
        }

        foreach ($finishMeasureData as $finishMeasureSingleData) {
            $finishMeasure = $this->finishMeasureFactory->getFinishMeasureByObject($finishMeasureSingleData);

            if ($finishMeasure !== null) {
                $finishMeasureArray[$finishMeasure->getFinishMeasureId()->toString()] = $finishMeasure;
            }
        }

        return $finishMeasureArray;
    }
}