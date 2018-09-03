<?php
declare(strict_types=1);

/**
 * FinishMeasureFactory.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       02.09.2018
 */

namespace Project\Module\FinishMeasure;

use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;

/**
 * Class FinishMeasureFactory
 * @package Project\Module\FinishMeasure
 */
class FinishMeasureFactory
{
    /**
     * @param $object
     *
     * @return null|FinishMeasure
     */
    public function getFinishMeasureByObject($object): ?FinishMeasure
    {
        try {
            if (empty($object->finishMeasureId) === true) {
                $finishMeasureId = Id::generateId();
            } else {
                $finishMeasureId = Id::fromString($object->finishMeasureId);
            }

            $transponderNumber = TransponderNumber::fromValue($object->transponderNumber);
            /** @var Datetime $timestamp */
            $timestamp = Datetime::fromValue($object->timestamp);

            return new FinishMeasure($finishMeasureId, $transponderNumber, $timestamp);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }
}