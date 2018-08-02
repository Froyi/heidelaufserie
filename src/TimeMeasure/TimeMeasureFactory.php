<?php
declare(strict_types=1);

namespace Project\TimeMeasure;

use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;

/**
 * Class TimeMeasureFactory
 * @package Project\TimeMeasure
 */
class TimeMeasureFactory
{
    /**
     * @param $object
     *
     * @return null|TimeMeasure
     */
    public function getTimeMeasureByObject($object): ?TimeMeasure
    {
        try {
            if (empty($object->timeMeasureId) === true) {
                $timeMeasureId = Id::generateId();
            } else {
                $timeMeasureId = Id::fromString($object->timeMeasureId);
            }

            $transponderNumber = TransponderNumber::fromValue($object->transponderNumber);
            /** @var Datetime $timestamp */
            $timestamp = Datetime::fromValue($object->timestamp);

            $shown = false;
            if (isset($object->shown)) {
                $shown = (bool)$object->shown;
            }

            return new TimeMeasure($timeMeasureId, $transponderNumber, $timestamp, $shown);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return null|TimeMeasure
     */
    public function generateTimeMeasureByData(CompetitionData $competitionData): ?TimeMeasure
    {
        $timeMeasureId = Id::generateId();
        $transponderNumber = $competitionData->getTransponderNumber();
        /** @var Datetime $timestamp */
        $timestamp = Datetime::fromValue(date('Y-m-d H:i:s'));
        $shown = false;

        return new TimeMeasure($timeMeasureId, $transponderNumber, $timestamp, $shown);
    }
}