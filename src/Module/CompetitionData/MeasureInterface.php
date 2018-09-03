<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;


use Project\Module\GenericValueObject\Datetime;

/**
 * Interface MeasureInterface
 * @package Project\Module\CompetitionData
 */
interface MeasureInterface
{
    /**
     * @return TransponderNumber
     */
    public function getTransponderNumber(): TransponderNumber;

    /**
     * @return Datetime
     */
    public function getTimestamp(): Datetime;
}