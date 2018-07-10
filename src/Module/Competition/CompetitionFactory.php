<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;


/**
 * Class CompetitionFactory
 * @package Project\Module\Competition
 */
class CompetitionFactory
{
    /**
     * @param $object
     *
     * @return null|Competition
     */
    public function getCompetitionByObject($object): ?Competition
    {
        try {
            if ($this->checkProperties($object) === false) {
                return null;
            }

            if (empty($object->competitionId) === true) {
                $competitionId = Id::generateId();
            } else {
                $competitionId = Id::fromString($object->competitionId);
            }

            /** @var Date $date */
            $date = Date::fromValue($object->date);
            $competitionNumber = (int) $object->competitionNumber;

            return new Competition($competitionId, $date, $competitionNumber);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    protected function checkProperties($properties): bool
    {
        return !(empty($properties->competitionNumber) === true || empty($properties->date) === true);
    }
}